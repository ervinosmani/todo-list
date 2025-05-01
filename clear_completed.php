<?php
require_once 'db.php';

$sql = "DELETE FROM tasks WHERE completed = 1";
$stmt = $conn->prepare($sql);
$stmt->execute();

header("Location: index.php");
exit;
?>