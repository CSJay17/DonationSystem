<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'gkdb');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$donor = $_GET['donor'];

// Fetch cash donations
$cash_query = "SELECT `amount`, `date` FROM `cash` WHERE `donor` = '$donor'";
$cash_result = mysqli_query($conn, $cash_query);
$cash_donations = [];
if ($cash_result && mysqli_num_rows($cash_result) > 0) {
    while ($row = mysqli_fetch_assoc($cash_result)) {
        $cash_donations[] = $row;
    }
}

// Fetch cashless donations
$cashless_query = "SELECT `type_of_donation`, `quantity`, `date` FROM `cashless` WHERE `donor` = '$donor'";
$cashless_result = mysqli_query($conn, $cashless_query);
$cashless_donations = [];
if ($cashless_result && mysqli_num_rows($cashless_result) > 0) {
    while ($row = mysqli_fetch_assoc($cashless_result)) {
        $cashless_donations[] = $row;
    }
}

// Return data as JSON
echo json_encode([
    'cash' => $cash_donations,
    'cashless' => $cashless_donations
]);

$conn->close();
?>
