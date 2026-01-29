<?php
require 'config.php'; // connexion PDO à la BD

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Vérification des champs
    if (empty($username)) {
        header("Location: login.html?error_username=Le nom d'utilisateur est requis");
        exit;
    }

    if (empty($password)) {
        header("Location: login.html?error_password=Le mot de passe est requis");
        exit;
    }

    // Récupération de l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Connexion réussie
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ];
        
        header("Location: dashboard.php"); // page après login
        exit;
    } else {
        header("Location: login.html?error_general=Nom d'utilisateur ou mot de passe incorrect");
        exit;
    }
} else {
    header("Location: login.html");
    exit;
}
?>
