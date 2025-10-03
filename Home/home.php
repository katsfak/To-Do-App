<?php
session_start();
require '../config.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch user info
$name = $_SESSION['fullname'] ?? 'Guest';
$user_id = $_SESSION['user_id'];

// ADD PROJECT
if (isset($_POST['add_project']) && !empty($_POST['project_name'])) {
    $project_name = trim($_POST['project_name']);
    $stmt = $conn->prepare("INSERT INTO projects (user_id, name) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $project_name);
    $stmt->execute();
}

// DELETE PROJECT
if (isset($_POST['delete_project'])) {
    $project_id = (int)$_POST['project_id'];
    $stmt = $conn->prepare("DELETE FROM projects WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $project_id, $user_id);
    $stmt->execute();
}

// EDIT PROJECT
if (isset($_POST['edit_project']) && !empty($_POST['edited_project'])) {
    $project_id = (int)$_POST['project_id'];
    $newname = trim($_POST['edited_project']);
    $stmt = $conn->prepare("UPDATE projects SET name=? WHERE id=? AND user_id=?");
    $stmt->bind_param("sii", $newname, $project_id, $user_id);
    $stmt->execute();
}

// ADD TASK
if (isset($_POST['add_task']) && !empty($_POST['task']) && isset($_POST['project_id'])) {
    $task = trim($_POST['task']);
    $project_id = (int)$_POST['project_id'];
    $stmt = $conn->prepare("INSERT INTO tasks (project_id, title) VALUES (?, ?)");
    $stmt->bind_param("is", $project_id, $task);
    $stmt->execute();
}

// DELETE TASK
if (isset($_POST['delete_task'])) {
    $task_id = (int)$_POST['task_id'];
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id=?");
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
}

// TOGGLE TASK (completed/not completed)
if (isset($_POST['toggle_task'])) {
    $task_id = (int)$_POST['task_id'];
    $stmt = $conn->prepare("UPDATE tasks SET completed = NOT completed WHERE id=?");
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
}

// EDIT TASK
if (isset($_POST['edit_task']) && !empty($_POST['edited_task'])) {
    $task_id = (int)$_POST['task_id'];
    $newtask = trim($_POST['edited_task']);
    $stmt = $conn->prepare("UPDATE tasks SET title=? WHERE id=?");
    $stmt->bind_param("si", $newtask, $task_id);
    $stmt->execute();
}

// GET PROJECTS
$projects = $conn->query("SELECT * FROM projects WHERE user_id=$user_id ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Projects & Tasks</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h1>Welcome, <?php echo htmlspecialchars($name); ?></h1>
        <div class="user-info">
            <a href="../Profile/profile.php">[Profile Settings]</a>
            <a href="../logout.php">[Logout]</a>
        </div>
    </div>

    <!-- Main Container -->
    <div class="container">
        <div class="userinfo">
            <h2>Add New Project</h2>
            <form method="POST">
                <label for="project_name">Project Name:</label>
                <input type="text" id="project_name" name="project_name" required>
                <button type="submit" name="add_project">Add Project</button>
            </form>
        </div>

        <!-- Projects List -->
        <?php if ($projects->num_rows == 0): ?>
            <div class="no-projects">
                <p>No projects yet. Create your first project above!</p>
            </div>
        <?php else: ?>
            <?php while ($project = $projects->fetch_assoc()): ?>
                <?php
                $project_id = $project['id'];
                $tasks = $conn->query("SELECT * FROM tasks WHERE project_id=$project_id ORDER BY id DESC");
                ?>
                <!-- Project Block -->
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
                    <!-- Tasks List -->
                    <h4>Add Task</h4>
                    <form method="POST">
                        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                        <input type="text" name="task" placeholder="Enter task description" required>
                        <button type="submit" name="add_task">Add Task</button>
                    </form>
                    <!-- Tasks Display -->
                    <?php if ($tasks->num_rows == 0): ?>
                        <p>No tasks yet.</p>
                    <?php else: ?>
                        <?php while ($task = $tasks->fetch_assoc()): ?>
                            <div class="task-item <?php echo $task['completed'] ? 'completed' : ''; ?>">
                                <span class="task-text"><?php echo htmlspecialchars($task['title']); ?></span>
                                <div class="task-actions">
                                    <!-- Toggle -->
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                        <button type="submit" name="toggle_task" class="edit-btn">
                                            <?php echo $task['completed'] ? 'Undo' : 'Complete'; ?>
                                        </button>
                                    </form>
                                    <!-- Edit -->
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                        <input type="text" name="edited_task" placeholder="New task description" required>
                                        <button type="submit" name="edit_task" class="edit-btn">Save</button>
                                    </form>
                                    <!-- Delete -->
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                        <button type="submit" name="delete_task" class="delete-btn" onclick="return confirm('Delete this task?')">Delete</button>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</body>

</html>