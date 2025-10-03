<?php
// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Start the session
    session_start();
    // Remove all session variables
    session_unset();
    // Destroy the session
    session_destroy();
    // Redirect to the login page
    header("Location: LogIn/index.php");
    exit();
} else {
    // If accessed via GET, redirect to the login page
    header("Location: LogIn/index.php");
    exit();
}
