<?php
session_start();
$name = $_SESSION['fullname'] ?? 'Guest';
$id = $_SESSION['user_id'] ?? null;

// Initialize projects if not exists
if (!isset($_SESSION['projects'])) {
    $_SESSION['projects'] = [];
}

// HANDLE ADD PROJECT
if (isset($_POST['add_project']) && !empty($_POST['project_name'])) {
    $project_name = htmlspecialchars(trim($_POST['project_name']));
    $project_id = uniqid();
    $_SESSION['projects'][$project_id] = [
        'name' => $project_name,
        'tasks' => []
    ];
}

// HANDLE DELETE PROJECT
if (isset($_POST['delete_project'])) {
    $project_id = $_POST['project_id'];
    unset($_SESSION['projects'][$project_id]);
}

// HANDLE EDIT PROJECT
if (isset($_POST['edit_project']) && !empty($_POST['edited_project'])) {
    $project_id = $_POST['project_id'];
    $_SESSION['projects'][$project_id]['name'] = htmlspecialchars(trim($_POST['edited_project']));
}

// HANDLE ADD TASK
if (isset($_POST['add_task']) && !empty($_POST['task']) && isset($_POST['project_id'])) {
    $task = htmlspecialchars(trim($_POST['task']));
    $task_id = uniqid();
    $project_id = $_POST['project_id'];
    $_SESSION['projects'][$project_id]['tasks'][$task_id] = [
        'task' => $task,
        'completed' => false,
        'created' => date('Y-m-d H:i:s')
    ];
}

// HANDLE DELETE TASK
if (isset($_POST['delete_task'])) {
    $project_id = $_POST['project_id'];
    $task_id = $_POST['task_id'];
    unset($_SESSION['projects'][$project_id]['tasks'][$task_id]);
}

// HANDLE TOGGLE TASK
if (isset($_POST['toggle_task'])) {
    $project_id = $_POST['project_id'];
    $task_id = $_POST['task_id'];
    $_SESSION['projects'][$project_id]['tasks'][$task_id]['completed'] =
        !$_SESSION['projects'][$project_id]['tasks'][$task_id]['completed'];
}

// HANDLE EDIT TASK
if (isset($_POST['edit_task']) && !empty($_POST['edited_task'])) {
    $project_id = $_POST['project_id'];
    $task_id = $_POST['task_id'];
    $_SESSION['projects'][$project_id]['tasks'][$task_id]['task'] = htmlspecialchars(trim($_POST['edited_task']));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects & Tasks</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="header">
        <h1>Welcome, <?php echo htmlspecialchars($name); ?></h1>
        <div class="user-info">
            <a href="../Profile/profile.php">[Profile Settings]</a>
            <a href="../logout.php">[Logout]</a>
        </div>
    </div>

    <div class="container">
        <div class="userinfo">
            <h2>Add New Project</h2>
            <form method="POST">
                <label for="project_name">Project Name:</label>
                <input type="text" id="project_name" name="project_name" placeholder="Enter project name" required>
                <button type="submit" name="add_project">Add Project</button>
            </form>
        </div>

        <?php if (empty($_SESSION['projects'])): ?>
            <div class="no-projects">
                <p>No projects yet. Create your first project above!</p>
            </div>
        <?php else: ?>
            <?php foreach ($_SESSION['projects'] as $project_id => $project): ?>
                <div class="project">
                    <div class="project-header">
                        <h3 class="project-name"><?php echo htmlspecialchars($project['name']); ?></h3>
                        <div class="project-actions">
                            <!-- Edit Project -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                                <input type="text" name="edited_project" placeholder="New name" required>
                                <button type="submit" name="edit_project" class="edit-btn">Save</button>
                            </form>
                            <!-- Delete Project -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                                <button type="submit" name="delete_project" class="delete-btn" onclick="return confirm('Delete this project and all its tasks?')">Delete</button>
                            </form>
                        </div>
                    </div>

                    <h4>Add Task</h4>
                    <form method="POST">
                        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                        <label for="task_<?php echo $project_id; ?>">Task Description:</label>
                        <input type="text" id="task_<?php echo $project_id; ?>" name="task" placeholder="Enter task description" required>
                        <button type="submit" name="add_task">Add Task</button>
                    </form>

                    <?php if (empty($project['tasks'])): ?>
                        <p>No tasks yet.</p>
                    <?php else: ?>
                        <?php foreach ($project['tasks'] as $task_id => $task): ?>
                            <div class="task-item <?php echo $task['completed'] ? 'completed' : ''; ?>">
                                <span class="task-text"><?php echo htmlspecialchars($task['task']); ?></span>
                                <div class="task-actions">
                                    <!-- Toggle -->
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                                        <input type="hidden" name="task_id" value="<?php echo $task_id; ?>">
                                        <button type="submit" name="toggle_task" class="edit-btn"><?php echo $task['completed'] ? 'Undo' : 'Complete'; ?></button>
                                    </form>
                                    <!-- Edit -->
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                                        <input type="hidden" name="task_id" value="<?php echo $task_id; ?>">
                                        <input type="text" name="edited_task" placeholder="New task description" required>
                                        <button type="submit" name="edit_task" class="edit-btn">Save</button>
                                    </form>
                                    <!-- Delete -->
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                                        <input type="hidden" name="task_id" value="<?php echo $task_id; ?>">
                                        <button type="submit" name="delete_task" class="delete-btn" onclick="return confirm('Delete this task?')">Delete</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>

</html>