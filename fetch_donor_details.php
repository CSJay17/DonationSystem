<?php
include('config.php');

if (isset($_GET['donor'])) {
    $donor = $_GET['donor'];

    // Fetch cash donations
    $cashDonations = mysqli_query($conn, "SELECT * FROM cash WHERE donor='$donor'");
    
    // Fetch cashless donations
    $cashlessDonations = mysqli_query($conn, "SELECT * FROM cashless WHERE donor='$donor'");
    
    echo "<h3>Cash Donations</h3>";
    if (mysqli_num_rows($cashDonations) > 0) {
        echo "<table>";
        echo "<tr><th>Amount</th><th>Date</th></tr>";
        while ($row = mysqli_fetch_assoc($cashDonations)) {
            echo "<tr><td>₱" . number_format($row['amount'], 2) . "</td><td>" . $row['date'] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No cash donations found.</p>";
    }

    echo "<h3>Cashless Donations</h3>";
    if (mysqli_num_rows($cashlessDonations) > 0) {
        echo "<table>";
        echo "<tr><th>Type</th><th>Quantity</th><th>Date</th></tr>";
        while ($row = mysqli_fetch_assoc($cashlessDonations)) {
            echo "<tr><td>" . $row['type_of_donation'] . "</td><td>" . $row['quantity'] . "</td><td>" . $row['date'] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No in-kind     donations found.</p>";
    }

    // Display contact of the donor
    $contact = mysqli_query($conn, "SELECT contact FROM cash WHERE donor='$donor'");
    $contactRow = mysqli_fetch_assoc($contact);
    $donorContact = $contactRow['contact'];
    echo "<h3>Contact</h3>";
    echo "<p>$donorContact</p>";
} else {
    echo "<p>Invalid request.</p>";
}
?>
