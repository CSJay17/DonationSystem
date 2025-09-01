<?php
session_start();
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if id is set in session
    if (!isset($_SESSION['id'])) {
        echo '<script>alert("Session id not set."); window.location = "home.php";</script>';
        exit; // Exit or handle the error appropriately
    }

    $id = $_SESSION['id'];

    // Update SQL query
    $update_sql = "UPDATE users SET username='$username', email='$email' WHERE id=$id";

    // Execute the query
    if (mysqli_query($conn, $update_sql)) {
        $_SESSION['username'] = $username; // Update session variable if username changes
        $_SESSION['email'] = $email; // Update session variable if email changes

        // Log the activity
        $action = "Updated profile information.";
        $log_sql = "INSERT INTO activity_logs (user_id, action) VALUES ($id, '$action')";
        mysqli_query($conn, $log_sql);

        echo '<script>alert("Profile updated successfully."); window.location = "home.php";</script>';
    } else {
        echo "Error updating profile: " . mysqli_error($conn);
    }
}
?>
