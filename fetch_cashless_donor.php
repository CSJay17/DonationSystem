<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gkdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $sql = "SELECT donor, contact, type_of_donation, quantity, date, donation_for FROM cashless WHERE donor LIKE '%$query%'";
    $result = $conn->query($sql);

    $donors = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $donors[] = $row;
        }
    }
    echo json_encode($donors);
}

$conn->close();
?>
