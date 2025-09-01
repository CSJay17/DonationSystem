<?php
// Include your database connection here
include('config.php');

// Check if ID parameter is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete query
    $deleteSql = "DELETE FROM expense WHERE id=$id";

    if (mysqli_query($conn, $deleteSql)) {
        echo "Expense deleted successfully.";
        echo "<script>window.location = 'home.php#cash-donations';</script>";
    } else {
        echo "Error deleting expense: " . mysqli_error($conn);
    }
} else {
    echo "ID parameter not provided.";
}
?>
