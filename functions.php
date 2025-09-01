<?php
// Include your database connection
include('config.php');

// Function to log an activity
function logActivity($user_id, $action_type, $action_description = '') {
    global $conn; // Access the $conn variable from config.php

    // Sanitize inputs if necessary
    $action_type = mysqli_real_escape_string($conn, $action_type);
    $action_description = mysqli_real_escape_string($conn, $action_description);

    // Insert the log into the database
    $sql = "INSERT INTO activity_logs (user_id, action_type, action_description)
            VALUES ('$user_id', '$action_type', '$action_description')";
    if (mysqli_query($conn, $sql)) {
        // Log successfully inserted
        return true;
    } else {
        // Error inserting log
        echo "Error: " . mysqli_error($conn);
        return false;
    }
}
?>
