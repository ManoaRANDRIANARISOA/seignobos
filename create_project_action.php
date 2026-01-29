<?php
require_once 'config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Non autorisé']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $project_name = trim($_POST['project_name'] ?? 'Nouveau projet');
        $project_description = trim($_POST['project_description'] ?? '');
        $form_data = $_POST['form_data'] ?? '[]';

        if (empty($project_name)) {
            echo json_encode(['status' => 'error', 'message' => 'Le nom du projet est requis']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO projects (project_name, project_description, form_data) VALUES (?, ?, ?)");
        $stmt->execute([$project_name, $project_description, $form_data]);

        $project_id = $pdo->lastInsertId();
        $table_name = "project_" . intval($project_id);
        
        $pdo->exec("CREATE TABLE IF NOT EXISTS `$table_name` (
            `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $fields = json_decode($form_data, true) ?: [];
        foreach ($fields as $field) {
            $col_name = preg_replace('/[^a-zA-Z0-9_]/', '_', $field['name']);
            // Vérifier si la colonne existe déjà pour éviter les erreurs (bien que table nouvelle)
            $pdo->exec("ALTER TABLE `$table_name` ADD COLUMN `$col_name` TEXT NULL");
            if (!empty($field['allowOther'])) {
                $pdo->exec("ALTER TABLE `$table_name` ADD COLUMN `{$col_name}_other` TEXT NULL");
            }
        }

        echo json_encode(['status' => 'success', 'message' => 'Projet créé avec succès !']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erreur : ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);
}
