<?php
// Include your database connection here
include('config.php');

// Example query to fetch expense data
$sql = "SELECT * FROM expense";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // Set headers for Word file download
    header('Content-Type: application/msword');
    header('Content-Disposition: attachment; filename="expenses.doc"');

    // Start building the Word document content
    $content = "<html><body>";
    $content .= "<h1>Expense Report</h1>";
    $content .= "<table border='1'>";
    $content .= "<tr><th>Item</th><th>Price</th><th>Date</th></tr>";

    // Add data rows
    while ($row = mysqli_fetch_assoc($result)) {
        $content .= "<tr>";
        $content .= "<td>" . htmlspecialchars($row['item'], ENT_QUOTES, 'UTF-8') . "</td>";
        $content .= "<td>" . htmlspecialchars($row['details'], ENT_QUOTES, 'UTF-8') . "</td>";
        $content .= "<td>₱" . number_format($row['price'], 2) . "</td>";
        $content .= "<td>" . htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8') . "</td>";
        $content .= "</tr>";
    }

    // Close the Word document content
    $content .= "</table>";
    $content .= "</body></html>";

    // Output the content
    echo $content;
} else {
    echo "<p>No expenses found.</p>";
}

mysqli_close($conn);
?>
