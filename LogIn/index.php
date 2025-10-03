<?php
// Initialize error message variable
$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Check if form is submitted
    if (empty($_POST['username']) || empty($_POST['password'])) { // Validate required fields
        $error_msg = "Both username and password are required.";
    } else {
        require_once '../config.php'; // Include database configuration

        // Prepare SQL statement to prevent SQL injection
        $sql = "SELECT id, fullname, password FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $_POST['username']); // Bind username parameter
        mysqli_stmt_execute($stmt); // Execute statement
        $result = mysqli_stmt_get_result($stmt); // Get result

        if ($row = mysqli_fetch_assoc($result)) { // Check if user exists
            // Verify password using password_hash
            if (password_verify($_POST['password'], $row['password'])) {
                session_start(); // Start session
                $_SESSION['user_id'] = $row['id']; // Store user ID in session
                $_SESSION['username'] = $_POST['username']; // Store username in session
                $_SESSION['fullname'] = $row['fullname']; // Store full name in session
                header("Location: ../Home/home.php"); // Redirect to home page
                exit();
            } else {
                $error_msg = "Incorrect password."; // Password does not match
            }
        } else {
            $error_msg = "Username not found."; // Username does not exist
        }
        mysqli_stmt_close($stmt); // Close statement
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
            <!-- Display error message if exists -->
            <p class="error-msg"><?php echo htmlspecialchars($error_msg); ?></p>
        <?php endif; ?>

        <!-- Login form -->
        <form action="index.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>

        <hr>
        <!-- Link to registration page -->
        <p>Don't have an account? <a href="../Register/signup.php">Sign up here</a></p>
    </div>
</body>

</html>