<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    session_unset();
    session_destroy();
    header("Location: LogIn/index.php");
    exit();
} else {
    // Optionally, redirect or show an error if accessed via GET
    header("Location: LogIn/index.php");
    exit();
}
?>