<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id']) || !ctype_digit($_POST['id'])) {
    header('Location: tableau_de_bord.php');
    exit;
}

$id = intval($_POST['id']);
$mysqli = new mysqli('127.0.0.1', 'root', 'root', 'company_cecile');

if (!$mysqli->connect_error) {
    $stmt = $mysqli->prepare('DELETE FROM team WHERE id = ?');
    if ($stmt) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }
    $mysqli->close();
}

header('Location: tableau_de_bord.php');
exit;
