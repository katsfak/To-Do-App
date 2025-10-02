<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../LogIn/index.php");
    exit();
}

require_once '../config.php';
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT fullname, username, email, password FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($fullname, $username, $email, $db_password);
$stmt->fetch();
$stmt->close();

$error_msg = "";
$success_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_fullname = trim($_POST['fullname']);
    $new_username = trim($_POST['username']);
    $new_email = trim($_POST['email']);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';

    // Έλεγχος username
    $check_sql = "SELECT id FROM users WHERE username = ? AND id != ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("si", $new_username, $user_id);
    $check_stmt->execute();
    $check_stmt->store_result();
    if ($check_stmt->num_rows > 0) {
        $error_msg = "Username already exists!";
        $check_stmt->close();
    } else {
        $check_stmt->close();

        // Έλεγχος email
        $check_sql = "SELECT id FROM users WHERE email = ? AND id != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("si", $new_email, $user_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        if ($check_stmt->num_rows > 0) {
            $error_msg = "Email already exists!";
            $check_stmt->close();
        } else {
            $check_stmt->close();

            // Έλεγχος αλλαγής password
            $update_password = false;
            if (!empty($current_password) || !empty($new_password)) {
                if (strlen($new_password) < 6) {
                    $error_msg = "New password must be at least 6 characters.";
                } elseif (!password_verify($current_password, $db_password)) {
                    $error_msg = "Current password is incorrect.";
                } else {
                    $update_password = true;
                    $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                }
            }

            // Αν δεν υπάρχει error, ενημέρωση στη βάση
            if (empty($error_msg)) {
                if ($update_password) {
                    $update_sql = "UPDATE users SET fullname = ?, username = ?, email = ?, password = ? WHERE id = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("ssssi", $new_fullname, $new_username, $new_email, $hashed_new_password, $user_id);
                } else {
                    $update_sql = "UPDATE users SET fullname = ?, username = ?, email = ? WHERE id = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("sssi", $new_fullname, $new_username, $new_email, $user_id);
                }
                $update_stmt->execute();
                $update_stmt->close();

                // Ενημέρωση session
                $_SESSION['fullname'] = $new_fullname;
                $_SESSION['username'] = $new_username;
                $_SESSION['email'] = $new_email;

                // Επιτυχής ενημέρωση
                $success_msg = "Profile updated successfully!";
                $fullname = $new_fullname;
                $username = $new_username;
                $email = $new_email;

                // Redirect στο home
                header("Location: ../Home/home.php");
                exit();
            }
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="header">
        <h1>User Profile</h1>
    </div>

    <div class="user-info">
        <?php
        if (!empty($error_msg)) echo "<p class='error-msg'>$error_msg</p>";
        if (!empty($success_msg)) echo "<p class='success-msg'>$success_msg</p>";
        ?>
        <form method="post" action="">
            <label>Full Name:</label>
            <input type="text" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" required>

            <label>Username:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

            <hr>
            <p><strong>Change Password</strong></p>
            <label>Current Password:</label>
            <input type="password" name="current_password">

            <label>New Password:</label>
            <input type="password" name="new_password">

            <button type="submit">Update</button>
        </form>
    </div>
</body>

</html>