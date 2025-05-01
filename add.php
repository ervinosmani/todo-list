<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);

    //Validimi
    if (strlen($title) === 0) {
        $error = "Titulli nuk mund te jete i zbrazet!";
    } elseif (strlen($title) > 100) {
        $error = "Titulli eshte shume i gjate! (max 100 karaktere)";
    }

    //Nese nuk ka error, shfaq detyren
    if (!isset($error)) {
        $sql = "INSERT INTO tasks (title) VALUES (:title)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->execute();
        header("Location: index.php");
        exit;
    } else {
        //Shfaq gabimin ne index.php me metoden GET
        header("Location: index.php?error=" . urlencode($error));
        exit;
    }
}


?>