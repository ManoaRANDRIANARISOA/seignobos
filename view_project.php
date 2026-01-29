<?php
require 'config.php';

/* ===============================
   1. ID PROJET
=============================== */
$project_id = intval($_GET['project_id'] ?? $_GET['id'] ?? $_POST['project_id'] ?? 0);
if ($project_id <= 0) exit('Projet invalide');

$table_name = "project_" . $project_id;

/* ===============================
   2. INFOS PROJET
=============================== */
$stmt = $pdo->prepare("SELECT project_name, project_description, created_at FROM projects WHERE id=?");
$stmt->execute([$project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$project) exit('Projet introuvable');

/* ===============================
   3. COLONNES
=============================== */
$cols = $pdo->query("SHOW COLUMNS FROM `$table_name`")->fetchAll(PDO::FETCH_COLUMN);
if (!$cols) exit('Aucune donnée');

/* ===============================
   4. TRI
=============================== */
$order_by = $_GET['order_by'] ?? '';
$order_dir = ($_GET['order_dir'] ?? 'ASC') === 'ASC' ? 'ASC' : 'DESC';
if ($order_by && !in_array($order_by, $cols)) $order_by = '';

$order_sql = 'id';
if ($order_by) {
    $order_sql = "
        CASE
            WHEN `$order_by` REGEXP '^[0-9]+$' THEN CAST(`$order_by` AS UNSIGNED)
            ELSE `$order_by`
        END
    ";
}

/* ===============================
   5. RECHERCHE
=============================== */
$where = '';
$params = [];
$search = trim($_GET['search'] ?? '');
if ($search !== '') {
    $conditions = [];
    foreach ($cols as $c) {
        $conditions[] = "`$c` LIKE ?";
        $params[] = "%$search%";
    }
    $where = 'WHERE ' . implode(' OR ', $conditions);
}

/* ===============================
   6. PAGINATION
=============================== */
$limit = 200;
$page = max(1, intval($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM `$table_name` $where ORDER BY $order_sql $order_dir LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ===============================
   7. ORDRE DES COLONNES
=============================== */
$columns_order = json_decode($_GET['columns_order'] ?? json_encode($cols), true) ?: $cols;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($project['project_name']) ?></title>

<style>
:root{
    --orange:#f76b1c;
    --orange-light:#f5a623;
    --bg:#f4f6f8;
}

/* GLOBAL */
body{
    font-family: "Segoe UI", Arial, sans-serif;
    background: var(--bg);
    margin:0;
    padding:20px;
}
h1{ color:var(--orange); margin-bottom:5px; }
p{ color:#444; }

/* CARD */
.card{
    background:#fff;
    padding:20px;
    border-radius:14px;
    box-shadow:0 6px 20px rgba(0,0,0,.08);
    margin-bottom:20px;
}

/* SEARCH */
.search-bar{
    display:flex;
    flex-wrap:wrap;
    gap:10px;
}
.search-bar input{
    padding:10px;
    border-radius:8px;
    border:1px solid #ccc;
    min-width:220px;
}
.search-bar button{
    background:var(--orange-light);
    color:#fff;
    border:none;
    border-radius:8px;
    padding:10px 16px;
    cursor:pointer;
}
.search-bar button:hover{ background:var(--orange); }

/* ACTIONS */
.actions{
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    margin:15px 0;
}
.actions button{
    background:var(--orange-light);
    color:#fff;
    border:none;
    border-radius:8px;
    padding:8px 14px;
    cursor:pointer;
}
.actions button:last-child{
    background:#333;
}
.actions button:hover{ opacity:.9 }

/* TABLE */
.table-wrapper{
    overflow-x:auto;
}
table{
    width:100%;
    border-collapse:collapse;
    background:#fff;
}
th,td{
    padding:10px;
    border:1px solid #ddd;
    font-size:13px;
}
th{
    background:var(--orange-light);
    color:#fff;
    position:sticky;
    top:0;
}
th .th-content{
    display:flex;
    flex-direction:column;
    gap:6px;
}
th .controls{
    display:flex;
    gap:4px;
    flex-wrap:wrap;
}
.orderBtn{
    background:rgba(255,255,255,.3);
    color:#fff;
    text-decoration:none;
    padding:2px 6px;
    border-radius:4px;
    font-size:11px;
}
td img{ max-width:120px; border-radius:6px }

/* MOBILE */
@media(max-width:768px){
    h1{ font-size:20px }
    th,td{ font-size:12px }
}
</style>
</head>

<body>

<div class="card">
    <h1><?= htmlspecialchars($project['project_name']) ?></h1>
    <p><?= nl2br(htmlspecialchars($project['project_description'])) ?></p>
</div>

<div class="card">
<form method="get" class="search-bar">
    <input type="hidden" name="project_id" value="<?= $project_id ?>">
    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="🔍 Recherche">
    <button>Rechercher</button>
</form>
</div>

<form method="post" action="generate_draft.php" id="draftForm" class="card">
<input type="hidden" name="project_id" value="<?= $project_id ?>">
<input type="hidden" name="columns_order" id="columns_order" value='<?= htmlspecialchars(json_encode($columns_order)) ?>'>

<div class="actions">
    <button type="button" id="selectAllColsBtn">Colonnes</button>
    <button type="button" id="selectAllRowsBtn">Lignes</button>
    <button type="submit">📝 Générer Word</button>
</div>

<div class="table-wrapper">
<table id="dataTable">
<thead>
<tr>
<th>✔</th>
<?php foreach ($columns_order as $c): ?>
<th>
<div class="th-content">
    <label>
        <input type="checkbox" name="columns[]" value="<?= htmlspecialchars($c) ?>" checked>
        <?= strtoupper(htmlspecialchars($c)) ?>
    </label>
    <div class="controls">
        <a href="#" class="moveLeft orderBtn" data-col="<?= $c ?>">←</a>
        <a href="#" class="moveRight orderBtn" data-col="<?= $c ?>">→</a>
        <input type="color" name="color_<?= htmlspecialchars($c) ?>" value="#000000">
        <a href="?project_id=<?= $project_id ?>&order_by=<?= $c ?>&order_dir=ASC" class="orderBtn">▲</a>
        <a href="?project_id=<?= $project_id ?>&order_by=<?= $c ?>&order_dir=DESC" class="orderBtn">▼</a>
    </div>
</div>
</th>
<?php endforeach; ?>
</tr>
</thead>

<tbody>
<?php foreach ($rows as $row): ?>
<tr>
<td><input type="checkbox" class="rowCheckbox" name="selected_rows[]" value="<?= $row['id'] ?>"></td>
<?php foreach ($columns_order as $c): ?>
<td data-col="<?= htmlspecialchars($c) ?>">
<?php
$val = $row[$c] ?? '';
$json = json_decode($val,true);
if(is_array($json)) echo htmlspecialchars(implode(', ',$json));
elseif(preg_match('/\.(jpg|png|jpeg|gif|webp)$/i',$val) && file_exists($val))
    echo "<img src='".htmlspecialchars($val)."'>";
else echo htmlspecialchars($val);
?>
</td>
<?php endforeach; ?>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</form>

<script>
// (JS IDENTIQUE – NON MODIFIÉ)
document.getElementById('selectAllColsBtn').onclick=()=> {
    const c=[...document.querySelectorAll('input[name="columns[]"]')];
    const a=c.every(x=>x.checked);
    c.forEach(x=>x.checked=!a);
};
document.getElementById('selectAllRowsBtn').onclick=()=> {
    const c=[...document.querySelectorAll('.rowCheckbox')];
    const a=c.every(x=>x.checked);
    c.forEach(x=>x.checked=!a);
};
document.querySelectorAll('input[type=color]').forEach(p=>{
    p.oninput=()=> {
        const col=p.name.replace('color_','');
        document.querySelectorAll(`td[data-col="${col}"]`).forEach(td=>td.style.color=p.value);
    }
});
</script>

</body>
</html>
