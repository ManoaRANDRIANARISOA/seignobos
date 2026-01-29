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
<style>
body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 30px; }
.container { max-width: 900px; margin: auto; background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
h1 { color: #022b0c; margin-bottom: 25px; text-align:center; }
.form-group { margin-bottom: 20px; }
.form-group label { font-weight: bold; margin-bottom: 5px; display: block; }
.input-with-btn { display: flex; gap: 10px; align-items: center; }
.input-with-btn input,
.input-with-btn select,
.input-with-btn textarea { flex: 1; padding: 10px; border-radius: 6px; border: 1px solid #ccc; }
.btn-ia { padding: 8px 12px; background: #022b0c; color: #fff; border: none; border-radius: 6px; cursor: pointer; }
.ai-suggestion { width: 100%; margin-top: 5px; font-size: 13px; color: #555; border-radius:6px; border:1px solid #ccc; padding: 8px; resize: none; }
button[type="submit"] { width: 100%; padding: 12px; background: #f76b1c; color: #fff; border: none; border-radius: 8px; font-size: 16px; cursor: pointer; }
.form-row { display: flex; gap: 20px; }
.form-row .half { flex: 1; }
.other-input { display:none; margin-top:5px; }
</style>
</head>
<body>
<div class="container">
<h1><?= htmlspecialchars($project['project_name']) ?> — Assistance IA</h1>

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
