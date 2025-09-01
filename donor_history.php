<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation History</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Font Awesome CSS for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>Donation History</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Donor</th>
                    <th>Donation Type</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Assuming you're using PHP and MySQL
                $servername = "localhost";
                $username = "username";
                $password = "password";
                $dbname = "database_name";

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Query for cashless donations
                $sql_cashless = "SELECT `donor`, `type_of_donation` FROM `cashless`";
                $result_cashless = $conn->query($sql_cashless);

                if ($result_cashless->num_rows > 0) {
                    while($row = $result_cashless->fetch_assoc()) {
                        echo "<tr><td>" . $row["donor"] . "</td><td>" . $row["type_of_donation"] . "</td><td></td></tr>";
                    }
                }

                // Query for cash donations
                $sql_cash = "SELECT `donor`, `amount` FROM `cash`";
                $result_cash = $conn->query($sql_cash);

                if ($result_cash->num_rows > 0) {
                    while($row = $result_cash->fetch_assoc()) {
                        echo "<tr><td>" . $row["donor"] . "</td><td></td><td>" . $row["amount"] . "</td></tr>";
                    }
                }

                // Close connection
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
