<?php
$email = $_GET['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Vérification du code - SEIGNOBOS</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="auth.css">
</head>
<body>
<div class="header">
    <div class="logo"><h2>SEIGNOBOS</h2></div>
</div>

<div class="login-page-wrapper">
    <div class="login-container">
        <h1>Vérification</h1>
        <p class="text-muted">Entrez le code envoyé à <strong><?= htmlspecialchars($email) ?></strong></p>
        
        <form action="verify_code_action.php" method="POST">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            
            <div class="input-group">
                <label for="code">Code de vérification (6 chiffres)</label>
                <input type="text" id="code" name="code" required pattern="[0-9]{6}" maxlength="6" placeholder="123456" style="letter-spacing: 5px; text-align: center; font-size: 20px;">
                <div class="error-msg" id="error-code"></div>
            </div>
            
            <button type="submit" class="btn-login">Vérifier</button>
        </form>
        <p><a href="forgot_password.html">Renvoyer le code</a></p>
    </div>
</div>

<script>
window.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    if(params.has('error')){
        document.getElementById('error-code').innerText = params.get('error');
        document.getElementById('code').classList.add('input-error');
    }
});
</script>
</body>
</html>
