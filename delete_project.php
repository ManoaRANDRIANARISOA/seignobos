<?php
session_start();
require "config.php";

header('Content-Type: application/json');

// Vérifier si utilisateur connecté
if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Non autorisé']);
    exit;
}

// Vérifier que l'ID est présent
if (!isset($_POST['id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID manquant']);
    exit;
}

$project_id = intval($_POST['id']);

// Nom de la table dynamique
$table_name = "project_" . $project_id;

try {
    // Supprimer la table dynamique si elle existe
    $pdo->exec("DROP TABLE IF EXISTS `$table_name`;");

    // Supprimer l’entrée du projet
    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->execute([$project_id]);

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

exit;
