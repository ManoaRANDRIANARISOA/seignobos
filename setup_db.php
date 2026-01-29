<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "127.0.0.1";
$user = "root";
$pass = ""; // Mot de passe vide confirmé par le diagnostic

echo "<h1>Installation de la Base de Données</h1>";

try {
    // 1. Connexion au serveur MySQL (sans choisir de base)
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "<p>✅ Connexion au serveur réussie.</p>";
    
    // 2. Lecture du fichier SQL
    $sqlFile = __DIR__ . '/BD/127_0_0_1.sql';
    if (!file_exists($sqlFile)) {
        die("<p style='color:red'>❌ Erreur : Le fichier BD/127_0_0_1.sql est introuvable.</p>");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // 3. Exécution des requêtes
    // On peut exécuter tout le fichier d'un coup avec exec()
    $pdo->exec($sql);
    
    echo "<p>✅ Base de données 'seignobos_db' créée et tables importées avec succès !</p>";
    echo "<div style='background:#e8f5e9; padding:15px; border:1px solid green; border-radius:5px; margin-top:20px;'>";
    echo "<h3>🎉 Tout est prêt !</h3>";
    echo "<p>Vous pouvez maintenant vous inscrire ou vous connecter.</p>";
    echo "<p><a href='signup.html' style='background:#3498db; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Créer un compte</a> ";
    echo "<a href='login.html' style='background:#2ecc71; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Se connecter</a></p>";
    echo "</div>";

} catch (PDOException $e) {
    echo "<div style='background:#ffebee; padding:15px; border:1px solid red; border-radius:5px;'>";
    echo "<h3>❌ Erreur lors de l'installation</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}
?>
