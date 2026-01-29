<?php
session_start();
if(!isset($_SESSION['reset_email'])){
    header("Location: forgot_password.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau mot de passe - SEIGNOBOS</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .logo {
            width: 80px;
            margin-bottom: 1rem;
        }
        h2 {
            color: #1a1a1a;
            margin-bottom: 1.5rem;
        }
        .input-group {
            margin-bottom: 1rem;
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #4a4a4a;
        }
        input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 1rem;
        }
        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-login:hover {
            background-color: #004494;
        }
        .error-msg {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Remplacez src par votre logo réel si disponible -->
        <img src="assets/img/logo.png" alt="Logo" class="logo" onerror="this.style.display='none'"> 
        <h2>Nouveau mot de passe</h2>
        <p>Veuillez définir votre nouveau mot de passe.</p>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="error-msg" style="margin-bottom: 15px;"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <form action="new_password_action.php" method="POST" onsubmit="return validateForm()">
            <div class="input-group">
                <label for="password">Nouveau mot de passe</label>
                <input type="password" id="password" name="password" required minlength="6">
            </div>
            <div class="input-group">
                <label for="confirm_password">Confirmer le mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
            </div>
            <button type="submit" class="btn-login">Réinitialiser</button>
        </form>
    </div>

    <script>
        function validateForm() {
            var p1 = document.getElementById("password").value;
            var p2 = document.getElementById("confirm_password").value;
            if(p1 !== p2) {
                alert("Les mots de passe ne correspondent pas !");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>