<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once "config.php";

if (!isset($_SESSION['user'])) exit("Accès refusé");

// Suppression AJAX
if (isset($_POST['delete'])) {
    $project_id = intval($_POST['id']);
    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->execute([$project_id]);
    echo json_encode(['status' => 'success']);
    exit;
}

// Récupération projets
$projects = $pdo->query("SELECT id, project_name, project_description, statut, created_at FROM projects ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="projects-list-container">
<h2>Liste des projets</h2>
<table>
<thead>
<tr>
    <th>ID</th>
    <th>Nom</th>
    <th>Description</th>
    <th>Statut</th>
    <th>Créé le</th>
    <th>Actions</th>
</tr>
</thead>
<tbody>
<?php foreach ($projects as $p): ?>
<tr id="project-<?= $p['id'] ?>">
    <td><?= $p['id'] ?></td>
    <td><?= htmlspecialchars($p['project_name']) ?></td>
    <td><?= htmlspecialchars($p['project_description']) ?></td>
    <td>
        <select onchange="updateStatus(<?= $p['id'] ?>, this.value)">
            <option value="En attente" <?= $p['statut']==='En attente'?'selected':'' ?>>En attente</option>
            <option value="En cours" <?= $p['statut']==='En cours'?'selected':'' ?>>En cours</option>
            <option value="Fini" <?= $p['statut']==='Fini'?'selected':'' ?>>Fini</option>
        </select>
    </td>
    <td><?= $p['created_at'] ?></td>
    <td>
        <a href="generate_form.php?id=<?= $p['id'] ?>" target="_blank">👁 Remplir</a> |
        <a href="edit_project.php?id=<?= $p['id'] ?>">✏️ Modifier</a> |
        <button onclick="deleteProject(<?= $p['id'] ?>)">🗑 Supprimer</button> |
        <a href="view_project.php?id=<?= $p['id'] ?>">📊 Afficher</a>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

<script>
// Suppression AJAX
function deleteProject(id) {
    if (!confirm("Voulez-vous vraiment supprimer ce projet ?")) return;
    fetch("delete_project.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "id=" + encodeURIComponent(id)
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === "success") document.getElementById("project-" + id)?.remove();
        else alert(data.message || "Erreur lors de la suppression");
    })
    .catch(()=>alert("Erreur réseau"));
}

// Mise à jour statut AJAX
function updateStatus(id, newStatus) {
    fetch("update_status.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "id=" + encodeURIComponent(id) + "&statut=" + encodeURIComponent(newStatus)
    })
    .then(r => r.json())
    .then(data => { if(data.status !== "success") alert(data.message || "Erreur mise à jour"); })
    .catch(()=>alert("Erreur réseau"));
}
</script>

<style>
.projects-list-container { padding: 20px; overflow-x:auto; }
.projects-list-container table { width: 100%; border-collapse: collapse; margin-top: 10px; }
.projects-list-container th, .projects-list-container td { border: 1px solid #ddd; padding: 10px; text-align: left; }
.projects-list-container th { background: #f5a623; color: #fff; }
.projects-list-container select { padding: 5px 8px; border-radius: 6px; border: 1px solid #ccc; }
.projects-list-container button { padding: 5px 10px; background: #f76b1c; color:#fff; border:none; border-radius:6px; cursor:pointer; transition:0.3s;}
.projects-list-container button:hover { filter: brightness(1.1); }
.projects-list-container a { text-decoration: none; color: #f76b1c; }
.projects-list-container a:hover { text-decoration: underline; }

/* Responsive */
@media(max-width:768px) { 
    .projects-list-container table, 
    .projects-list-container thead, 
    .projects-list-container tbody, 
    .projects-list-container th, 
    .projects-list-container td, 
    .projects-list-container tr { display:block; width:100%; } 
    .projects-list-container tr { margin-bottom:15px; } 
    .projects-list-container th { display:none; } 
    .projects-list-container td { text-align:right; padding-left:50%; position:relative; } 
    .projects-list-container td::before { content: attr(data-label); position:absolute; left:10px; text-align:left; font-weight:600; } 
}
</style>
