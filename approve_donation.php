<?php
// Include your database configuration file
include('config.php');

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approve_donation'])) {
    // Get the donation ID from the form
    $donation_id = $_POST['donation_id'];

    // Prepare and execute query to move data from temporary_donations to cash table
    $stmt = $conn->prepare("INSERT INTO cash (donor, contact, amount, date, purpose, nameof, mod_of)
                            SELECT donor, contact, amount, date, purpose, nameof, mod_of
                            FROM temporary_donations
                            WHERE id = ?");
    $stmt->bind_param("i", $donation_id);

    if ($stmt->execute()) {
        // If insertion is successful, delete the record from temporary_donations
        $delete_stmt = $conn->prepare("DELETE FROM temporary_donations WHERE id = ?");
        $delete_stmt->bind_param("i", $donation_id);
        
        if ($delete_stmt->execute()) {
            echo '<script>alert("Donation approved and moved to cash records.");</script>';
            echo '<script>window.location = "home.php";</script>';
        } else {
            echo "Error deleting temporary donation: " . $delete_stmt->error;
        }
    } else {
        echo "Error approving donation: " . $stmt->error;
    }

    // Close prepared statements
    $stmt->close();
    $delete_stmt->close();
}

// Close database connection
$conn->close();
?>
