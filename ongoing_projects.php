<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once "config.php";

if (!isset($_SESSION['user'])) exit("Accès refusé");

// Récupération projets en cours
$projects = $pdo->prepare("SELECT id, project_name, project_description FROM projects WHERE statut = 'En cours' ORDER BY created_at DESC");
$projects->execute();
$projects = $projects->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="ongoing-projects-container">
    <h2>Projets en cours</h2>
    <?php if(empty($projects)): ?>
        <p>Aucun projet en cours pour le moment.</p>
    <?php else: ?>
        <div class="cards-container">
            <?php foreach($projects as $p): ?>
            <div class="project-card">
                <h3><?= htmlspecialchars($p['project_name']) ?></h3>
                <p><?= htmlspecialchars($p['project_description']) ?></p>
                <a href="generate_form.php?id=<?= $p['id'] ?>" class="fill-btn">Remplir</a>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.ongoing-projects-container { padding: 20px; }
.cards-container { display: flex; flex-wrap: wrap; gap: 20px; justify-content: flex-start; }
.project-card {
    background: linear-gradient(135deg, #FFA726, #FB8C00);
    color: #fff;
    border-radius: 12px;
    padding: 20px;
    width: 260px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: 0 6px 18px rgba(0,0,0,0.15);
    transition: transform 0.2s, box-shadow 0.2s;
}
.project-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
.project-card h3 { margin: 0 0 10px 0; font-size: 18px; font-weight: 700; }
.project-card p { flex-grow: 1; font-size: 14px; margin-bottom: 15px; }
.fill-btn {
    display: inline-block; text-decoration: none; background: #fff; color: #FB8C00;
    padding: 10px 16px; border-radius: 8px; text-align: center; font-weight: 600;
    border: 2px solid #FB8C00; transition: 0.3s;
}
.fill-btn:hover { background: #FB8C00; color: #fff; }

/* Responsive */
@media (max-width: 768px) { .cards-container { flex-direction: column; align-items: center; } }
</style>
