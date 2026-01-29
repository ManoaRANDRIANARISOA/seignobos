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
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root{
    --orange:#f76b1c;
    --orange-light:#f5a623;
    --bg:#f4f6f8;
}

/* GLOBAL */
body{
    font-family: 'Poppins', sans-serif;
    background: var(--bg);
    margin:0;
    padding:20px;
    color: #333;
}
h1{ color:var(--orange); margin-bottom:5px; font-weight: 600; }
p{ color:#555; }

/* BACK BUTTON */
.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    color: #555;
    background: #fff;
    padding: 8px 15px;
    border-radius: 8px;
    font-weight: 500;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    margin-bottom: 20px;
    transition: 0.3s;
}
.back-btn:hover {
    color: var(--orange);
    transform: translateX(-5px);
}

/* CARD */
.card{
    background:#fff;
    padding:25px;
    border-radius:14px;
    box-shadow:0 6px 20px rgba(0,0,0,.08);
    margin-bottom:20px;
}

/* SEARCH */
.search-bar{
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    align-items: center;
}
.search-bar input{
    padding:10px 15px;
    border-radius:8px;
    border:1px solid #ccc;
    min-width:250px;
    font-family: 'Poppins', sans-serif;
}
.search-bar button{
    background: linear-gradient(135deg, var(--orange-light), var(--orange));
    color:#fff;
    border:none;
    border-radius:8px;
    padding:10px 20px;
    cursor:pointer;
    font-family: 'Poppins', sans-serif;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: 0.3s;
}
.search-bar button:hover{ opacity: 0.9; transform: translateY(-2px); }

/* ACTIONS */
.actions{
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    margin:15px 0;
}
.actions button{
    background: #fff;
    border: 1px solid #ddd;
    color: #555;
    border-radius:8px;
    padding:8px 16px;
    cursor:pointer;
    font-family: 'Poppins', sans-serif;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: 0.3s;
}
.actions button:hover{ background: #f9f9f9; color: var(--orange); }

.actions button[type="submit"]{
    background: linear-gradient(135deg, var(--orange-light), var(--orange));
    color: #fff;
    border: none;
    font-weight: 500;
}
.actions button[type="submit"]:hover{ opacity: 0.9; color: #fff; }

/* TABLE */
.table-wrapper{
    overflow-x:auto;
    border-radius: 12px;
}
table{
    width:100%;
    border-collapse:collapse;
    background:#fff;
}
th,td{
    padding:12px 15px;
    border:1px solid #eee;
    font-size:14px;
}
th{
    background: linear-gradient(135deg, var(--orange-light), var(--orange));
    color:#fff;
    position:sticky;
    top:0;
    z-index: 10;
}
th .th-content{
    display:flex;
    flex-direction:column;
    gap:8px;
}
th .controls{
    display:flex;
    gap:4px;
    flex-wrap:wrap;
}
.orderBtn{
    background:rgba(255,255,255,.2);
    color:#fff;
    text-decoration:none;
    padding:4px 6px;
    border-radius:4px;
    font-size:12px;
    transition: 0.2s;
}
.orderBtn:hover { background: rgba(255,255,255,0.4); }

td img{ max-width:120px; border-radius:6px }

/* MOBILE */
@media(max-width:768px){
    h1{ font-size:20px }
    th,td{ font-size:12px }
}
</style>
</head>

<body>

<a href="dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Retour au tableau de bord</a>

<div class="card">
    <h1><?= htmlspecialchars($project['project_name']) ?></h1>
    <p><?= nl2br(htmlspecialchars($project['project_description'])) ?></p>
</div>

<div class="card">
<form method="get" class="search-bar">
    <input type="hidden" name="project_id" value="<?= $project_id ?>">
    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Rechercher...">
    <button type="submit"><i class="fas fa-search"></i> Rechercher</button>
</form>
</div>

<form method="post" action="generate_draft.php" id="draftForm" class="card">
<input type="hidden" name="project_id" value="<?= $project_id ?>">
<input type="hidden" name="columns_order" id="columns_order" value='<?= htmlspecialchars(json_encode($columns_order)) ?>'>

<div class="actions">
    <button type="button" id="selectAllColsBtn"><i class="fas fa-columns"></i> Colonnes</button>
    <button type="button" id="selectAllRowsBtn"><i class="fas fa-list-ol"></i> Lignes</button>
    <button type="submit"><i class="fas fa-file-word"></i> Générer Word</button>
</div>

<div class="table-wrapper">
<table id="dataTable">
<thead>
<tr>
<th><i class="fas fa-check-square"></i></th>
<?php foreach ($columns_order as $c): ?>
<th>
<div class="th-content">
    <label>
        <input type="checkbox" name="columns[]" value="<?= htmlspecialchars($c) ?>" checked>
        <?= strtoupper(htmlspecialchars($c)) ?>
    </label>
    <div class="controls">
        <a href="#" class="moveLeft orderBtn" data-col="<?= $c ?>"><i class="fas fa-arrow-left"></i></a>
        <a href="#" class="moveRight orderBtn" data-col="<?= $c ?>"><i class="fas fa-arrow-right"></i></a>
        <input type="color" name="color_<?= htmlspecialchars($c) ?>" value="#000000" title="Changer la couleur">
        <a href="?project_id=<?= $project_id ?>&order_by=<?= $c ?>&order_dir=ASC" class="orderBtn" title="Trier Asc"><i class="fas fa-sort-up"></i></a>
        <a href="?project_id=<?= $project_id ?>&order_by=<?= $c ?>&order_dir=DESC" class="orderBtn" title="Trier Desc"><i class="fas fa-sort-down"></i></a>
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
