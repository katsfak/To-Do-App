<?php
session_start();
$name = $_SESSION['fullname'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - To Do List</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="header">
        <h1>To Do List</h1>
        <div class="user-info">
            Welcome, <?php echo htmlspecialchars($name); ?>
            <a href="../Profile/profile.php">[Profile Settings]</a>
            <a href="../Auth/logout.php">[Logout]</a>
        </div>
    </div>
</body>

</html>