<?php
header('Content-Type: application/json');
require 'config.php';
session_start();

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Non autorisé']);
    exit;
}

// Récupération du champ pour lequel on demande une suggestion
$data = json_decode(file_get_contents('php://input'), true);
$field = $data['field'] ?? '';

$suggestions = [
    'identification'    => "Identifiant unique de l’extrait.",
    'upload'            => "Résumé possible de l’extrait importé.",
    'reference_source'  => "Type de source historique identifié.",
    'zotero'            => "Lien Zotero cohérent détecté.",
    'copie_reference'   => "Référence bibliographique normalisée.",
    'page_numerique'    => "Page numérique probable.",
    'page_papier'       => "Page papier correspondante.",
    'chapitre'          => "Chapitre principal suggéré.",
    'interet'           => "Intérêt historique majeur de cet extrait.",
    'dates'             => "Dates clés associées.",
    'marqueurs'         => "Mots-clés pertinents.",
    'periodes'          => "Période historique dominante.",
    'priorisation'      => "Priorité recommandée : 3"
];

echo json_encode([
    'status' => 'success',
    'suggestion' => $suggestions[$field] ?? "Suggestion indisponible."
]);
