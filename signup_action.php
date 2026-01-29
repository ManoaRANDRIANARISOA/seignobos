<?php
require 'config.php'; // connexion PDO à la BD

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    $errors = [];

    // Validation simple
    if (empty($fullname)) $errors['fullname'] = "Le nom complet est requis";
    if (empty($username)) $errors['username'] = "Le nom d'utilisateur est requis";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = "Adresse e-mail invalide";
    if (empty($password)) $errors['password'] = "Le mot de passe est requis";
    if ($password !== $confirm_password) $errors['confirm'] = "Les mots de passe ne correspondent pas";
    if (empty($role)) $errors['role'] = "Le rôle est requis";

    // Si erreurs, rediriger avec messages
    if (!empty($errors)) {
        $query = http_build_query(array_map(fn($k,$v)=>["error_$k"=>$v], array_keys($errors), $errors));
        header("Location: signup.html?$query");
        exit;
    }

    // Vérifier si username ou email existent déjà
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->rowCount() > 0) {
        header("Location: signup.html?error_general=Nom d'utilisateur ou e-mail déjà utilisé");
        exit;
    }

    // Hash du mot de passe
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insérer dans BD
    $stmt = $pdo->prepare("INSERT INTO users (fullname, username, email, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$fullname, $username, $email, $hashedPassword, $role]);

    // Redirection vers login avec message succès
    header("Location: login.html?success=Compte créé avec succès, veuillez vous connecter");
    exit;
} else {
    header("Location: signup.html");
    exit;
}
?>
