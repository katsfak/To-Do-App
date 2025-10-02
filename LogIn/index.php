<?php
$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['username']) || empty($_POST['password'])) {
        $error_msg = "Both username and password are required.";
    } else {
        require_once '../config.php';

        // Use prepared statement to prevent SQL injection
        $sql = "SELECT id, fullname, password FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $_POST['username']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($_POST['password'], $row['password'])) {
                session_start();
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $_POST['username'];
                $_SESSION['fullname'] = $row['fullname'];
                header("Location: ../Home/home.php");
                exit();
            } else {
                $error_msg = "Incorrect password.";
            }
        } else {
            $error_msg = "Username not found.";
        }
        mysqli_stmt_close($stmt);
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="header">
        <h1>Login</h1>
    </div>
    <div class="user-info">
        <?php if (!empty($error_msg)): ?>
            <p class="error-msg"><?php echo htmlspecialchars($error_msg); ?></p>
        <?php endif; ?>

        <form action="index.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>

        <hr>
        <p>Don't have an account? <a href="../Register/signup.php">Sign up here</a></p>
    </div>
</body>

</html>