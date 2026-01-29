<?php
session_start();
require "config.php";

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $email = trim($_POST["email"]);
    $code = trim($_POST["code"]);

    // Vérifier le code dans la DB
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? AND reset_code=? AND reset_expires_at > NOW()");
    $stmt->execute([$email, $code]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user){
        // Code valide !
        // On garde l'email en session pour l'étape suivante
        $_SESSION['reset_email'] = $email;
        
        // Rediriger vers la page de changement de mot de passe
        header("Location: new_password.php");
        exit;
    } else {
        // Code invalide ou expiré
        header("Location: verify_code.php?email=" . urlencode($email) . "&error=" . urlencode("Code invalide ou expiré"));
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>