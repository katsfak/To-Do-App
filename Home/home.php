<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To Do List</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>To Do List</h1>
    <form method="post" action="">
        <input type="text" name="task" placeholder="Add new task" required>
        <button type="submit">Add</button>
    </form>
    <ul>
        <?php
        session_start();
        if (!isset($_SESSION['tasks'])) {
            $_SESSION['tasks'] = [];
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['task'])) {
            $_SESSION['tasks'][] = htmlspecialchars($_POST['task']);
        }
        foreach ($_SESSION['tasks'] as $task) {
            echo "<li>" . $task . "</li>";
        }
        ?>
    </ul>
</body>
</html>