<?php
session_start();
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $current_password = mysqli_real_escape_string($conn, $_POST['current_password']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Check if id is set in session
    if (!isset($_SESSION['id'])) {
        echo '<script>alert("Session id not set."); window.location = "home.php";</script>';
        exit; // Exit or handle the error appropriately
    }

    $id = $_SESSION['id'];

    // Fetch current user's password hash from database
    $fetch_sql = "SELECT password FROM users WHERE id = $id";
    $result = mysqli_query($conn, $fetch_sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $stored_password = $row['password'];

        // Verify current password
        if (password_verify($current_password, $stored_password)) {
            // Check if new password matches confirmation
            if ($new_password === $confirm_password) {
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_sql = "UPDATE users SET password='$hashed_password' WHERE id=$id";

                if (mysqli_query($conn, $update_sql)) {
                    // Log the activity
                    $action = "Changed password.";
                    $log_sql = "INSERT INTO activity_logs (user_id, action) VALUES ($id, '$action')";
                    mysqli_query($conn, $log_sql);

                    echo '<script>alert("Password changed successfully."); window.location = "home.php";</script>';
                } else {
                    echo "Error updating password: " . mysqli_error($conn);
                }
            } else {
                echo '<script>alert("New passwords do not match."); window.location = "home.php";</script>';
            }
        } else {
            echo '<script>alert("Incorrect current password."); window.location = "home.php";</script>';
        }
    } else {
        echo '<script>alert("User not found."); window.location = "home.php";</script>';
    }
}
?>
