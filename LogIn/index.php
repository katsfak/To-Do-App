<?php
echo "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "<br>";
echo "<pre>POST: " . print_r($_POST, true) . "</pre>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['username']) || empty($_POST['password'])) {
        echo "Error: Both username and password are required.<br>";
    } else {

        require_once '../config.php';
        echo "Database connection established.<br>";
        echo "Password: " . htmlspecialchars($_POST['password']) . "<br>";
        $sql = "SELECT * FROM users WHERE username = '" . $_POST['username'] . "'";
        $result = mysqli_query($conn, $sql);
        $num = mysqli_num_rows($result);
        if ($num == 1) {
            while ($row = mysqli_fetch_assoc($result)) {
                $id = $row['id'];
                $fullname = $row['fullname'];
                $hashed_password = $row['password'];
                echo "Fetched user data. password: " . htmlspecialchars($hashed_password) . "<br>";
                echo "Password: " . htmlspecialchars($_POST['password']) . "<br>";

                if (password_verify($_POST['password'], $hashed_password)) {
                    echo "Password verified successfully.<br>";
                    $login_success = true;
                    session_start();
                    $_SESSION['user_id'] = $id;
                    $_SESSION['username'] = $_POST['username'];
                    $_SESSION['fullname'] = $fullname;
                    header("Location: ../Home/home.php");
                    exit();
                } else {
                    echo "Error: Incorrect password.<br>";
                }
            }
        } else {
            echo "Error: Username not found.<br>";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>
    <form action="index.php" method="post">
        <label>Username: <input type="text" name="username"></label><br>
        <label>Password: <input type="password" name="password"></label><br>
        <button type="submit">Submit</button>
    </form>
</body>

</html>