<?php
require "config.php";
session_start();

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    exit("Non autorisé");
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data['project_name']) || empty($data['form_schema'])) {
    http_response_code(400);
    exit("Données invalides");
}

$stmt = $pdo->prepare(
    "INSERT INTO projects (user_id, project_name, project_description, form_data)
     VALUES (?, ?, ?, ?)"
);

$stmt->execute([
    $_SESSION['user']['id'],
    $data['project_name'],
    $data['project_description'] ?? '',
    json_encode($data['form_schema'], JSON_UNESCAPED_UNICODE)
]);

echo "Projet enregistré";
