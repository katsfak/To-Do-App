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
    }
    if (empty($errors)) {
        require_once '../config.php';

        if ($conn) {
            $stmt = $conn->prepare("SELECT id, fullname, username, email, password FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();
            echo "i am here";

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($user_id, $fullname, $db_username, $email, $hashed_password);
                $stmt->fetch();
                echo $hashed_password;
                echo $password;
                print "i am here1";

                if (password_verify($password, $hashed_password) == true) {
                    session_start();

                    $_SESSION['username'] = $db_username;
                    $_SESSION['fullname'] = $fullname;
                    $_SESSION['email'] = $email;
                    print "i am here2";
                    header("Location: ../Home/home.php");
                    exit();
                } else {
                    $errors[] = "Invalid username or password.";
                    print "i am here3";
                }
            } else {
                $errors[] = "Invalid username or password.";
                print "i am here4";
            }

            $stmt->close();
        } else {
            $errors[] = "Database error occurred. Please try again later.";
        }
        $conn->close();
    } else {
        $errors[] = "Database connection failed. Please try again later.";
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