<?php
require_once 'auth.php';
require_once 'connection.php';
$id = $_GET['id'] ?? '';
if ($id) {
    $stmt = $connection->prepare("DELETE FROM data WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}
header("Location: readtable.php");
exit;
