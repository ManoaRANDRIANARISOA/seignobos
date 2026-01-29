<?php
session_start();
require "config.php";

if ($_SESSION['user']['role'] !== 'admin') exit;

$id = intval($_POST['id']);
$pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
