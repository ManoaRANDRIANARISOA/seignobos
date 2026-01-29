<?php
require 'config.php';
session_start();

// ---------------------------
// 1. Vérifier projet
// ---------------------------
$project_id = $_POST['project_id'] ?? null;
if (!$project_id) die("Aucun projet sélectionné.");

$project_id = intval($project_id);
$table_name = "project_" . $project_id;

// ---------------------------
// 2. Récupérer projet + form_data
// ---------------------------
$stmt = $pdo->prepare("SELECT form_data FROM projects WHERE id = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$project) die("Projet introuvable.");

$form_data = json_decode($project['form_data'], true) ?: [];

// ---------------------------
// 3. Créer table si nécessaire
// ---------------------------
$pdo->exec("
    CREATE TABLE IF NOT EXISTS `$table_name` (
        `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// ---------------------------
// 4. Créer colonnes dynamiques
// ---------------------------
$allowed_columns = [];
foreach ($form_data as $field) {
    $col_name = preg_replace('/[^a-zA-Z0-9_]/', '_', $field['name']);
    $allowed_columns[$field['name']] = $col_name;

    $check = $pdo->prepare("SHOW COLUMNS FROM `$table_name` LIKE ?");
    $check->execute([$col_name]);
    if (!$check->fetch()) {
        $pdo->exec("ALTER TABLE `$table_name` ADD `$col_name` TEXT NULL");
    }

    if (!empty($field['allowOther'])) {
        $other_col = $col_name . "_other";
        $checkOther = $pdo->prepare("SHOW COLUMNS FROM `$table_name` LIKE ?");
        $checkOther->execute([$other_col]);
        if (!$checkOther->fetch()) {
            $pdo->exec("ALTER TABLE `$table_name` ADD `$other_col` TEXT NULL");
        }
    }
}

// ---------------------------
// 5. Nettoyer réponses POST
// ---------------------------
$answers = [];
foreach ($_POST as $key => $value) {
    if ($key === 'project_id') continue;
    $clean_key = preg_replace('/[^a-zA-Z0-9_]/', '_', $key);
    if (is_array($value)) $value = json_encode($value, JSON_UNESCAPED_UNICODE);
    $answers[$clean_key] = $value;
}

// ---------------------------
// 6. Gérer les fichiers uploadés (avec limite et redimensionnement)
// ---------------------------
$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

foreach ($_FILES as $field_name => $file_info) {
    $clean_key = preg_replace('/[^a-zA-Z0-9_]/', '_', $field_name);

    if (!isset($file_info['tmp_name']) || !is_uploaded_file($file_info['tmp_name'])) continue;

    // Vérifier erreur
    if ($file_info['error'] !== 0) {
        error_log("Erreur upload fichier '$field_name': " . $file_info['error']);
        continue;
    }

    // Vérifier taille max (10 Mo)
    if ($file_info['size'] > 10 * 1024 * 1024) {
        error_log("Fichier trop gros: $field_name");
        continue;
    }

    // Créer nom unique
    $ext = strtolower(pathinfo($file_info['name'], PATHINFO_EXTENSION));
    $fileName = uniqid('file_') . '.' . $ext;
    $targetPath = $uploadDir . $fileName;

    // Redimensionner si image trop grande
    $maxWidth = 2000;  // largeur max
    $maxHeight = 2000; // hauteur max

    $imageTypes = ['jpg','jpeg','png','gif','webp'];
    if (in_array($ext, $imageTypes)) {
        $img = null;
        switch($ext){
            case 'jpg':
            case 'jpeg': $img = imagecreatefromjpeg($file_info['tmp_name']); break;
            case 'png': $img = imagecreatefrompng($file_info['tmp_name']); break;
            case 'gif': $img = imagecreatefromgif($file_info['tmp_name']); break;
            case 'webp': $img = imagecreatefromwebp($file_info['tmp_name']); break;
        }

        if ($img) {
            $width = imagesx($img);
            $height = imagesy($img);

            // Redimensionner si nécessaire
            if ($width > $maxWidth || $height > $maxHeight) {
                $ratio = min($maxWidth/$width, $maxHeight/$height);
                $newWidth = (int)($width * $ratio);
                $newHeight = (int)($height * $ratio);
                $tmpImg = imagecreatetruecolor($newWidth, $newHeight);

                // Gérer transparence PNG/GIF
                if(in_array($ext,['png','gif','webp'])){
                    imagecolortransparent($tmpImg, imagecolorallocatealpha($tmpImg, 0,0,0,127));
                    imagealphablending($tmpImg, false);
                    imagesavealpha($tmpImg, true);
                }

                imagecopyresampled($tmpImg, $img, 0,0,0,0, $newWidth,$newHeight, $width,$height);
                imagedestroy($img);

                // Convertir WebP en PNG pour compatibilité PHPWord
                if($ext==='webp'){
                    $targetPath = $uploadDir . uniqid('file_') . '.png';
                    imagepng($tmpImg, $targetPath);
                } elseif(in_array($ext,['jpg','jpeg'])) {
                    imagejpeg($tmpImg,$targetPath,90);
                } elseif($ext==='png') {
                    imagepng($tmpImg,$targetPath);
                } elseif($ext==='gif') {
                    imagegif($tmpImg,$targetPath);
                }
                imagedestroy($tmpImg);
            } else {
                // Pas de redimensionnement
                move_uploaded_file($file_info['tmp_name'],$targetPath);
            }
        } else {
            // Si image corrompue
            move_uploaded_file($file_info['tmp_name'],$targetPath);
        }
    } else {
        // Fichier non-image
        move_uploaded_file($file_info['tmp_name'],$targetPath);
    }

    // Ajouter colonne si nécessaire
    $check = $pdo->prepare("SHOW COLUMNS FROM `$table_name` LIKE ?");
    $check->execute([$clean_key]);
    if (!$check->fetch()) {
        $pdo->exec("ALTER TABLE `$table_name` ADD `$clean_key` TEXT NULL");
    }

    // Stocker chemin relatif
    $answers[$clean_key] = 'uploads/' . basename($targetPath);
}

// ---------------------------
// 7. INSERT sécurisé
// ---------------------------
if(!empty($answers)){
    $columns = array_map(fn($c)=>"`$c`",array_keys($answers));
    $placeholders = array_fill(0,count($answers),'?');
    $sql = "INSERT INTO `$table_name` (".implode(',',$columns).") VALUES (".implode(',',$placeholders).")";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_values($answers));
}

echo "<p style='color:green;'>✅ Formulaire enregistré !</p>";
echo "<a href='generate_form.php?id=$project_id'>← Retour</a>";
?>
