<?php
include('config.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donor = $_POST['donor'];
    $contact = $_POST['contact'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $purpose = $_POST['purpose'];
    $nameof = $_POST['nameof']; // Assuming user_id is stored in the session
    $mod_of = $_POST['mod_of']; // Assuming mod_of is from the form input

    $sql = "INSERT INTO cash (donor, contact, amount, date, purpose, nameof, mod_of) 
            VALUES ('$donor', '$contact', '$amount', '$date', '$purpose', '$nameof', '$mod_of')";
    
    if ($conn->query($sql) === TRUE) {
        echo '<script>alert("Cash donation added successfully!");</script>';
        echo '<script>window.location = "home.php#cash-donations";</script>';
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
