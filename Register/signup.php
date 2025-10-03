<?php
// Initialize error and success message variables
$error_msg = "";
$success_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic validation for required fields
    if (empty($_POST['name']) || empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['confirm_password'])) {
        $error_msg = "All fields are required.";
    } 
    // Validate email format
    elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Invalid email format.";
    } 
    // Check if passwords match
    elseif ($_POST['password'] !== $_POST['confirm_password']) {
        $error_msg = "Passwords do not match.";
    } 
    // Check password length
    elseif (strlen($_POST['password']) < 6) {
        $error_msg = "Password must be at least 6 characters long.";
    } 
    else {
        // Include database configuration
        require_once '../config.php';

        if ($conn) {
            // Check if username or email already exists in the database
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $_POST['username'], $_POST['email']);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error_msg = "Username or email already taken.";
            } else {
                // Hash the password for security
                $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $created_at = date("Y-m-d H:i:s");

                // Insert new user into the database
                $stmt = $conn->prepare("INSERT INTO users (fullname, username, email, password, created_at) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $_POST['name'], $_POST['username'], $_POST['email'], $hashed_password, $created_at);

                if ($stmt->execute()) {
                    $success_msg = "Registration successful!";
                } else {
                    $error_msg = "Could not register. Please try again.";
                }
            }

            // Close statement and connection
            $stmt->close();
            $conn->close();
        } else {
            $error_msg = "Database connection failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="header">
        <h1>Sign Up</h1>
    </div>
    <div class="user-info">
        <!-- Display error message if any -->
        <?php if (!empty($error_msg)): ?>
            <p class="error-msg"><?php echo htmlspecialchars($error_msg); ?></p>
        <?php endif; ?>
        <!-- Display success message if any -->
        <?php if (!empty($success_msg)): ?>
            <p class="success-msg"><?php echo htmlspecialchars($success_msg); ?></p>
        <?php endif; ?>

        <!-- Registration form -->
        <form action="signup.php" method="post">
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit">Sign Up</button>
        </form>

        <hr>
        <p>Already have an account? <a href="../LogIn/index.php">Login here</a></p>
    </div>
</body>

</html>