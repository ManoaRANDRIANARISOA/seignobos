<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require "config.php";

if (!isset($_SESSION['user'])) {
    echo json_encode(['status'=>'error','message'=>'Accès refusé']);
    exit;
}

// Vérifier que les données POST existent
if (!isset($_POST['id'], $_POST['statut'])) {
    echo json_encode(['status'=>'error','message'=>'Données manquantes']);
    exit;
}

$id = intval($_POST['id']);
$statut = trim($_POST['statut']);

// Vérifier que le statut est valide
$validStatus = ['En attente','En cours','Fini'];
if (!in_array($statut, $validStatus)) {
    echo json_encode(['status'=>'error','message'=>'Statut invalide']);
    exit;
}

// Mettre à jour le projet
$stmt = $pdo->prepare("UPDATE projects SET statut=? WHERE id=?");
if ($stmt->execute([$statut, $id])) {
    echo json_encode(['status'=>'success']);
} else {
    echo json_encode(['status'=>'error','message'=>'Erreur base de données']);
}
?>
