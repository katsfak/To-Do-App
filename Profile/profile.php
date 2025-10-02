<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../LogIn/index.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "your_database_name");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user info
$user_id = $_SESSION['user_id'];
$sql = "SELECT fullname, username, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($fullname, $username, $email);
$stmt->fetch();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_fullname = $_POST['fullname'];
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $update_sql = "UPDATE users SET fullname = ?, username = ?, email = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssi", $new_fullname, $new_username, $new_email, $user_id);
    $update_stmt->execute();
    $update_stmt->close();
    // Update session variables
    $_SESSION['fullname'] = $new_fullname;
    $_SESSION['username'] = $new_username;
    $_SESSION['email'] = $new_email;
    // Refresh page to show updated info
    header("Location: profile.php");
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="header">
        <h1>User Profile</h1>
    </div>
    <div class="user-info">
        <form method="post" action="">
            <p>Full Name: <input type="text" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>"></p>
            <p>Username: <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>"></p>
            <p>Email: <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>"></p>
            <button type="submit">Update</button>
        </form>
    </div>
</body>
</html>