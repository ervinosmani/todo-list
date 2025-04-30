<?php 
require_once 'db.php';

//Merr te gjitha tasks nga databaza
$sql = "SELECT * FROM tasks ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>To-Do List</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container py-5">
            <h1 class="mb-4">My To-Do List</h1>

            <ul class="list-group">
                <form action="add.php" method="POST" class="mt-4">
                    <div class="input-group">
                        <input type="text" name="title" class="form-control" placeholder="add a task..." required>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </div>
                </form>

                <!-- Shfaqja e tasks nga databaza -->
                <?php foreach ($tasks as $task): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <span><?= htmlspecialchars($task['title']) ?></span>
                            <div class="d-flex gap-2">
                                <span class="badge bg-<?= $task['completed'] ? 'success' : 'warning' ?>">
                                    <?= $task['completed'] ? 'Done' : 'Pending' ?>
                                </span>
                                <form action="delete.php" method="POST">
                                    <input type="hidden" name="id" value="<?= $task['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>

                                <form action="toggle.php" method="POST">
                                    <input type="hidden" name="id" value="<?= $task['id'] ?>">
                                    <input type="hidden" name="completed" value="<?= $task['completed'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        <?= $task['completed'] ? 'Undo' : 'Mark Done' ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </body>
</html>