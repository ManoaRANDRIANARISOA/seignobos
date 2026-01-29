<?php
session_start();
require 'config.php';

if(!isset($_SESSION['user'])){ http_response_code(403); exit('Non autorisé'); }

$project_name = $_POST['project_name'] ?? '';
$project_description = $_POST['project_description'] ?? '';
$form_data = $_POST['form_data'] ?? '';

if(!$project_name || !$form_data){
    echo json_encode(['status'=>'error','message'=>'Données manquantes']);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO projects (user_id, project_name, project_description, form_data) VALUES (?,?,?,?)");
if($stmt->execute([$_SESSION['user']['id'],$project_name,$project_description,$form_data])){
    echo json_encode(['status'=>'success','project_id'=>$pdo->lastInsertId()]);
}else{
    echo json_encode(['status'=>'error','message'=>'Erreur DB']);
}
