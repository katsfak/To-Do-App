<?php
session_start();
$name = $_SESSION['fullname'] ?? 'Guest';
$id = $_SESSION['user_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - To Do List</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="header">
        <h1>To Do List</h1>
        <div class="user-info">
            Welcome, <?php echo htmlspecialchars($name); ?>
            <a href="../Profile/profile.php">[Profile Settings]</a>
            <a href="../logout.php">[Logout]</a>
        </div>
    </div>

    <div class="container">
        <div class="todo-section">
            <form method="POST" action="">
                <input type="text" name="task" placeholder="Enter a new task..." required>
                <button type="submit" name="add_task">Add Task</button>
            </form>

            <?php
            // Initialize tasks in session if not exists
            if (!isset($_SESSION['tasks'])) {
                $_SESSION['tasks'] = [];
            }

            // Handle adding new task
            if (isset($_POST['add_task']) && !empty($_POST['task'])) {
                $task = htmlspecialchars(trim($_POST['task']));
                $task_id = uniqid();
                $_SESSION['tasks'][$task_id] = [
                    'task' => $task,
                    'completed' => false,
                    'created' => date('Y-m-d H:i:s')
                ];
            }

            // Handle task completion toggle
            if (isset($_POST['toggle_task'])) {
                $task_id = $_POST['task_id'];
                if (isset($_SESSION['tasks'][$task_id])) {
                    $_SESSION['tasks'][$task_id]['completed'] = !$_SESSION['tasks'][$task_id]['completed'];
                }
            }

            // Handle task deletion
            if (isset($_POST['delete_task'])) {
                $task_id = $_POST['task_id'];
                unset($_SESSION['tasks'][$task_id]);
            }
            ?>

            <div class="tasks-list">
                <?php if (empty($_SESSION['tasks'])): ?>
                    <p class="no-tasks">No tasks yet. Add one above!</p>
                <?php else: ?>
                    <?php foreach ($_SESSION['tasks'] as $task_id => $task_data): ?>
                        <div class="task-item <?php echo $task_data['completed'] ? 'completed' : ''; ?>">
                            <span class="task-text"><?php echo htmlspecialchars($task_data['task']); ?></span>
                            <div class="task-actions">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="task_id" value="<?php echo $task_id; ?>">
                                    <button type="submit" name="toggle_task" class="toggle-btn">
                                        <?php echo $task_data['completed'] ? 'Undo' : 'Complete'; ?>
                                    </button>
                                </form>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="task_id" value="<?php echo $task_id; ?>">
                                    <button type="submit" name="delete_task" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>