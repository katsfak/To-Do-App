<?php
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $errors[] = "All fields are required.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    } else {
        if (empty($errors)) {
            require_once '../config.php';

            if ($conn) {
                $stmt = $conn->prepare("SELECT id, fullname, password FROM users WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $stmt->bind_result($id, $fullname, $hashed_password);
                    $stmt->fetch();

                    if (password_verify($password, $hashed_password) === true) {
                        print "<pre>D: $id, Pas: $password, Hashed Password: $hashed_password</pre>";
                        // session_start();
                        // $_SESSION['user_id'] = $id;
                        // $_SESSION['username'] = $username;
                        // $_SESSION['fullname'] = $fullname;
                        // $_SESSION['tasks'] = $_SESSION['tasks'] ?? [];
                        // header("Location: ../Home/home.php");
                        // exit();
                    } else {
                        $errors[] = "Invalid username or password.";
                        print "<pre>Password verification failed</pre>";
                    }
                } else {
                    $errors[] = "Invalid username or password.";
                    print "<pre>User not found</pre>";
                }
            } else {
                $errors[] = "Database connection failed. Please try again later.";
                print "<pre>Database connection failed</pre>";
            }
            $stmt->close();
            $conn->close();
        } else {
            $errors[] = "Database connection failed. Please try again later.";
            print "<pre>Errors: " . print_r($errors, true) . "</pre>";
        }
    }
}

?>
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

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error animate-shake">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="index.php" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>

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