<?php 
// Lidhja me databazen permes PDO (eshte e ndare ne nje file tjeter per organizim)
require_once 'db.php';

// Merr vleren e filtrit (done, pending ose bosh)
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

// Krijojme dy variabla per te ndertuar kushtet ne menyre dinamike
$where = [];
$params = [];

// Nese perdoruesi ka shkruar ndonje fjale per kerkim, shtohet si kusht ne query
if (!empty($search)) {
    $where[] = "title LIKE :search";
    $params[':search'] = "%$search%";
}

// Shtohet filter shtese nese eshte zgjedhur "done" apo "pending"
if ($filter === 'done') {
    $where[] = "completed = 1";
} elseif ($filter === 'pending') {
    $where[] = "completed = 0";
}

// Nese ka ndonje kusht, ndertojme string-un final WHERE
$whereSql = '';
if (!empty($where)) {
    $whereSql = 'WHERE ' . implode(' AND ', $where);
}

// Krijojme query per te marre te gjitha detyrat, me filtrat e aplikuar
$sql = "SELECT * FROM tasks $whereSql ORDER BY created_at DESC";
$stmt = $conn->prepare($sql); // Pergatitja e query-t

// Lidhja e parametrave te kerkimit ne menyre te sigurt permes bindValue
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}

$stmt->execute(); // Ekzekutohet query
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC); // Rezultatet ruhen ne forme array associative
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
     
            <!-- Mesazhi i gabimit nese perdoruesi ka futur nje titull te pavlefshem -->
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>

            <!-- Forma per kerkim + filtrimi (All, Done, Pending) -->
            <form method="GET" class="mb-4">
                <div class="row g-2">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control" placeholder="Search a task..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="filter" class="form-select">
                            <option value="">All</option>
                            <option value="done" <?= (isset($_GET['filter']) && $_GET['filter'] === 'done') ? 'selected' : '' ?>>Done</option>
                            <option value="pending" <?= (isset($_GET['filter']) && $_GET['filter'] === 'pending') ? 'selected' : '' ?>>Pending</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-outline-primary w-100">Search</button>
                        <a href="index.php" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>

            <!-- Butoni per fshirje te detyrave te perfunduara -->
            <div class="d-flex justify-content-end mb-4">
                <form action="clear_completed.php" method="POST">
                    <button type="submit" class="btn btn-danger btn-sm"
                        onclick="return confirm('Are you sure you want to delete all completed tasks?')">
                        Delete all completed tasks
                    </button>
                </form>
            </div>

            <!-- Shfaqja e çdo detyre + butonat per veprim -->
            <ul class="list-group">
                <!-- Forma per shtim te detyres se re -->
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

                                <form action="edit.php" method="GET">
                                    <input type="hidden" name="id" value="<?= $task['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Edit</button>
                                </form>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </body>
</html>