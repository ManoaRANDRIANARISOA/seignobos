<?php
session_start();
require "config.php";

if ($_SESSION['user']['role'] !== 'admin') exit;

$id = intval($_POST['id']);
$role = $_POST['role'];

$stmt = $pdo->prepare("UPDATE users SET role=? WHERE id=?");
$stmt->execute([$role, $id]);
