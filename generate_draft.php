<?php
require 'config.php';
require 'vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') die("Accès direct interdit.");

// ===============================
// 1️⃣ Récupérer données du formulaire
// ===============================
$project_id = $_POST['project_id'] ?? null;
$columns_order = $_POST['columns_order'] ?? null;
$selected_rows = $_POST['selected_rows'] ?? [];
$selected_columns = $_POST['columns'] ?? [];

if (!$project_id || !$columns_order) {
    die("Données du projet manquantes.");
}

$project_id = intval($project_id);
$table_name = "project_" . $project_id;

// Vérification stricte : il faut au moins une ligne et une colonne
if (empty($selected_columns)) {
    die("Aucune colonne sélectionnée pour l'exportation.");
}

if (empty($selected_rows)) {
    die("Aucune ligne sélectionnée. Veuillez cocher les éléments à exporter dans le tableau.");
}

// Décoder l'ordre des colonnes
$columns_order = json_decode($columns_order, true);
if (!$columns_order || !is_array($columns_order)) die("Colonnes invalides.");

// Filtrer l'ordre pour ne garder que les colonnes cochées
$columns_order = array_values(array_filter($columns_order, function($col) use ($selected_columns){
    return in_array($col, $selected_columns);
}));

// Couleurs colonnes
$column_colors = [];
foreach ($columns_order as $col) {
    $column_colors[$col] = $_POST['color_'.$col] ?? '#000000';
}

// ===============================
// 2️⃣ Récupérer les lignes sélectionnées
// ===============================
$placeholders = implode(',', array_fill(0, count($selected_rows), '?'));
$sql = "SELECT * FROM `$table_name` WHERE id IN ($placeholders) ORDER BY FIELD(id,".implode(',', $selected_rows).")";
$stmt = $pdo->prepare($sql);
$stmt->execute($selected_rows);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ===============================
// 3️⃣ Initialiser PhpWord
// ===============================
$phpWord = new PhpWord();
$section = $phpWord->addSection();
$uploadDir = __DIR__ . '/uploads/';
$tempFiles = []; // fichiers WebP temporaires

// ===============================
// 4️⃣ Parcourir les lignes et colonnes
// ===============================
foreach($rows as $row){
    foreach($columns_order as $col){
        $val = $row[$col] ?? '';
        $json = $val !== '' ? json_decode($val,true) : null;

        $display = '';
        $isImage = false;
        $filePath = '';

        if(is_array($json)){
            $display = implode(', ', $json);
        }
        elseif(is_string($val) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i',$val)){
            $isImage = true;
            $filePath = __DIR__.'/'.$val;

            if(!file_exists($filePath)){
                $isImage = false;
                $display = "[Image non trouvée: $val]";
            } else {
                $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                if($ext==='webp'){
                    $img = @imagecreatefromwebp($filePath);
                    if($img){
                        $newName = uniqid('file_').'.png';
                        $newPath = $uploadDir.$newName;
                        if(imagepng($img,$newPath)){
                            $filePath = $newPath;
                            $tempFiles[] = $newPath;
                        } else {
                            $isImage = false;
                            $display = "[Erreur conversion WebP: $val]";
                        }
                        imagedestroy($img);
                    } else {
                        $isImage=false;
                        $display="[WebP corrompue: $val]";
                    }
                }
            }
        } else {
            $display = $val;
        }

        // Ajouter titre de la colonne
        $section->addText(strtoupper($col), ['color'=>str_replace('#','',$column_colors[$col]), 'bold'=>true]);

        // Ajouter contenu ou image avec couleur
        if($isImage && file_exists($filePath)){
            $section->addImage($filePath, ['width'=>400,'height'=>400,'wrappingStyle'=>'inline']);
        } else {
            $section->addText(htmlspecialchars($display), ['color'=>str_replace('#','',$column_colors[$col])]);
        }
    }
    $section->addTextBreak(1);
}

// ===============================
// 5️⃣ Générer le Word
// ===============================
$filename = "Brouillon_Project_{$project_id}.docx";
header("Content-Description: File Transfer");
header('Content-Disposition: attachment; filename="'.$filename.'"');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Expires: 0');

$objWriter = IOFactory::createWriter($phpWord,'Word2007');
$objWriter->save("php://output");

// ===============================
// 6️⃣ Supprimer fichiers temporaires
// ===============================
foreach($tempFiles as $tmp) if(file_exists($tmp)) unlink($tmp);

exit;
