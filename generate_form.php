<?php
require 'config.php';
session_start();

$project_id = $_GET['id'] ?? null;
if (!$project_id) die("Aucun projet sélectionné.");

$stmt = $pdo->prepare("SELECT project_name, form_data FROM projects WHERE id = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$project) die("Projet introuvable.");

$form_data = json_decode($project['form_data'], true);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($project['project_name']) ?> - Formulaire IA</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body { 
    font-family: 'Poppins', Arial, sans-serif; 
    background: #f0f2f5; 
    padding: 30px; 
    color: #333;
}
.container { 
    max-width: 900px; 
    margin: auto; 
    background: #fff; 
    padding: 40px; 
    border-radius: 16px; 
    box-shadow: 0 10px 25px rgba(0,0,0,0.08); 
}
.header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f2f5;
}
h1 { 
    color: #2c3e50; 
    margin: 0; 
    font-size: 24px;
    font-weight: 700;
}
.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: #fff;
    color: #555;
    border: 1px solid #ddd;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s ease;
    font-weight: 500;
}
.btn-back:hover {
    background: #f9f9f9;
    color: #333;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}
.form-group { margin-bottom: 25px; }
.form-group label { 
    font-weight: 600; 
    margin-bottom: 8px; 
    display: block; 
    color: #444;
    font-size: 15px;
}
.input-with-btn { display: flex; gap: 10px; align-items: center; }
.input-with-btn input,
.input-with-btn select,
.input-with-btn textarea { 
    flex: 1; 
    padding: 12px; 
    border-radius: 8px; 
    border: 1px solid #ddd; 
    font-size: 14px;
    transition: border-color 0.3s;
    font-family: inherit;
}
.input-with-btn input:focus,
.input-with-btn select:focus,
.input-with-btn textarea:focus {
    border-color: #f76b1c;
    outline: none;
    box-shadow: 0 0 0 3px rgba(247, 107, 28, 0.1);
}
.btn-ia { 
    padding: 0 15px; 
    height: 45px;
    background: #2c3e50; 
    color: #fff; 
    border: none; 
    border-radius: 8px; 
    cursor: pointer; 
    transition: background 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}
.btn-ia:hover { background: #1a252f; }
.ai-suggestion { 
    width: 100%; 
    margin-top: 10px; 
    font-size: 13px; 
    color: #666; 
    border-radius: 8px; 
    border: 1px solid #eee; 
    padding: 12px; 
    resize: vertical; 
    background: #f9f9f9;
    box-sizing: border-box;
}
button[type="submit"] { 
    width: 100%; 
    padding: 15px; 
    background: linear-gradient(135deg, #f5a623, #f76b1c); 
    color: #fff; 
    border: none; 
    border-radius: 50px; 
    font-size: 16px; 
    font-weight: 600;
    cursor: pointer; 
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(247, 107, 28, 0.3);
    margin-top: 10px;
}
button[type="submit"]:hover { 
    transform: translateY(-2px); 
    box-shadow: 0 6px 20px rgba(247, 107, 28, 0.4); 
    filter: brightness(1.05);
}
.checkbox-group, .radio-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
    background: #f9f9f9;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #eee;
}
.checkbox-group label, .radio-group label {
    font-weight: normal;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
}
.other-input { 
    display:none; 
    margin-top: 10px; 
    width: 100%;
    box-sizing: border-box;
}
</style>
</head>
<body>
<div class="container">
    <div class="header-row">
        <h1><?= htmlspecialchars($project['project_name']) ?></h1>
        <a href="ongoing_projects_page.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

<form method="POST" action="submit_form.php" enctype="multipart/form-data">
<input type="hidden" name="project_id" value="<?= $project_id ?>">
<?php foreach($form_data as $field):
    $name = htmlspecialchars($field['name'] ?? '');
    $type = $field['type'] ?? 'text';
    $options = $field['options'] ?? [];
    $allowAI = $field['allowAI'] ?? false;
?>
<div class="form-group">
    <label for="<?= $name ?>"><?= $name ?></label>
    <div class="input-with-btn">
        <?php if ($type === 'text' || $type === 'number' || $type === 'file' || $type === 'url'): ?>
            <input type="<?= $type ?>" id="<?= $name ?>" name="<?= $name ?>">
        <?php elseif($type === 'textarea'): ?>
            <textarea id="<?= $name ?>" name="<?= $name ?>" rows="4"></textarea>
        <?php elseif($type === 'select'): ?>
            <select id="<?= $name ?>" name="<?= $name ?>" class="select-other" data-name="<?= $name ?>">
                <option value="">-- Sélectionnez --</option>
                <?php foreach($options as $opt): ?>
                    <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="<?= $name ?>_other" class="other-input" placeholder="Veuillez préciser...">
        <?php elseif($type === 'checkbox'): ?>
            <div class="checkbox-group">
                <?php foreach($options as $opt): ?>
                    <label><input type="checkbox" name="<?= $name ?>[]" value="<?= htmlspecialchars($opt) ?>"> <?= htmlspecialchars($opt) ?></label>
                <?php endforeach; ?>
            </div>
        <?php elseif($type === 'radio'): ?>
            <div class="radio-group">
                <?php foreach($options as $opt): ?>
                    <label><input type="radio" name="<?= $name ?>" value="<?= htmlspecialchars($opt) ?>"> <?= htmlspecialchars($opt) ?></label>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if($allowAI): ?>
            <button type="button" class="btn-ia" onclick="getAISuggestion('<?= $name ?>')">💡</button>
        <?php endif; ?>
    </div>

    <?php if($allowAI): ?>
        <textarea id="suggestion_<?= $name ?>" class="ai-suggestion" readonly placeholder="Suggestion IA..."></textarea>
    <?php endif; ?>
</div>
<?php endforeach; ?>

<button type="submit">Envoyer</button>
</form>
</div>

<script>
document.querySelectorAll('.select-other').forEach(select => {
    select.addEventListener('change', function() {
        const otherInput = this.nextElementSibling;
        if(this.value === 'Autre') otherInput.style.display = 'block';
        else otherInput.style.display = 'none';
    });
});

async function getAISuggestion(fieldId){
    const el = document.getElementById(fieldId);
    if(!el) return;
    let value = '';
    if(el.type==='file') value = el.files && el.files.length ? el.files[0].name : '';
    else if(el.tagName==='SELECT') value = el.options[el.selectedIndex].text;
    else value = el.value;

    const suggestionBox = document.getElementById('suggestion_'+fieldId);
    if(!suggestionBox) return;
    suggestionBox.value = '⏳ Chargement de la suggestion...';

    try{
        const resp = await fetch('/get_ai_suggestion',{
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({field:fieldId,value:value})
        });
        if(!resp.ok) throw new Error('HTTP '+resp.status);
        const data = await resp.json();
        suggestionBox.value = data.suggestion || 'Aucune suggestion disponible.';
    }catch(err){
        console.error(err);
        suggestionBox.value = '⚠️ Erreur lors de la récupération de la suggestion.';
    }
}
</script>
</body>
</html>
