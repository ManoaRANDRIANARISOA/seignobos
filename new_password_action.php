<?php
session_start();
require "config.php";

if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['reset_email'])){
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $email = $_SESSION['reset_email'];

    if($password !== $confirm_password){
        header("Location: new_password.php?error=" . urlencode("Les mots de passe ne correspondent pas"));
        exit;
    }

    if(strlen($password) < 6){
        header("Location: new_password.php?error=" . urlencode("Le mot de passe doit faire au moins 6 caractères"));
        exit;
    }

    // Hasher le nouveau mot de passe (compatible avec login_action.php qui utilise password_verify)
    $new_hash = password_hash($password, PASSWORD_DEFAULT);

    // Mettre à jour le mot de passe et effacer le code de reset
    $stmt = $pdo->prepare("UPDATE users SET password=?, reset_code=NULL, reset_expires_at=NULL WHERE email=?");
    $stmt->execute([$new_hash, $email]);

    // Nettoyer la session
    unset($_SESSION['reset_email']);

    // Rediriger vers login avec message succès
    // On suppose que index.php redirige vers login.php ou affiche le login
    // Je vais rediriger vers login.html s'il existe, sinon index.php
    header("Location: index.php?success=" . urlencode("Mot de passe modifié avec succès. Connectez-vous."));
    exit;

} else {
    header("Location: index.php");
    exit;
}
?>