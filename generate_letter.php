<?php
include('config.php');

if (isset($_GET['donor'])) {
    $donor = $_GET['donor'];
    $contact = '';

    // Fetch donor contact information
    $sql = "SELECT contact FROM cash WHERE donor='$donor' UNION SELECT contact FROM cashless WHERE donor='$donor'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $contact = $row['contact'];
    }

    // Check if contact is a Gmail address
    if (strpos($contact, '@gmail.com') !== false) {
        // Fetch donation details
        $cashDonations = mysqli_query($conn, "SELECT amount, date FROM cash WHERE donor='$donor'");
        $cashlessDonations = mysqli_query($conn, "SELECT type_of_donation, quantity, date FROM cashless WHERE donor='$donor'");

        // Generate appreciation letter content
        $content = "Dear $donor,\n\n";
        $content .= "We are writing to express our heartfelt gratitude for your continued support through your donations.\n\n";
        
        // Cash Donations
        $content .= "Your cash donations have made a significant impact:\n";
        while ($row = mysqli_fetch_assoc($cashDonations)) {
            $content .= "- Amount: ₱" . number_format($row['amount'], 2) . ", Date: " . $row['date'] . "\n";
        }
        $content .= "\n";

        // Cashless Donations
        $content .= "Additionally, your cashless donations have been instrumental in supporting our cause:\n";
        while ($row = mysqli_fetch_assoc($cashlessDonations)) {
            $content .= "- Type: " . $row['type_of_donation'] . ", Quantity: " . $row['quantity'] . ", Date: " . $row['date'] . "\n";
        }
        $content .= "\n";

        // Closing
        $content .= "Your generosity and commitment to our organization are deeply appreciated. With your support, we are able to make a meaningful difference in our community.\n\n";
        $content .= "Thank you once again for your kindness and generosity.\n\n";
        $content .= "Sincerely,\nGawad Kalinga Laguna";

        // Create a Word document
        $filename = "appreciation_letter_$donor.doc";
        header("Content-Type: application/vnd.ms-word");
        header("Content-Disposition: attachment;Filename=$filename");
        echo $content;
        exit;
    } else {
        echo "The contact is not a Gmail address.";
    }
}
?>
