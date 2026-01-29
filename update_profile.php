<?php
session_start();
require 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Utilisateur non connecté'
    ]);
    exit;
}

$user_id = $_SESSION['user']['id'] ?? 0;
if ($user_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'ID utilisateur invalide'
    ]);
    exit;
}

// Récupération et sécurisation des données
$fullname = trim($_POST['fullname'] ?? '');
$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($fullname === '' || $username === '' || $email === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Tous les champs obligatoires doivent être remplis'
    ]);
    exit;
}

try {

    // Si mot de passe fourni → mise à jour avec password
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            UPDATE users 
            SET fullname = ?, username = ?, email = ?, password = ?
            WHERE id = ?
        ");
        $stmt->execute([$fullname, $username, $email, $hashedPassword, $user_id]);

    } else {
        // Sinon, mise à jour sans toucher au mot de passe
        $stmt = $pdo->prepare("
            UPDATE users 
            SET fullname = ?, username = ?, email = ?
            WHERE id = ?
        ");
        $stmt->execute([$fullname, $username, $email, $user_id]);
    }

    // Mise à jour de la session
    $_SESSION['user']['fullname'] = $fullname;
    $_SESSION['user']['username'] = $username;
    $_SESSION['user']['email']    = $email;

    echo json_encode([
        'success' => true,
        'message' => 'Profil mis à jour avec succès'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la mise à jour du profil'
    ]);
}
