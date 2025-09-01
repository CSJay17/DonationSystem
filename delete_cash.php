<?php
// Include your database connection here
include('config.php');

// Check if ID parameter is set
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // SQL query to delete cash donation record
    $sql = "DELETE FROM cash WHERE id='$id'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Record deleted successfully');</script>";
        echo "<script>window.location = 'admin-panel.php#cash-donations';</script>";
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>
