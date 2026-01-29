<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/autoload.php';
require 'config.php';

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $email = trim($_POST["email"]);

    // 1. Validation du format de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: forgot_password.html?error_email=" . urlencode("Format d'email invalide"));
        exit;
    }

    // 2. Vérifier si l'email existe
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$user){
        // "dans le cas ou l'email n'est meme pas dans la bd il faut dire a l'utilisauteur qu'il se trompe"
        header("Location: forgot_password.html?error_email=" . urlencode("Cette adresse email ne correspond à aucun compte."));
        exit;
    }

    // Générer un code à 6 chiffres
    $code = rand(100000, 999999);
    // IMPORTANT : On utilise le fuseau horaire de PHP/MySQL pour être cohérent.
    // Si la DB est en UTC et PHP en Europe/Paris, NOW() peut différer.
    // On force l'ajout de 1 heure pour être sûr (ou on ajuste le timezone).
    // Mieux : On utilise DATE_ADD en SQL pour être synchro avec la DB.
    
    // Enregistrer dans la DB avec DATE_ADD pour éviter les décalages horaires PHP/MySQL
    $stmt = $pdo->prepare("UPDATE users SET reset_code=?, reset_expires_at=DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE email=?");
    $stmt->execute([$code, $email]);

    // Préparer l'envoi avec PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configuration Serveur SMTP
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = SMTP_PORT;
        
        // Encodage
        $mail->CharSet = 'UTF-8';

        // Destinataires
        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress($email, $user['fullname']);

        // Contenu
        $mail->isHTML(true);
        $mail->Subject = 'Code de réinitialisation - SEIGNOBOS';
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; color: #333;'>
                <h2>Bonjour " . htmlspecialchars($user['fullname']) . ",</h2>
                <p>Vous avez demandé une réinitialisation de mot de passe pour votre compte Seignobos.</p>
                <p>Votre code de vérification est :</p>
                <div style='background: #f4f4f4; padding: 15px; border-radius: 5px; text-align: center; width: fit-content; margin: 20px 0;'>
                    <h1 style='margin: 0; letter-spacing: 5px; color: #2c3e50; font-size: 32px;'>$code</h1>
                </div>
                <p>Ce code est valide pendant <strong>15 minutes</strong>.</p>
                <p style='color: #777; font-size: 12px; margin-top: 30px;'>Si vous n'êtes pas à l'origine de cette demande, veuillez ignorer cet email.</p>
            </div>
        ";
        $mail->AltBody = "Bonjour " . $user['fullname'] . ",\n\nVotre code de vérification est : $code\n\nCe code expire dans 15 minutes.";

        $mail->send();
        
        // Succès : redirection vers la page de vérification
        header("Location: verify_code.php?email=" . urlencode($email));
        exit;

    } catch (Exception $e) {
        // Erreur d'envoi
        $errorMsg = "L'email n'a pas pu être envoyé. ";
        
        // Message d'aide si la config est par défaut
        if (defined('SMTP_USER') && strpos(SMTP_USER, 'votre_email') !== false) {
            $errorMsg .= "Veuillez configurer les paramètres SMTP dans config.php (OBLIGATOIRE pour un envoi réel).";
        } else {
            $errorMsg .= "Erreur technique : " . $mail->ErrorInfo;
        }
        
        header("Location: forgot_password.html?error_general=" . urlencode($errorMsg));
        exit;
    }

} else {
    header("Location: forgot_password.html");
    exit;
}
?>
