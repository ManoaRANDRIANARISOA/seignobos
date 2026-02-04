<?php 
$host = "localhost"; 
$dbname = "seignobos_db"; 
$user = "root"; 
$pass = ""; 

try { 
    $pdo = new PDO( 
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4", 
        $user, 
        $pass, 
        [ 
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC 
        ] 
    ); 
} catch (PDOException $e) { 
    die("Erreur DB: " . $e->getMessage()); 
} 

// ========================================== 
// CONFIGURATION EMAIL (BREVO SMTP) 
// ========================================== 
define('SMTP_HOST', 'smtp-relay.brevo.com'); 
define('SMTP_PORT', 587); 
define('SMTP_SECURE', 'tls'); 

// Identifiants fournis par Brevo 
define('SMTP_USER', 'a0f1bf001@smtp-brevo.com'); 
define('SMTP_PASS', 'zdLQZx9kbrFRq8fW'); 

// IMPORTANT : Avec Brevo gratuit, l'adresse "From" doit souvent correspondre 
// à l'email utilisé pour créer le compte Brevo (votre email personnel d'inscription). 
// Si l'envoi échoue, remplacez 'no-reply@seignobos.com' par votre email d'inscription Brevo. 
define('FROM_EMAIL', 'zerodevy@gmail.com'); 
define('FROM_NAME', 'Support Seignobos'); 
?>