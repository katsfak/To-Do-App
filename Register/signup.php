<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="signup-container">
        <h2>Sign Up</h2>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            $errors = array();

            if (empty($name) || empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
                array_push($errors, "All fields are required.");
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                array_push($errors, "Invalid email format.");
            }
            if ($password !== $confirm_password) {
                array_push($errors, "Passwords do not match.");
                
            }
            if (strlen($password) < 6) {
                array_push($errors, "Password must be at least 6 characters long.");
            }
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    echo "<div class='alert alert-danger'>$error</div>";
                }
            } else {
                require_once '../config.php';
                // Check if username or email already exists
                $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
                $stmt->bind_param("ss", $username, $email);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $errors[] = "Username or email already taken.";
                } else {
                    // Hash the password
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                    $created_at = date("Y-m-d H:i:s");
                    // Insert new user
                    $stmt = $conn->prepare("INSERT INTO users (fullname, username, email, password, created_at) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssss", $name, $username, $email, $hashed_password, $created_at);
                    if ($stmt->execute()) {
                        echo "<p class='success'>Registration successful! <a href='../LogIn/index.php'>Log in here</a>.</p>";
                    } else {
                        array_push($errors, "Error during registration. Please try again.");
                    }
                }
                $stmt->close();
            }

            $conn->close();
            $stmt->close();
        }
        ?>
        <form action="signup.php" method="post">
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="signup-btn">Sign Up</button>
        </form>
        <p>Already have an account? <a href="../LogIn/index.php">Log In</a></p>
    </div>
</body>

</html>