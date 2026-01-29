<?php
// Accueil Dashboard
require_once 'config.php';

// Compter les projets
$total = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$en_cours = $pdo->query("SELECT COUNT(*) FROM projects WHERE statut='En cours'")->fetchColumn();
$nouveau = $pdo->query("SELECT COUNT(*) FROM projects WHERE statut='En attente'")->fetchColumn();
$fini = $pdo->query("SELECT COUNT(*) FROM projects WHERE statut='Fini'")->fetchColumn();
?>

<div class="dashboard-header">
    <h1>Bienvenue <?= $username ?> !</h1>
    <p>Rôle : <strong><?= $role ?></strong></p>
</div>

<div class="dashboard-cards">
    <div class="card new-projects">
        <div class="card-icon"><i class="fas fa-folder-plus"></i></div>
        <div class="card-info">
            <h2><?= $nouveau ?></h2>
            <p>Nouveaux projets</p>
        </div>
    </div>

    <div class="card ongoing-projects">
        <div class="card-icon"><i class="fas fa-spinner"></i></div>
        <div class="card-info">
            <h2><?= $en_cours ?></h2>
            <p>Projets en cours</p>
        </div>
    </div>

    <div class="card total-projects">
        <div class="card-icon"><i class="fas fa-list"></i></div>
        <div class="card-info">
            <h2><?= $total ?></h2>
            <p>Liste des projets</p>
        </div>
    </div>
</div>

<div class="dashboard-chart">
    <canvas id="projectsChart" width="400" height="200"></canvas>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('projectsChart').getContext('2d');
const projectsChart = new Chart(ctx, {
    type: 'doughnut', // Camembert
    data: {
        labels: ['En attente', 'En cours', 'Fini'],
        datasets: [{
            label: 'Projets',
            data: [<?= $nouveau ?>, <?= $en_cours ?>, <?= $fini ?>],
            backgroundColor: ['#f5a623', '#3498db', '#2ecc71'],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            },
            title: {
                display: true,
                text: 'Répartition des projets'
            }
        }
    }
});
</script>

<style>
.dashboard-header { margin-bottom: 30px; }
.dashboard-cards {
    display: flex;
    gap: 20px;
    margin-bottom: 40px;
    flex-wrap: wrap;
}
.card {
    flex: 1;
    min-width: 200px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    padding: 30px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    transition: transform 0.2s;
    /* Aspect carré si possible, ou du moins hauteur min */
    min-height: 180px; 
}
.card:hover { transform: translateY(-5px); }
.card-icon { 
    font-size: 50px; 
    margin-bottom: 15px;
    color: #f76b1c; /* Orange par défaut */
}
.card-info h2 { margin:0; font-size:36px; color:#333; }
.card-info p { margin:5px 0 0 0; color:#777; font-weight:600; font-size: 16px; }

/* Couleurs spécifiques */
.new-projects .card-icon { color: #f5a623; }
.ongoing-projects .card-icon { color: #3498db; }
.total-projects .card-icon { color: #555; }

.new-projects { border-top: 5px solid #f5a623; }
.ongoing-projects { border-top: 5px solid #3498db; }
.total-projects { border-top: 5px solid #555; }

.dashboard-chart {
    max-width: 600px;
    margin: auto;
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}
</style>
