<?php
require 'config.php';
session_start();

if (!isset($_SESSION['can_reset_password']) || !isset($_SESSION['reset_email'])) {
    header("Location: login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $email = $_SESSION['reset_email'];

    if ($password !== $confirm) {
        header("Location: reset_new_password.php?error=" . urlencode("Les mots de passe ne correspondent pas"));
        exit;
    }

    if (strlen($password) < 6) {
        header("Location: reset_new_password.php?error=" . urlencode("Le mot de passe doit faire au moins 6 caractères"));
        exit;
    }

    // Hashage et mise à jour
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // On met à jour le mot de passe ET on vide le code de reset pour qu'il ne soit plus utilisable
    $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_code = NULL, reset_expires_at = NULL WHERE email = ?");
    $stmt->execute([$hashed, $email]);

    // Nettoyage session
    unset($_SESSION['can_reset_password']);
    unset($_SESSION['reset_email']);

    // Redirection login avec succès
    header("Location: login.html?success=" . urlencode("Mot de passe réinitialisé avec succès. Vous pouvez vous connecter."));
    exit;

} else {
    header("Location: reset_new_password.php");
    exit;
}
?>
