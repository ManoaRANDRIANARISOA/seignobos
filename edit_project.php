<?php
require 'config.php';
session_start();

// Vérification utilisateur connecté
if (!isset($_SESSION['user'])) exit("Accès refusé");

$project_id = $_GET['id'] ?? null;
if (!$project_id) die("Aucun projet sélectionné.");

// Récupération projet
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$project) die("Projet introuvable.");

// Décodage des champs JSON
$form_data = json_decode($project['form_data'], true);
if (!is_array($form_data)) $form_data = [];

$success = false;

// Sauvegarde si POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_name = trim($_POST['project_name'] ?? '');
    $project_description = trim($_POST['project_description'] ?? '');
    $fields = $_POST['fields'] ?? [];

    // Normalisation et options JSON
    foreach ($fields as &$f) {
        if (!empty($f['options']) && is_array($f['options'])) {
            $f['options'] = array_values(array_filter($f['options'], fn($v)=> $v !== ''));
        } else {
            $f['options'] = [];
        }
        $f['allowOther'] = !empty($f['allowOther']);
        $f['allowAI'] = !empty($f['allowAI']);
    }
    unset($f);

    $jsonFields = json_encode($fields, JSON_UNESCAPED_UNICODE);

    $stmt = $pdo->prepare("UPDATE projects SET project_name = ?, project_description = ?, form_data = ? WHERE id = ?");
    $stmt->execute([$project_name, $project_description, $jsonFields, $project_id]);

    $form_data = $fields;
    $success = true;
}

// Table dynamique
$table_name = "project_" . intval($project_id);
$existing_columns = $pdo->query("SHOW COLUMNS FROM `$table_name`")->fetchAll(PDO::FETCH_COLUMN);

// Ajouter colonnes manquantes
foreach ($form_data as $field) {
    $col_name = preg_replace('/[^a-zA-Z0-9_]/', '_', $field['name']);
    if (!in_array($col_name, $existing_columns)) {
        $pdo->exec("ALTER TABLE `$table_name` ADD `$col_name` TEXT NULL");
        $existing_columns[] = $col_name;
    }
    if (!empty($field['allowOther'])) {
        $other_col = $col_name . "_other";
        if (!in_array($other_col, $existing_columns)) {
            $pdo->exec("ALTER TABLE `$table_name` ADD `$other_col` TEXT NULL");
            $existing_columns[] = $other_col;
        }
    }
}

// Supprimer colonnes supprimées
foreach ($existing_columns as $col) {
    if ($col === 'id' || $col === 'submitted_at') continue;
    $found = false;
    foreach ($form_data as $field) {
        $name_col = preg_replace('/[^a-zA-Z0-9_]/', '_', $field['name']);
        if ($col === $name_col || $col === $name_col.'_other') $found = true;
    }
    if (!$found) $pdo->exec("ALTER TABLE `$table_name` DROP COLUMN `$col`");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Modifier <?= htmlspecialchars($project['project_name'] ?? '') ?></title>

<!-- Fonts: Poppins -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- FontAwesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
body { 
    font-family: 'Poppins', sans-serif; 
    background-color: #f9f9f9; 
    margin: 0;
    padding: 30px; 
}
.container { 
    max-width: 900px; 
    margin: auto; 
    background: #fff; 
    padding: 30px; 
    border-radius: 12px; 
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
h1 { 
    text-align: center; 
    color: #333; 
    margin-bottom: 25px;
    font-weight: 700;
}
.header-actions {
    display: flex;
    justify-content: flex-start;
    margin-bottom: 20px;
}
.back-btn {
    text-decoration: none;
    color: #555;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: 0.3s;
}
.back-btn:hover {
    color: #f5a623;
}

label {
    display: block;
    margin: 15px 0 5px;
    font-weight: 600;
    color: #444;
}
input[type="text"], textarea, select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-family: 'Poppins', sans-serif;
    box-sizing: border-box;
}
textarea { resize: vertical; min-height: 80px; }

button { 
    padding: 10px 16px; 
    margin-top: 10px; 
    cursor: pointer; 
    border: none; 
    border-radius: 8px; 
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    transition: 0.3s;
}

button[type="submit"] {
    background: linear-gradient(135deg, #f5a623, #f76b1c);
    color: #fff;
    width: 100%;
    margin-top: 20px;
    font-size: 16px;
}
button[type="submit"]:hover {
    filter: brightness(1.1);
    box-shadow: 0 4px 12px rgba(247,107,28,0.3);
}

button.remove { 
    background: #e74c3c; 
    color: #fff; 
    position: absolute; 
    top: 10px; 
    right: 10px;
    font-size: 12px;
    padding: 6px 10px;
}
button.remove:hover { background: #c0392b; }

button.add-option { 
    background: #3498db; 
    color: #fff;
    font-size: 13px;
    padding: 6px 12px;
}
button.add-option:hover { background: #2980b9; }

#add-field {
    background: #2ecc71;
    color: white;
    width: 100%;
    margin-top: 20px;
}
#add-field:hover { background: #27ae60; }

.field { 
    border: 1px solid #eee; 
    padding: 20px; 
    border-radius: 10px; 
    margin-bottom: 15px; 
    position: relative;
    background: #fafafa;
}
.options-container { 
    margin-top: 10px; 
    padding: 10px; 
    background: #fff; 
    border: 1px solid #eee;
    border-radius: 8px;
}
.option-item {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 5px;
}
.option-item button {
    background: #e74c3c;
    color: white;
    padding: 4px 8px;
    margin: 0;
    border-radius: 4px;
}
.success-msg {
    background: #d4edda;
    color: #155724;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    border: 1px solid #c3e6cb;
    text-align: center;
}
</style>
</head>
<body>
<div class="container">
    <div class="header-actions">
        <a href="dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Retour au Dashboard</a>
    </div>

    <h1>Modifier le projet</h1>
    
    <?php if($success): ?>
        <div class="success-msg"><i class="fas fa-check-circle"></i> Modifications enregistrées avec succès</div>
    <?php endif; ?>

    <form method="POST">
        <label>Nom du projet</label>
        <input type="text" name="project_name" value="<?= htmlspecialchars($project['project_name'] ?? '') ?>" required>

        <label>Description</label>
        <textarea name="project_description"><?= htmlspecialchars($project['project_description'] ?? '') ?></textarea>

        <h3><i class="fas fa-tasks"></i> Champs du formulaire</h3>
        <div id="fields">
            <?php foreach($form_data as $i => $field): ?>
            <div class="field">
                <input type="text" name="fields[<?= $i ?>][name]" value="<?= htmlspecialchars($field['name'] ?? '') ?>" placeholder="Nom du champ" required>
                <select name="fields[<?= $i ?>][type]" class="field-type">
                    <?php
                    $types = ['text','textarea','number','email','select','radio','checkbox','file','url','date'];
                    foreach($types as $type):
                        $selected = ($field['type'] ?? '') === $type ? 'selected' : '';
                    ?>
                    <option value="<?= $type ?>" <?= $selected ?>><?= $type ?></option>
                    <?php endforeach; ?>
                </select>
                <div style="margin-top: 10px;">
                    <label style="display:inline-block; margin-right: 15px;"><input type="checkbox" name="fields[<?= $i ?>][allowAI]" <?= !empty($field['allowAI'])?'checked':'' ?>> IA</label>
                    <label style="display:inline-block;"><input type="checkbox" name="fields[<?= $i ?>][allowOther]" <?= !empty($field['allowOther'])?'checked':'' ?>> Autoriser "Autre"</label>
                </div>

                <div class="options-container">
                    <?php if(!empty($field['options'])): ?>
                        <?php foreach($field['options'] as $j => $opt): ?>
                        <div class="option-item">
                            <input type="text" name="fields[<?= $i ?>][options][<?= $j ?>]" value="<?= htmlspecialchars($opt) ?>">
                            <button type="button" onclick="this.parentElement.remove()">×</button>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <button type="button" class="add-option" onclick="addOption(this)">➕ Ajouter option</button>
                </div>

                <button type="button" class="remove" onclick="this.parentElement.remove()"><i class="fas fa-trash"></i> Supprimer</button>
            </div>
            <?php endforeach; ?>
        </div>

        <button type="button" id="add-field"><i class="fas fa-plus"></i> Ajouter un champ</button>
        <button type="submit"><i class="fas fa-save"></i> Enregistrer</button>
    </form>
</div>

<script>
let fieldCount = <?= count($form_data) ?>;

document.getElementById('add-field').addEventListener('click', ()=>{
    const container = document.getElementById('fields');
    const div = document.createElement('div');
    div.className = 'field';
    div.innerHTML = `
    <input type="text" name="fields[${fieldCount}][name]" placeholder="Nom du champ" required>
    <select name="fields[${fieldCount}][type]" class="field-type">
        <option value="text">text</option>
        <option value="textarea">textarea</option>
        <option value="number">number</option>
        <option value="email">email</option>
        <option value="select">select</option>
        <option value="radio">radio</option>
        <option value="checkbox">checkbox</option>
        <option value="file">file</option>
        <option value="url">url</option>
        <option value="date">date</option>
    </select>
    <div style="margin-top: 10px;">
        <label style="display:inline-block; margin-right: 15px;"><input type="checkbox" name="fields[${fieldCount}][allowAI]"> IA</label>
        <label style="display:inline-block;"><input type="checkbox" name="fields[${fieldCount}][allowOther]"> Autoriser "Autre"</label>
    </div>
    <div class="options-container"><button type="button" class="add-option" onclick="addOption(this)">➕ Ajouter option</button></div>
    <button type="button" class="remove" onclick="this.parentElement.remove()"><i class="fas fa-trash"></i> Supprimer</button>
    `;
    container.appendChild(div);
    fieldCount++;
});

function addOption(btn){
    const container = btn.parentElement;
    const index = Array.from(container.parentElement.parentElement.children).indexOf(container.parentElement);
    const optionCount = container.querySelectorAll('input').length;
    const div = document.createElement('div');
    div.className = 'option-item';
    div.innerHTML = `<input type="text" name="fields[${index}][options][${optionCount}]"><button type="button" onclick="this.parentElement.remove()">×</button>`;
    container.insertBefore(div, btn);
}
</script>
</body>
</html>