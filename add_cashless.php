<?php
// Include your database connection here
include('config.php');

// Initialize variables with form data
$donor = $_POST['donor'];
$contact = $_POST['contact'];
$type_of_donation = $_POST['type_of_donation'];
$quantity = $_POST['quantity'];
$date = $_POST['date'];
$donation_for = $_POST['donation_for'];

// SQL query to insert data into cashless table
$sql = "INSERT INTO cashless (donor, contact, type_of_donation, quantity, date, donation_for) 
        VALUES ('$donor', '$contact', '$type_of_donation', '$quantity', '$date', '$donation_for')";

if (mysqli_query($conn, $sql)) {
    echo "<script>alert('New record added successfully');</script>";
    echo "<script>window.location = 'home.php#cashless-donations';</script>";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>
