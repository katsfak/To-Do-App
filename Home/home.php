<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To Do List</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php
    session_start();

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../LogIn/index.php");
        exit();
    }

    if (!isset($_SESSION['tasks'])) {
        $_SESSION['tasks'] = [];
    }

    // Handle logout
    if (isset($_POST['logout'])) {
        session_destroy();
        header("Location: ../LogIn/index.php");
        exit();
    }
    ?>

    <div class="header">
        <h1>To Do List</h1>
        <div class="user-info">
            <span class="welcome-text">Welcome, <?php echo htmlspecialchars($_SESSION['fullname'] ?? 'User'); ?>!</span>
            <div class="nav-buttons">
                <a href="../Profile/profile.php" class="profile-btn">Profile Settings</a>
                <form method="post" style="display: inline;">
                    <button type="submit" name="logout" class="logout-btn">Logout</button>
                </form>
            </div>
        </div>
    </div>
    <?php
    // Add new task
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
        $task = trim($_POST['task']);
        if ($task !== '') {
            $_SESSION['tasks'][] = ['text' => htmlspecialchars($task), 'done' => false];
        }
    }

    // Delete task
    if (isset($_POST['delete'])) {
        $index = (int)$_POST['delete'];
        if (isset($_SESSION['tasks'][$index])) {
            array_splice($_SESSION['tasks'], $index, 1);
        }
    }

    // Mark as done/undone
    if (isset($_POST['done'])) {
        $index = (int)$_POST['done'];
        if (isset($_SESSION['tasks'][$index])) {
            $_SESSION['tasks'][$index]['done'] = !$_SESSION['tasks'][$index]['done'];
        }
    }

    // Edit task
    if (isset($_POST['edit']) && isset($_POST['edit_task'])) {
        $index = (int)$_POST['edit'];
        $newText = trim($_POST['edit_task']);
        if ($newText !== '' && isset($_SESSION['tasks'][$index])) {
            $_SESSION['tasks'][$index]['text'] = htmlspecialchars($newText);
        }
    }
    ?>

    <div class="todo-container">
        <form method="post" action="" class="add-form">
            <input type="text" name="task" placeholder="Add new task" required>
            <button type="submit" name="add" class="add-btn">Add Task</button>
        </form>

        <?php if (empty($_SESSION['tasks'])): ?>
            <div class="empty-state">
                <h3>No tasks yet!</h3>
                <p>Add your first task above to get started.</p>
            </div>
        <?php else: ?>
            <ul class="task-list">
                <?php foreach ($_SESSION['tasks'] as $i => $task): ?>
                    <li class="task-item">
                        <?php if (isset($_POST['edit_mode']) && $_POST['edit_mode'] == $i): ?>
                            <form method="post" style="display: flex; width: 100%; align-items: center; gap: 0.5rem;">
                                <input type="text" name="edit_task" value="<?php echo $task['text']; ?>" required class="edit-input">
                                <button type="submit" name="edit" value="<?php echo $i; ?>" class="btn btn-save">Save</button>
                                <button type="submit" class="btn btn-cancel">Cancel</button>
                            </form>
                        <?php else: ?>
                            <div class="task-content">
                                <span class="task-text <?php echo $task['done'] ? 'done' : ''; ?>">
                                    <?php echo $task['text']; ?>
                                </span>
                            </div>
                            <div class="task-actions">
                                <form method="post" style="display: inline;">
                                    <button type="submit" name="done" value="<?php echo $i; ?>" class="btn <?php echo $task['done'] ? 'btn-undone' : 'btn-done'; ?>">
                                        <?php echo $task['done'] ? 'Undone' : 'Done'; ?>
                                    </button>
                                </form>
                                <form method="post" style="display: inline;">
                                    <button type="submit" name="edit_mode" value="<?php echo $i; ?>" class="btn btn-edit">Edit</button>
                                </form>
                                <form method="post" style="display: inline;">
                                    <button type="submit" name="delete" value="<?php echo $i; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this task?')">Delete</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>

</html>