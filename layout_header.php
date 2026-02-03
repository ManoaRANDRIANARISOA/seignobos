<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier connexion
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

$user = $_SESSION['user'];
$username = htmlspecialchars($user['username']);
$role = htmlspecialchars($user['role']);

// Déterminer la page active pour la classe CSS 'active'
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard SEIGNOBOS</title>

<!-- Fonts: Poppins -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- FontAwesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<link rel="stylesheet" href="dashboard.css">
<style>
/* ===== PROFILE ===== */
.profile-container {
    max-width: 500px;
    background: #fff;
    padding: 20px;
    margin: 20px 0;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.profile-container label {
    display: block;
    margin: 10px 0 4px;
    font-weight: 600;
}
.profile-container input, 
.profile-container select {
    width: 100%;
    padding: 8px;
    border-radius: 6px;
    border: 1px solid #ccc;
    margin-bottom: 10px;
}
.profile-container button {
    background: linear-gradient(135deg, #f5a623, #f76b1c);
    color: #fff;
    border: none;
    padding: 10px 16px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: 0.3s;
}
.profile-container button:hover {
    filter: brightness(1.1);
}
.success { color: green; }
.error { color: red; }

/* ===== HAMBURGER ===== */
.hamburger {
    display: none;
    position: fixed;
    top: 20px;
    left: 20px;
    z-index: 1100;
    width: 30px;
    height: 25px;
    flex-direction: column;
    justify-content: space-between;
    cursor: pointer;
}
.hamburger div {
    width: 100%;
    height: 4px;
    background-color: #333;
    border-radius: 3px;
    transition: all 0.3s;
}

/* ===== RESPONSIVE ===== */
@media screen and (max-width: 992px) {
    .dashboard-container {
        margin-left: 0;
        padding: 20px;
    }
    .sidebar {
        position: fixed;
        width: 220px;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        z-index: 1000;
    }
    .sidebar.show {
        transform: translateX(0);
    }
    .menu a { padding: 10px 12px; }
}
@media screen and (max-width: 768px) {
    .hamburger { display: flex; }
    table th, table td { padding: 8px; font-size: 14px; }
}
</style>
</head>
<body>

<!-- HAMBURGER -->
<div class="hamburger" id="hamburger">
  <div></div>
  <div></div>
  <div></div>
</div>

<aside class="sidebar">
    <div class="logo"><h2>SEIGNOBOS</h2></div>
    <nav class="menu">
      <a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>"><i class="fas fa-home"></i> Accueil</a>
      <a href="new_project_page.php" class="<?= $current_page == 'new_project_page.php' ? 'active' : '' ?>"><i class="fas fa-plus-circle"></i> Nouveau projet</a>
      <a href="ongoing_projects_page.php" class="<?= $current_page == 'ongoing_projects_page.php' ? 'active' : '' ?>"><i class="fas fa-spinner"></i> Projets en cours</a>
      <a href="projects_list_page.php" class="<?= $current_page == 'projects_list_page.php' ? 'active' : '' ?>"><i class="fas fa-list"></i> Liste des projets</a>
      <?php if($role === 'admin'): ?>
          <a href="users_page.php" class="<?= $current_page == 'users_page.php' ? 'active' : '' ?>"><i class="fas fa-users"></i> Gestion utilisateurs</a>
      <?php endif; ?>
      <a href="settings_page.php" class="<?= $current_page == 'settings_page.php' ? 'active' : '' ?>"><i class="fas fa-cog"></i> Paramètres</a>
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </nav>
</aside>

<main class="dashboard-container">
