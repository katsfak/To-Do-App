<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="login-container">
        <h2>Log In</h2>
        <?php
        $username = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            $errors = array();

            if (empty($username) || empty($password)) {
                array_push($errors, "All fields are required.");
            }
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    echo "<div class='alert alert-error'>$error</div>";
                }
            } else {
                require_once '../config.php';

                // Check database connection
                if (!$conn) {
                    array_push($errors, "Database connection failed. Please try again later.");
                } else {
                    // Fetch user by username
                    $stmt = $conn->prepare("SELECT user_id, fullname, username, email, password FROM users WHERE username = ?");
                    if (!$stmt) {
                        array_push($errors, "Database error occurred. Please try again later.");
                    } else {
                        $stmt->bind_param("s", $username);
                        $stmt->execute();
                        $stmt->store_result();
                        if ($stmt->num_rows == 1) {
                            $stmt->bind_result($user_id, $fullname, $db_username, $email, $hashed_password);
                            $stmt->fetch();
                            if (password_verify($password, $hashed_password)) {
                                // Password is correct, start a session
                                session_start();
                                $_SESSION['user_id'] = $user_id;
                                $_SESSION['username'] = $db_username;
                                $_SESSION['fullname'] = $fullname;
                                $_SESSION['email'] = $email;

                                $stmt->close();
                                $conn->close();
                                header("Location: ../Home/home.php");
                                exit();
                            } else {
                                array_push($errors, "Invalid username or password.");
                            }
                        } else {
                            array_push($errors, "Invalid username or password.");
                        }
                        $stmt->close();
                    }
                    $conn->close();
                }
            }

            // Display errors if any occurred after database operations
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    echo "<div class='alert alert-error'>$error</div>";
                }
            }
        }
        ?>

        <form action="index.php" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="login-btn">Log In</button>
        </form>
        <p>Don't have an account? <a href="../Register/signup.php">Sign Up</a></p>
    </div>
</body>

</html>