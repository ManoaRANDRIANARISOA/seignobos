<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'config.php';

if (!isset($_SESSION['user'])) exit('Accès refusé');

$user_id = $_SESSION['user']['id'] ?? 0;

if ($user_id > 0) {
    // Récupérer les infos depuis la DB
    $stmt = $pdo->prepare("SELECT fullname, username, email FROM users WHERE id=?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // fallback si DB vide
    if (!$user) {
        $user = [
            'fullname' => $_SESSION['user']['fullname'] ?? '',
            'username' => $_SESSION['user']['username'] ?? '',
            'email' => $_SESSION['user']['email'] ?? ''
        ];
    }
} else {
    $user = [
        'fullname' => $_SESSION['user']['fullname'] ?? '',
        'username' => $_SESSION['user']['username'] ?? '',
        'email' => $_SESSION['user']['email'] ?? ''
    ];
}
?>

<div class="profile-container">
    <h2>Paramètres du profil</h2>
    <form id="profileForm">
        <label>Nom complet</label>
        <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" required>

        <label>Nom d'utilisateur</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>

        <label>Mot de passe <small>(laisser vide pour ne pas changer)</small></label>
        <input type="password" name="password" placeholder="Nouveau mot de passe">

        <button type="submit"><i class="fas fa-save"></i> Mettre à jour</button>
    </form>

    <p id="profileMessage"></p>

    <hr>
    <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Se déconnecter</a>
</div>

<style>
.profile-container {
    max-width: 500px;
    margin: 0 auto;
    background: #fff;
    padding: 25px 30px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.profile-container h2 {
    text-align: center;
    color: #f76b1c;
    margin-bottom: 20px;
}

.profile-container label {
    display: block;
    margin-top: 15px;
    font-weight: 600;
    color: #333;
}

.profile-container input[type="text"],
.profile-container input[type="password"],
.profile-container input[type="email"] {
    width: 100%;
    padding: 10px 12px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 14px;
}

.profile-container button {
    margin-top: 20px;
    padding: 10px 18px;
    background: linear-gradient(135deg, #f5a623, #f76b1c);
    color: #fff;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    transition: 0.3s;
}

.profile-container button:hover {
    filter: brightness(1.1);
    box-shadow: 0 4px 12px rgba(247,107,28,0.4);
}

.logout-btn {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 18px;
    background: #ccc;
    color: #333;
    border-radius: 10px;
    text-decoration: none;
    text-align: center;
    font-weight: 600;
    transition: 0.3s;
}

.logout-btn:hover {
    background: #f76b1c;
    color: #fff;
}

#profileMessage {
    margin-top: 15px;
    font-weight: 600;
}
</style>

<script>
document.getElementById('profileForm').addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(this);

    fetch('update_profile.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        const msg = document.getElementById('profileMessage');
        msg.textContent = data.message;
        msg.style.color = data.success ? 'green' : 'red';
    })
    .catch(()=>{
        const msg = document.getElementById('profileMessage');
        msg.textContent = "Erreur réseau, réessayez";
        msg.style.color = 'red';
    });
});
</script>
