<?php
session_start();
if (!isset($_SESSION['can_reset_password']) || $_SESSION['can_reset_password'] !== true) {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nouveau mot de passe - SEIGNOBOS</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="auth.css">
</head>
<body>
<div class="header">
    <div class="logo"><h2>SEIGNOBOS</h2></div>
</div>

<div class="login-page-wrapper">
    <div class="login-container">
        <h1>Nouveau mot de passe</h1>
        <p class="text-muted">Choisissez un nouveau mot de passe sécurisé.</p>
        
        <form action="reset_new_password_action.php" method="POST">
            <div class="input-group">
                <label for="password">Nouveau mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="input-group">
                <label for="confirm_password">Confirmer le mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <div class="error-msg" id="error-msg"></div>
            </div>
            
            <button type="submit" class="btn-login">Réinitialiser</button>
        </form>
    </div>
</div>

<script>
window.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    if(params.has('error')){
        document.getElementById('error-msg').innerText = params.get('error');
    }
});
</script>
</body>
</html>
