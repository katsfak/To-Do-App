<?php
echo "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "<br>";
echo "<pre>POST: " . print_r($_POST, true) . "</pre>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Basic validation
    if (empty($_POST['name']) || empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['confirm_password'])) {
        echo "Error: All fields are required.<br>";
        exit;
    }

    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        echo "Error: Invalid email format.<br>";
        exit;
    }

    if ($_POST['password'] !== $_POST['confirm_password']) {
        echo "Error: Passwords do not match.<br>";
        exit;
    }

    if (strlen($_POST['password']) < 6) {
        echo "Error: Password must be at least 6 characters long.<br>";
        exit;
    }

    require_once '../config.php';
    echo "Database connection established.<br>";

    if ($conn) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $_POST['username'], $_POST['email']);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "Error: Username or email already taken.<br>";
        } else {
            $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            echo "Password: " . htmlspecialchars($_POST['password']) . "<br>";
            echo "Hashed Password: " . htmlspecialchars($hashed_password) . "<br>";

            $created_at = date("Y-m-d H:i:s");

            $stmt = $conn->prepare("INSERT INTO users (fullname, username, email, password, created_at) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $_POST['name'], $_POST['username'], $_POST['email'], $hashed_password, $created_at);

            if ($stmt->execute()) {
                echo "Registration successful! <a href='../LogIn/index.php'>Log in here</a>.<br>";
            } else {
                echo "Error: Could not register. Please try again.<br>";
            }
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Error: Database connection failed.<br>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign Up Test</title>
</head>

<body>
    <form action="signup.php" method="post">
        <label>Full Name: <input type="text" name="name" required></label><br>
        <label>Username: <input type="text" name="username" required></label><br>
        <label>Email: <input type="email" name="email" required></label><br>
        <label>Password: <input type="password" name="password" required></label><br>
        <label>Confirm Password: <input type="password" name="confirm_password" required></label><br>
        <button type="submit">Sign Up</button>
    </form>
</body>

</html>