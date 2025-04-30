<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $completed = $_POST['completed'];

    //Nese completed = 1 -> beje 0, nese 0 -> beje 1
    $newStatus = $completed ? 0 : 1;

    $sql = "UPDATE tasks SET completed = :completed WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':completed', $newStatus, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}

header('Location: index.php');
exit;
?>