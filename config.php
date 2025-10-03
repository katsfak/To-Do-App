<?php

// Database connection parameters
$host = "localhost";
$user = "root";
$password = "";
$dbname = "todolist_app";

// Create connection to MySQL database
$conn = mysqli_connect($host, $user, $password, $dbname);

// Check if connection was successful
if (!$conn) {
    // Output error message and terminate script if connection failed
    die("Connection failed: " . mysqli_connect_error());
}

