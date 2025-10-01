<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php
    session_start();

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../LogIn/index.php");
        exit();
    }

    // Handle logout
    if (isset($_POST['logout'])) {
        session_destroy();
        header("Location: ../LogIn/index.php");
        exit();
    }

    // Handle profile update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
        $fullname = trim($_POST['fullname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        $errors = array();
        $success = false;

        // Validate inputs
        if (empty($fullname) || empty($email)) {
            array_push($errors, "Full name and email are required.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($errors, "Please enter a valid email address.");
        }

        // If password change is requested
        if (!empty($new_password) || !empty($confirm_password) || !empty($current_password)) {
            if (empty($current_password)) {
                array_push($errors, "Current password is required to change password.");
            }
            if (empty($new_password)) {
                array_push($errors, "New password is required.");
            }
            if (strlen($new_password) < 6) {
                array_push($errors, "New password must be at least 6 characters long.");
            }
            if ($new_password !== $confirm_password) {
                array_push($errors, "New password and confirmation do not match.");
            }
        }

        if (count($errors) == 0) {
            require_once '../config.php';

            if (!$conn) {
                array_push($errors, "Database connection failed. Please try again later.");
            } else {
                // Verify current password if password change is requested
                if (!empty($new_password)) {
                    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
                    $stmt->bind_param("i", $_SESSION['user_id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user = $result->fetch_assoc();

                    if (!password_verify($current_password, $user['password'])) {
                        array_push($errors, "Current password is incorrect.");
                    } else {
                        // Update with new password
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $stmt = $conn->prepare("UPDATE users SET fullname = ?, email = ?, password = ? WHERE user_id = ?");
                        $stmt->bind_param("sssi", $fullname, $email, $hashed_password, $_SESSION['user_id']);
                    }
                    $stmt->close();
                } else {
                    // Update without password change
                    $stmt = $conn->prepare("UPDATE users SET fullname = ?, email = ? WHERE user_id = ?");
                    $stmt->bind_param("ssi", $fullname, $email, $_SESSION['user_id']);
                }

                if (count($errors) == 0) {
                    if ($stmt->execute()) {
                        $_SESSION['fullname'] = $fullname;
                        $_SESSION['email'] = $email;
                        $success = true;
                    } else {
                        array_push($errors, "Failed to update profile. Please try again.");
                    }
                    $stmt->close();
                }
                $conn->close();
            }
        }
    }

    // Get current user data
    require_once '../config.php';
    $stmt = $conn->prepare("SELECT fullname, username, email FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    ?>

    <div class="header">
        <h1>Profile Settings</h1>
        <div class="user-info">
            <span class="welcome-text">Hello, <?php echo htmlspecialchars($_SESSION['fullname'] ?? 'User'); ?>!</span>
            <div class="nav-buttons">
                <a href="../Home/home.php" class="home-btn">Back to To-Do</a>
                <form method="post" style="display: inline;">
                    <button type="submit" name="logout" class="logout-btn">Logout</button>
                </form>
            </div>
        </div>
    </div>

    <div class="profile-container">
        <?php if (isset($success) && $success): ?>
            <div class="alert alert-success">✅ Profile updated successfully!</div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <div class="alert alert-error">⚠️ <?php echo $error; ?></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="profile-info">
            <h2>Account Information</h2>
            <div class="info-item">
                <strong>Username:</strong> <?php echo htmlspecialchars($user_data['username']); ?>
                <span class="info-note">(Username cannot be changed)</span>
            </div>
        </div>

        <div class="profile-form">
            <h3>Update Profile</h3>
            <form method="post" action="">
                <div class="form-group">
                    <label for="fullname">Full Name:</label>
                    <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user_data['fullname']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                </div>

                <hr class="section-divider">

                <h4>Change Password (Optional)</h4>
                <p class="password-note">Leave password fields empty if you don't want to change your password.</p>

                <div class="form-group">
                    <label for="current_password">Current Password:</label>
                    <input type="password" id="current_password" name="current_password">
                </div>

                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password">
                </div>

                <button type="submit" name="update_profile" class="update-btn">Update Profile</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const newPassword = document.getElementById('new_password');
            const confirmPassword = document.getElementById('confirm_password');
            const currentPassword = document.getElementById('current_password');

            // Real-time password confirmation check
            confirmPassword.addEventListener('input', function() {
                if (newPassword.value && confirmPassword.value) {
                    if (newPassword.value !== confirmPassword.value) {
                        confirmPassword.style.borderColor = '#e74c3c';
                    } else {
                        confirmPassword.style.borderColor = '#74ebd5';
                    }
                }
            });

            // Check if password fields are being used
            newPassword.addEventListener('input', function() {
                if (newPassword.value) {
                    currentPassword.required = true;
                    confirmPassword.required = true;
                } else {
                    currentPassword.required = false;
                    confirmPassword.required = false;
                }
            });

            form.addEventListener('submit', function(e) {
                // If any password field is filled, all must be filled
                if (newPassword.value || confirmPassword.value || currentPassword.value) {
                    if (!currentPassword.value) {
                        e.preventDefault();
                        alert('⚠️ Current password is required to change password!');
                        currentPassword.focus();
                        return false;
                    }
                    if (!newPassword.value) {
                        e.preventDefault();
                        alert('⚠️ New password is required!');
                        newPassword.focus();
                        return false;
                    }
                    if (newPassword.value !== confirmPassword.value) {
                        e.preventDefault();
                        alert('⚠️ New password and confirmation do not match!');
                        confirmPassword.focus();
                        return false;
                    }
                    if (newPassword.value.length < 6) {
                        e.preventDefault();
                        alert('⚠️ New password must be at least 6 characters long!');
                        newPassword.focus();
                        return false;
                    }
                }

                // Show loading message
                const submitBtn = document.querySelector('.update-btn');
                submitBtn.textContent = 'Updating...';
                submitBtn.disabled = true;
            });
        });
    </script>
</body>

</html>