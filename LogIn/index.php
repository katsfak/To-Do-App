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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $errors = array();

            if (empty($username) || empty($password)) {
                array_push($errors, "All fields are required.");
            }
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    echo "<div class='alert alert-danger'>$error</div>";
                }
            } else {
                require_once '../config.php';
                // Fetch user by username
                $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($user_id, $fullname, $username, $email, $hashed_password);
                    $stmt->fetch();
                    if (password_verify($password, $hashed_password)) {
                        // Password is correct, start a session
                        session_start();
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['username'] = $username;
                        $_SESSION['fullname'] = $fullname;
                        $_SESSION['email'] = $email;
                        
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