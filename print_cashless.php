<?php
// Include your database connection here
include('config.php');

// Example query to fetch cashless donation data
$sql = "SELECT * FROM cashless";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // Set headers for Word file download
    header('Content-Type: application/msword');
    header('Content-Disposition: attachment; filename="inkind_donations.doc"');

    // Start building the Word document content
    $content = "<html><body>";
    $content .= "<h1>Gawad Kalinga In-Kind Donations Report</h1>";
    $content .= "<table border='1'>";
    $content .= "<tr><th>Donor ID</th><th>Name</th><th>Contact</th><th>Type of Donation</th><th>Quantity</th><th>Date</th></tr>";

    // Add data rows
    while ($row = mysqli_fetch_assoc($result)) {
        $content .= "<tr>";
        $content .= "<td>{$row['id']}</td>";
        $content .= "<td>{$row['donor']}</td>";
        $content .= "<td>{$row['contact']}</td>";
        $content .= "<td>{$row['type_of_donation']}</td>";
        $content .= "<td>{$row['quantity']}</td>";
        $content .= "<td>{$row['date']}</td>";
        $content .= "</tr>";
    }

    // Close the Word document content
    $content .= "</table>";
    $content .= "</body></html>";

    // Output the content
    echo $content;
} else {
    echo "<p>No in-kind donations found.</p>";
}

mysqli_close($conn);
?>
