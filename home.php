<?php
// Include your database connection here
session_start();
include('config.php');

if (!isset($_SESSION['email'])) {
    header("Location: index.php"); // Redirect to login page if not logged in
    exit();
}

// Fetch the current user's username from the database
$email = $_SESSION['email'];
$sql = "SELECT `username` FROM `users` WHERE `email` = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $username = htmlspecialchars($row["username"], ENT_QUOTES, 'UTF-8');
} else {
    $username = "Unknown"; // Default username if not found
}

// Fetch total amount
$sqlTotalAmount = "SELECT SUM(amount) AS totalAmount FROM cash";
$resultTotalAmount = mysqli_query($conn, $sqlTotalAmount);
$rowTotalAmount = mysqli_fetch_assoc($resultTotalAmount);
$totalAmount = $rowTotalAmount['totalAmount'];

// Fetch total quantity
$sqlTotalQuantity = "SELECT SUM(quantity) AS totalQuantity FROM cashless";
$resultTotalQuantity = mysqli_query($conn, $sqlTotalQuantity);
$rowTotalQuantity = mysqli_fetch_assoc($resultTotalQuantity);
$totalQuantity = $rowTotalQuantity['totalQuantity'];

// Fetch total users
$sqlTotalUsers = "SELECT COUNT(*) AS totalUsers FROM users";
$resultTotalUsers = mysqli_query($conn, $sqlTotalUsers);
$rowTotalUsers = mysqli_fetch_assoc($resultTotalUsers);
$totalUsers = $rowTotalUsers['totalUsers'];

// Fetch monthly cash donation data
$sqlMonthlyCashDonations = "SELECT DATE_FORMAT(date, '%Y-%m') AS month, SUM(amount) AS total FROM cash GROUP BY month ORDER BY month";
$resultMonthlyCashDonations = mysqli_query($conn, $sqlMonthlyCashDonations);

$monthlyCashDonations = [];
while ($row = mysqli_fetch_assoc($resultMonthlyCashDonations)) {
    $monthlyCashDonations[] = $row;
}

// Fetch monthly cashless donation quantity data
$sqlMonthlyCashlessDonations = "SELECT DATE_FORMAT(date, '%Y-%m') AS month, SUM(quantity) AS total FROM cashless GROUP BY month ORDER BY month";
$resultMonthlyCashlessDonations = mysqli_query($conn, $sqlMonthlyCashlessDonations);

$monthlyCashlessDonations = [];
while ($row = mysqli_fetch_assoc($resultMonthlyCashlessDonations)) {
    $monthlyCashlessDonations[] = $row;
}

$sqlRecentCash = "SELECT * FROM cash ORDER BY date DESC LIMIT 3";
$resultRecentCash = mysqli_query($conn, $sqlRecentCash);
$recentCashDonations = [];
while ($row = mysqli_fetch_assoc($resultRecentCash)) {
    $recentCashDonations[] = $row;
}

// Fetch the most recent five cashless donations
$sqlRecentCashless = "SELECT * FROM cashless ORDER BY date DESC LIMIT 3";
$resultRecentCashless = mysqli_query($conn, $sqlRecentCashless);
$recentCashlessDonations = [];
while ($row = mysqli_fetch_assoc($resultRecentCashless)) {
    $recentCashlessDonations[] = $row;
}

$sql = "SELECT SUM(price) AS total_cash FROM expense";
$result = $conn->query($sql);
$total_cash = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_cash = $row['total_cash'];
}

if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $sql = "SELECT DISTINCT donor FROM cash WHERE donor LIKE '%$query%' LIMIT 10";
    $result = mysqli_query($conn, $sql);

    $donors = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $donors[] = $row['donor'];
    }

    echo json_encode($donors);
}

if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $sql = "SELECT * FROM cash WHERE donor LIKE '%$query%' LIMIT 10";
    $result = mysqli_query($conn, $sql);

    $donors = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $donors[] = $row;
    }

    echo json_encode($donors);
}

$sqlMonthlyExpenses = "SELECT DATE_FORMAT(date, '%Y-%m') AS month, SUM(price) AS total FROM expense GROUP BY month ORDER BY month";
$resultMonthlyExpenses = mysqli_query($conn, $sqlMonthlyExpenses);

$monthsExpenses = [];
$totalExpenses = [];

while ($row = mysqli_fetch_assoc($resultMonthlyExpenses)) {
    $monthsExpenses[] = $row['month'];
    $totalExpenses[] = $row['total'];
}

function getTopDonors($conn) {
    $sql = "SELECT donor, COUNT(*) AS donation_count FROM (
                SELECT donor FROM cash
                UNION ALL
                SELECT donor FROM cashless
            ) AS combined_donations
            GROUP BY donor
            ORDER BY donation_count DESC
            LIMIT 5";

    $result = mysqli_query($conn, $sql);
    $topDonors = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $topDonors[] = $row;
    }

    return $topDonors;
}

// Fetch top 5 donors
$topDonors = getTopDonors($conn);

mysqli_close($conn);
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tulong Kalinga</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-zoom/1.1.1/chartjs-plugin-zoom.min.js"></script>
</head>
<body>

    <div class="header">
    <div class="logo">
        <img src="images/3.png" alt="Logo">
    </div>
    <div class="user-info">
        <p>Welcome, <?php echo $username; ?>&nbsp&nbsp<i class="fas fa-user">&nbsp</i> </p>
    </div>
</div>

    <div class="admin-panel">
        <div class="admin-menu">
        <ul>
        <li><a href="#dashboard" data-target="dashboard" class="menu-link"><i class="fas fa-tachometer-alt"></i>&nbsp;Dashboard</a></li>
        <li><a href="#cash-donations" data-target="cash-donations" class="menu-link"><i class="fas fa-money-bill"></i>&nbsp;Cash Donations</a></li>
        <li><a href="#cashless-donations" data-target="cashless-donations" class="menu-link"><i class="fas fa-credit-card"></i>&nbsp;In-Kind Donations</a></li>
        <li><a href="#expenses" data-target="expenses" class="menu-link"><i class="fas fa-receipt"></i>&nbsp;Expenses</a></li>
        <li><a href="#donor-history" data-target="donor-history" class="menu-link"><i class="fas fa-history"></i>&nbsp;Donor Records</a></li>
        <li><a href="#transaction-history" data-target="transaction-history" class="menu-link"><i class="fas fa-exchange-alt"></i>&nbsp;Transaction History</a></li>
        <li><a href="user.php" id="user-settings-link" class="menu-link"><i class="fas fa-user-cog"></i>&nbsp;User Settings</a></li>
        <li>
    <a href="logout.php" class="menu-link" id="logout-link" onclick="confirmLogout(event)">
        <i class="fas fa-sign-out-alt"></i>&nbsp;Logout
    </a>
</li>
        </ul>
        </div>
        <div class="admin-content">
            <div id="dashboard">
            <h2>Dashboard</h2>
            <div class="panel">
                <div class="panel-item">
                    <h3>Cash Donation</h3>
                    <p>₱<?php echo is_array($totalAmount) ? 'N/A' : number_format($totalAmount, 2); ?></p>
                </div>
                <div class="panel-item">
                    <h3>Item Quantity</h3>
                    <p><?php echo is_array($totalQuantity) ? 'N/A' : $totalQuantity; ?></p>
                </div>
                <div class="panel-item">
                    <h3>Expenses</h3>
                    <p>₱<?php echo number_format($total_cash, 2); ?></p>
                </div>
            </div>


                <div class="chart-container">
                    <h3></h3>
                    <canvas id="cashDonationChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3></h3>
                    <canvas id="cashlessDonationChart"></canvas>
                </div>
                <div class="chart-container">
                <h3></h3>
                <canvas id="expenseChart"></canvas>
                </div>
                 
                 <div class="recent-donations">
    <div class="panel-item">
        <h3>Top Donors by Frequency of Donations</h3>
        <table class="panel-item-table">
            <thead>
                <tr>
                    <th>Donor</th>
                    <th>Number of Donations</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topDonors as $donor): ?>
                    <tr>
                        <td><?php echo $donor['donor']; ?></td>
                        <td><?php echo $donor['donation_count']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="panel-item">
        <h3>Recent Cash Donations</h3>
        <table class="panel-item-table">
            <thead>
                <tr>
                    <th>Donor</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentCashDonations as $donation): ?>
                    <tr>
                        <td><?php echo $donation['donor']; ?></td>
                        <td>₱<?php echo number_format($donation['amount'], 2); ?></td>
                        <td><?php echo $donation['date']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="panel-item">
        <h3>Recent In-Kind Donations</h3>
        <table class="panel-item-table">
            <thead>
                <tr>
                    <th>Donor</th>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentCashlessDonations as $donation): ?>
                    <tr>
                        <td><?php echo $donation['donor']; ?></td>
                        <td><?php echo $donation['type_of_donation']; ?></td>
                        <td><?php echo $donation['quantity']; ?></td>
                        <td><?php echo $donation['date']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</div>
            
            <div id="cash-donations" style="display: none;">
    <div class="form-container" id="add-cash-form">
    <h2>Add Cash Donation</h2>
    <form action="add_cash.php" method="POST">
        <input type="text" id="donor" name="donor" placeholder="Donor Name" required>
        <div id="donor-suggestions" class="suggestions"></div>

        <input type="text" id="contact" name="contact" placeholder="Contact Number" required>

        <input type="number" id="amount" name="amount" placeholder="Amount" required>

        <input type="text" id="mod_of" name="mod_of" placeholder="Mode of Donation" required>

        <input type="date" id="date" name="date" placeholder="Date" required>

        <input type="text" id="purpose" name="purpose" placeholder="Purpose" required>

        <input type="text" id="nameof" name="nameof" placeholder="Added By" required>

        <button type="submit">Submit</button>
        <br>
        <button type="button" id="cancel-btn" class="cancel-btn">Cancel</button>
    </form>
</div>


    <h2>Cash Donations
        <input type="text" id="searchInput" placeholder="Search..." style="float: right;">
    </h2>

    <form method="GET" action="" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center;">
            <button id="add-new-btn" style="margin-right: 10px;">Add New</button> <!-- Button to show the add form -->
            <a href="print_cash.php" onclick="window.print();" class="button-link">Print</a>
        </div>
        <div style="display: flex; align-items: center;">
            <input type="hidden" id="current_tab" name="current_tab" value="cash-donations"> <!-- Add this line -->
            <label for="start_date" style="margin-right: 10px;">From:</label>
            <input type="date" id="start_date" name="start_date" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>" style="padding: 6px; border-radius: 4px; border: 1px solid #ccc; margin-right: 10px;">

            <label for="end_date" style="margin-right: 10px;">To:</label>
            <input type="date" id="end_date" name="end_date" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>" style="padding: 6px; border-radius: 4px; border: 1px solid #ccc; margin-right: 10px;">

            <button type="submit" class="button-link">Filter</button>
        </div>
    </form>

    <table>
        <thead>
            <tr>
                <th>Donor ID</th>
                <th>Name</th>
                <th>Amount</th>
                <th>Mod of Donation</th>
                <th>Donation For</th>
                <th>Date</th>
                <th>Added By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <!-- PHP code to fetch and display cash donation data -->
            <?php
                // Include your database connection here
                include('config.php');
                $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
                $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

                // Example query to fetch cash donation data
                $sql = "SELECT * FROM cash";
                if (!empty($start_date) && !empty($end_date)) {
                    $sql .= " WHERE date >= '$start_date' AND date <= '$end_date'";
                } elseif (!empty($start_date)) {
                    $sql .= " WHERE date >= '$start_date'";
                } elseif (!empty($end_date)) {
                    $sql .= " WHERE date <= '$end_date'";
                }
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Extract year from date
                        $year = date('Y', strtotime($row['date']));

                        echo "<tr>";
                        echo "<td>" . sprintf("CD-%s-%04d", $year, $row['id']) . "</td>"; // Formatting the ID with year
                        echo "<td>" . htmlspecialchars($row['donor'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>₱" . number_format($row['amount'], 2) . "</td>";
                        echo "<td>" . htmlspecialchars($row['mod_of'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . htmlspecialchars($row['purpose'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . htmlspecialchars($row['nameof'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>";
                        echo "<button class='action-button' onclick=\"location.href='edit_cash.php?action=edit&id=" . $row['id'] . "'\">Edit</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No cash donations found.</td></tr>";
                }

                mysqli_close($conn);
            ?>
        </tbody>
    </table>
</div>


    <div id="cashless-donations" style="display: none;">
    <div class="form-container" id="add-cashless-form">
    <h2>Add In-Kind Donation</h2>
    <form action="add_cashless.php" method="POST">
        <input type="text" id="donor" name="donor" placeholder="Donor Name" required>
        <div id="donor-suggestions" class="suggestions"></div>
        <input type="text" id="contact" name="contact" placeholder="Contact Number" required>
        
        <input type="text" id="type" name="type_of_donation" placeholder="Type of Donation" required>
        
        <input type="number" id="quantity" name="quantity" placeholder="Quantity" required>
        
        <input type="date" id="date" name="date" placeholder="Date" required>

        <input type="text" id="donation_for" name="donation_for" placeholder="Donation For" required>
        
        <button type="submit">Submit</button>
        <br>
        <button type="button" id="cancel-btn" class="cancel-btn">Cancel</button>
    </form>
</div>


    <h2>In-Kind Donations
        <input type="text" id="searchInputCashless" placeholder="Search..." style="float: right;">
    </h2>

    <form method="GET" action="" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center;">
            <button id="add-new-cashless-btn" style="margin-right: 10px;">Add New</button> <!-- Button to show the add form -->
            <a href="print_cashless.php" onclick="window.print();" class="button-link">Print</a>
        </div>
        <div style="display: flex; align-items: center;">
            <input type="hidden" id="current_tab" name="current_tab" value="cashless-donations"> <!-- Add this line -->
            <label for="start_date_cashless" style="margin-right: 10px;">From:</label>
            <input type="date" id="start_date_cashless" name="start_date_cashless" value="<?php echo isset($_GET['start_date_cashless']) ? $_GET['start_date_cashless'] : ''; ?>" style="padding: 6px; border-radius: 4px; border: 1px solid #ccc; margin-right: 10px;">

            <label for="end_date_cashless" style="margin-right: 10px;">To:</label>
            <input type="date" id="end_date_cashless" name="end_date_cashless" value="<?php echo isset($_GET['end_date_cashless']) ? $_GET['end_date_cashless'] : ''; ?>" style="padding: 6px; border-radius: 4px; border: 1px solid #ccc; margin-right: 10px;">

            <button type="submit" class="button-link">Filter</button>
        </div>
    </form>
    <table>
        <thead>
            <tr>
                <th>Donor ID</th>
                <th>Name</th>
                <th>Type of Donation</th>
                <th>Quantity</th>
                <th>Donation For</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="tableBodyCashless">
            <!-- PHP code to fetch and display cashless donation data -->
            <?php
                // Include your database connection here
                include('config.php');
                
                $start_date_cashless = isset($_GET['start_date_cashless']) ? $_GET['start_date_cashless'] : '';
                $end_date_cashless = isset($_GET['end_date_cashless']) ? $_GET['end_date_cashless'] : '';

                // Example query to fetch cashless donation data
                $sql = "SELECT * FROM cashless";
                if (!empty($start_date_cashless) && !empty($end_date_cashless)) {
                    $sql .= " WHERE date >= '$start_date_cashless' AND date <= '$end_date_cashless'";
                } elseif (!empty($start_date_cashless)) {
                    $sql .= " WHERE date >= '$start_date_cashless'";
                } elseif (!empty($end_date_cashless)) {
                    $sql .= " WHERE date <= '$end_date_cashless'";
                }
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Extract year from date
                        $year = date('Y', strtotime($row['date']));

                        echo "<tr>";
                        echo "<td>" . sprintf("IKD-%s-%04d", $year, $row['id']) . "</td>"; // Formatting the ID with year
                        echo "<td>" . htmlspecialchars($row['donor'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . htmlspecialchars($row['type_of_donation'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . htmlspecialchars($row['quantity'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . htmlspecialchars($row['donation_for'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>" . htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8') . "</td>";
                        echo "<td>";
                        echo "<button class='action-button' onclick=\"location.href='edit_cashless.php?action=edit_cashless&id=" . $row['id'] . "'\">Edit</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No cashless donations found.</td></tr>";
                }

                mysqli_close($conn);
            ?>
        </tbody>
    </table>
</div>

            
            <div class="section" id="expenses" style="display: none;">
            <h2>Expenses</h2>
            <!-- Button to show the add expense form -->
            <button id="add-expense-btn">Add Expenses</button>
            <a href="print_expense.php" class="button-link">Print</a>

            <!-- Date filter form -->

            <div class="expense-tracker">
                <div class="panel">
                    <div class="panel-item">
                        <div class="total-cash">
                            <h2>Total Expenses:</h2>
                            <p>₱<?php echo number_format($total_cash, 2); ?></p>
                        </div>
                    </div>
                    <div class="panel-item">
                        <div class="remaining-funds">
                            <h2>Remaining Funds:</h2>
                            <p>₱<?php echo number_format($totalAmount - $total_cash, 2); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="expense-list">
                <h3>
                    
                </h3>
                
              <form method="GET" action="" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center;">
                    <input type="text" id="searchInputExpense" placeholder="Search..." style="padding: 8px; border: 1px solid #ccc; border-radius: 4px; width: 200px; margin-right: 10px;">
                    <input type="hidden" id="current_tab" name="current_tab" value="expenses"> <!-- Add this line -->
                </div>
                
                <div style="display: flex; align-items: center;">
                    <label for="start_date_expense" style="margin-right: 10px;">Start Date:</label>
                    <input type="date" id="start_date_expense" name="start_date_expense" value="<?php echo isset($_GET['start_date_expense']) ? $_GET['start_date_expense'] : ''; ?>" style="padding: 6px; border-radius: 4px; border: 1px solid #ccc; margin-right: 10px;">

                    <label for="end_date_expense" style="margin-right: 10px;">End Date:</label>
                    <input type="date" id="end_date_expense" name="end_date_expense" value="<?php echo isset($_GET['end_date_expense']) ? $_GET['end_date_expense'] : ''; ?>" style="padding: 6px; border-radius: 4px; border: 1px solid #ccc; margin-right: 10px;">

                    <button type="submit" class="button-link">Filter</button>
                </div>
            </form>
                <table>
    <thead>
        <tr>
            <th>Item ID</th>
            <th>Item</th>
            <th>Details</th> <!-- Added Details column -->
            <th>Price</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody id="tableBodyExpense">
            <!-- PHP code to fetch and display expenses -->
            <?php
            include('config.php');

            $start_date_expense = isset($_GET['start_date_expense']) ? $_GET['start_date_expense'] : '';
            $end_date_expense = isset($_GET['end_date_expense']) ? $_GET['end_date_expense'] : '';

            $sql = "SELECT * FROM expense";
            if (!empty($start_date_expense) && !empty($end_date_expense)) {
                $sql .= " WHERE date >= '$start_date_expense' AND date <= '$end_date_expense'";
            } elseif (!empty($start_date_expense)) {
                $sql .= " WHERE date >= '$start_date_expense'";
            } elseif (!empty($end_date_expense)) {
                $sql .= " WHERE date <= '$end_date_expense'";
            }
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // Extract year from date
                    $year = date('Y', strtotime($row['date']));

                    echo '<tr>';
                    echo '<td>' . sprintf("I-%s-%04d", $year, $row['id']) . '</td>'; // Formatting the item ID with year
                    echo '<td>' . htmlspecialchars($row['item'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '<td>' . htmlspecialchars($row['details'], ENT_QUOTES, 'UTF-8') . '</td>'; // Displaying details
                    echo '<td>₱' . number_format($row['price'], 2) . '</td>';
                    echo '<td>' . htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '<td><button class="action-button" onclick="location.href=\'edit_expense.php?action=edit_expense&id=' . $row['id'] . '\'">Edit</button></td>';
                    echo '</tr>';
                }
        } else {
            echo "<tr><td colspan='6'>No expenses found.</td></tr>"; // Adjusted colspan to match number of columns
        }

        mysqli_close($conn);
        ?>
    </tbody>
</table>

            </div>
        </div>
        <div class="form-container" id="expense-form-container">
    <div class="expense-form">
        <h2>Add Expense</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>#expenses" method="POST">
            <label for="item">Item:</label>
            <input type="text" id="item" name="item" required>
            
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" required>
            
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>
            
            <label for="details">Details:</label>
            <textarea id="details" name="details" required></textarea>
            
            <button type="submit" name="submit">Submit</button>
            <br>
            <button type="button" id="cancel-btn" class="cancel-btn">Cancel</button>
        </form>
    </div>
</div>


        <div class="section" id="donor-history">
    <h2>Donor History</h2>
    <table>
        <thead>
            <tr>
                <th>Donor Name</th>
                <th>Contact</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include('config.php');

            // Query to get distinct donors from both cash and cashless tables
            $sql = "SELECT DISTINCT donor, contact FROM (SELECT donor, contact FROM cash UNION ALL SELECT donor, contact FROM cashless) as donors";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['donor'] . "</td>";
                    echo "<td>" . $row['contact'] . "</td>";
                    echo "<td>
                            <button class='show-button' onclick=\"viewDetails('" . $row['donor'] . "')\">View Details</button>
                            <button class='send-button' onclick=\"sendLetter('" . $row['donor'] . "', '" . $row['contact'] . "')\">Generate Appreciation Letter</button>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='2'>No donors found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
 <div id="donor-details" style="display: none;">
                <h2>Donor Details</h2>
                <div id="details-content">
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

                        echo "<h3>In-Kind Donations</h3>";
                        if (mysqli_num_rows($cashlessDonations) > 0) {
                            echo "<table>";
                            echo "<tr><th>Type</th><th>Quantity</th><th>Date</th></tr>";
                            while ($row = mysqli_fetch_assoc($cashlessDonations)) {
                                echo "<tr><td>" . $row['type_of_donation'] . "</td><td>" . $row['quantity'] . "</td><td>" . $row['date'] . "</td></tr>";
                            }
                            echo "</table>";
                        } else {
                            echo "<p>No in-kind donations found.</p>";
                        }
                    } else {
                        echo "";
                    }
                    ?>
                </div>
                <button class="show-button" onclick="closeDetails()">Back</button>
            </div>

            <!-- Content for Transaction History -->
<div class="admin-content" id="transaction-history">
    <h2>Transaction History</h2>
    
    <?php
    include('config.php');

    // Fetch and display records from cash table
    $sql_cash = "SELECT id, donor, contact, amount, purpose, date, nameof FROM cash ORDER BY date DESC";
    $result_cash = mysqli_query($conn, $sql_cash);

    // Fetch and display records from cashless table
    $sql_cashless = "SELECT id, donor, contact, type_of_donation, quantity, donation_for, date FROM cashless ORDER BY date DESC";
    $result_cashless = mysqli_query($conn, $sql_cashless);

    // Fetch and display records from temporary_donations table (for pending donations)
    $sql_temp = "SELECT id, donor, contact, amount, purpose, date, nameof, mod_of FROM temporary_donations ORDER BY date DESC";
    $result_temp = mysqli_query($conn, $sql_temp);
    ?>


    <table class="transaction-table">
    <thead>
        <tr>
            <th colspan="6"><h3>Donation History</h3></th>
        </tr>
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Donor</th>
            <th>Contact</th>
            <th>Details</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Display pending donations
        if (mysqli_num_rows($result_temp) > 0) {
            while ($row = mysqli_fetch_assoc($result_temp)) {
                echo "<tr class='pending-donation'>";
                echo "<td>" . $row['date'] . "</td>";
                echo "<td>Pending Donation</td>";
                echo "<td>" . $row['donor'] . "</td>";
                echo "<td>" . $row['contact'] . "</td>";
                echo "<td>Amount: ₱" . number_format($row['amount'], 2) . "<br>Purpose: " . $row['purpose'] . "</td>";
                echo '<td>
                        <form action="approve_donation.php" method="post">
                            <input type="hidden" name="donation_id" value="' . $row['id'] . '">
                            <button type="submit" name="approve_donation">Approve</button>
                        </form>
                      </td>';
                echo "</tr>";
            }
        }

        // Display approved cash donations
        if (mysqli_num_rows($result_cash) > 0) {
            while ($row = mysqli_fetch_assoc($result_cash)) {
                echo "<tr class='cash-donation'>";
                echo "<td>" . $row['date'] . "</td>";
                echo "<td>Cash Donation</td>";
                echo "<td>" . $row['donor'] . "</td>";
                echo "<td>" . $row['contact'] . "</td>";
                echo "<td>Amount: ₱" . number_format($row['amount'], 2) . "<br>Purpose: " . $row['purpose'] . "</td>";
                echo "<td>Approved</td>";
                echo "</tr>";
            }
        }

        // Display approved in-kind donations
        if (mysqli_num_rows($result_cashless) > 0) {
            while ($row = mysqli_fetch_assoc($result_cashless)) {
                echo "<tr class='cashless-donation'>";
                echo "<td>" . $row['date'] . "</td>";
                echo "<td>In-Kind Donation</td>";
                echo "<td>" . $row['donor'] . "</td>";
                echo "<td>" . $row['contact'] . "</td>";
                echo "<td>Type: " . $row['type_of_donation'] . "<br>Quantity: " . $row['quantity'] . "<br>For: " . $row['donation_for'] . "</td>";
                echo "<td>Approved</td>";
                echo "</tr>";
            }
        }

        // If no records found for any type, display a message
        if (mysqli_num_rows($result_temp) == 0 && mysqli_num_rows($result_cash) == 0 && mysqli_num_rows($result_cashless) == 0) {
            echo "<tr><td colspan='6'>No donation records found.</td></tr>";
        }
        ?>
    </tbody>
</table>

        </div>
</div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function showTab(tabId) {
            // Hide all tabs
            var tabs = document.querySelectorAll('.admin-content > div');
            tabs.forEach(function(tab) {
                tab.style.display = 'none';
            });

            // Show the selected tab
            document.getElementById(tabId).style.display = 'block';

            // Ensure the expense form is visible if Expenses tab is clicked
            if (tabId === 'expenses') {
                document.getElementById('expense-form-container').style.display = 'none'; // Ensure the form is hidden initially
            }

            // Update the URL hash based on the selected tab
            window.location.hash = tabId;
        }

        // Initially show the dashboard tab or the tab from the hash
        var initialTab = window.location.hash ? window.location.hash.substring(1) : 'dashboard';
        showTab(initialTab);

        // Add click event listeners to menu items
        document.querySelectorAll('.admin-menu a').forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                var tabId = this.getAttribute('href').substring(1);
                showTab(tabId);
            });
        });

        // Show add cash form
        document.getElementById('add-new-btn').addEventListener('click', function() {
            document.getElementById('add-cash-form').style.display = 'block';
        });

        // Show add cashless form
        document.getElementById('add-new-cashless-btn').addEventListener('click', function() {
            document.getElementById('add-cashless-form').style.display = 'block';
        });

        // Show expense form
        document.getElementById('add-expense-btn').addEventListener('click', function() {
            document.getElementById('expense-form-container').style.display = 'block';
        });

        // Add event listeners for all cancel buttons
        document.querySelectorAll('.cancel-btn').forEach(function(cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                var formContainer = this.closest('.form-container');
                if (formContainer) {
                    formContainer.classList.remove('active');
                    formContainer.style.display = 'none';
                }
            });
        });

        // Ensure the correct tab is shown on hash change
        window.addEventListener('hashchange', function() {
            var tabId = window.location.hash.substring(1);
            showTab(tabId);
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
    var donorInput = document.getElementById('donor');
    var suggestionsContainer = document.getElementById('donor-suggestions');
    donorInput.addEventListener('input', function() {
        var query = donorInput.value;
        if (query.length >= 2) {
            fetch('fetch_donor.php?query=' + query)
                .then(response => response.json())
                .then(data => {
                    suggestionsContainer.innerHTML = '';
                    if (data.length > 0) {
                        // Auto-fill with the first matching donor
                        var donor = data[0];
                        document.getElementById('contact').value = donor.contact;
                        document.getElementById('amount').value = donor.amount;
                        document.getElementById('date').value = donor.date;
                        document.getElementById('purpose').value = donor.purpose;
                        document.getElementById('nameof').value = donor.nameof;
                        document.getElementById('mod_of').value = donor.mod_of;

                        // Show suggestions in case user wants to select a different donor
                        data.forEach(donor => {
                            var suggestion = document.createElement('div');
                            suggestion.classList.add('suggestion');
                            suggestion.textContent = donor.donor;
                            suggestion.addEventListener('click', function() {
                                donorInput.value = donor.donor;
                                document.getElementById('contact').value = donor.contact;
                                document.getElementById('amount').value = donor.amount;
                                document.getElementById('date').value = donor.date;
                                document.getElementById('purpose').value = donor.purpose;
                                document.getElementById('nameof').value = donor.nameof;
                                document.getElementById('mod_of').value = donor.mod_of;
                                suggestionsContainer.innerHTML = '';
                            });
                            suggestionsContainer.appendChild(suggestion);
                        });
                    } else {
                        // Clear the fields if no matching donor is found
                        document.getElementById('contact').value = '';
                        document.getElementById('amount').value = '';
                        document.getElementById('date').value = '';
                        document.getElementById('purpose').value = '';
                        document.getElementById('nameof').value = '';
                        document.getElementById('mod_of').value = '';
                    }
                })
                .catch(error => console.error('Error fetching donor data:', error));
        } else {
            suggestionsContainer.innerHTML = '';
            // Clear the fields if the query is too short
            document.getElementById('contact').value = '';
            document.getElementById('amount').value = '';
            document.getElementById('date').value = '';
            document.getElementById('purpose').value = '';
            document.getElementById('nameof').value = '';
            document.getElementById('mod_of').value = '';
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    var donorInput = document.getElementById('donor');
    var suggestionsContainer = document.getElementById('donor-suggestions');
    donorInput.addEventListener('input', function() {
        var query = donorInput.value;
        if (query.length >= 2) {
            fetch('fetch_cashless_donor.php?query=' + query)
                .then(response => response.json())
                .then(data => {
                    suggestionsContainer.innerHTML = '';
                    if (data.length > 0) {
                        // Auto-fill with the first matching donor
                        var donor = data[0];
                        document.getElementById('contact').value = donor.contact;
                        document.getElementById('type_of_donation').value = donor.type_of_donation;
                        document.getElementById('quantity').value = donor.quantity;
                        document.getElementById('date').value = donor.date;
                        document.getElementById('donation_for').value = donor.donation_for;

                        // Show suggestions in case user wants to select a different donor
                        data.forEach(donor => {
                            var suggestion = document.createElement('div');
                            suggestion.classList.add('suggestion');
                            suggestion.textContent = donor.donor;
                            suggestion.addEventListener('click', function() {
                                donorInput.value = donor.donor;
                                document.getElementById('contact').value = donor.contact;
                                document.getElementById('type_of_donation').value = donor.type_of_donation;
                                document.getElementById('quantity').value = donor.quantity;
                                document.getElementById('date').value = donor.date;
                                document.getElementById('donation_for').value = donor.donation_for;
                                suggestionsContainer.innerHTML = '';
                            });
                            suggestionsContainer.appendChild(suggestion);
                        });
                    } else {
                        // Clear the fields if no matching donor is found
                        document.getElementById('contact').value = '';
                        document.getElementById('type_of_donation').value = '';
                        document.getElementById('quantity').value = '';
                        document.getElementById('date').value = '';
                        document.getElementById('donation_for').value = '';
                    }
                })
                .catch(error => console.error('Error fetching donor data:', error));
        } else {
            suggestionsContainer.innerHTML = '';
            // Clear the fields if the query is too short
            document.getElementById('contact').value = '';
            document.getElementById('type_of_donation').value = '';
            document.getElementById('quantity').value = '';
            document.getElementById('date').value = '';
            document.getElementById('donation_for').value = '';
        }
    });
});



function toggleTransactionHistory() {
        var transactionHistory = document.getElementById('transaction-history');
        if (transactionHistory.style.display === 'none') {
            transactionHistory.style.display = 'block';
        } else {
            transactionHistory.style.display = 'none';
        }
    }

// JavaScript to toggle visibility of donor history section
function sendLetter(donor, contact) {
    if (contact.includes("gmail.com")) {
        // Generate appreciation letter content
        let content = `
        <html>
        <head><title>Appreciation Letter</title></head>
        <body>
            <h1>Thank You, ${donor}!</h1>
            <p>Dear ${donor},</p>
            <p>We are incredibly grateful for your generous donations. Your support has been instrumental in helping us achieve our mission and make a positive impact on the lives of many.</p>
            <p>Throughout the year, your contributions have made a significant difference. Your generosity has allowed us to:</p>
            <ul>
                <li>Provide financial assistance to families in need.</li>
                <li>Support educational programs that empower young minds.</li>
                <li>Deliver essential supplies to communities affected by natural disasters.</li>
                <li>Expand our efforts to protect the environment and promote sustainable practices.</li>
            </ul>
            <p>Your dedication to our cause inspires us every day. With your continued support, we are confident that we can create a brighter future for all.</p>
            <p>Once again, thank you for your kindness and generosity.</p>
            <p>Sincerely,</p>
            <p>Gawad Kalinga Laguna</p>
        </body>
        </html>
        `;

        // Create a Blob from the HTML content
        let blob = new Blob([content], { type: 'application/msword' });

        // Use FileSaver.js to save the file
        saveAs(blob, `${donor}_Appreciation_Letter.doc`);
    } else {
        alert("The donor's contact is not a Gmail address.");
    }
}

var monthlyCashDonations = <?php echo json_encode($monthlyCashDonations); ?>;
var monthlyCashlessDonations = <?php echo json_encode($monthlyCashlessDonations); ?>;

var monthsCash = monthlyCashDonations.map(function(donation) {
    return donation.month;
});

var totalAmounts = monthlyCashDonations.map(function(donation) {
    return donation.total;
});

var monthsCashless = monthlyCashlessDonations.map(function(donation) {
    return donation.month;
});

var totalQuantities = monthlyCashlessDonations.map(function(donation) {
    return donation.total;
});

// Chart.js code for cash donations
var ctxCash = document.getElementById('cashDonationChart').getContext('2d');
var cashDonationChart = new Chart(ctxCash, {
    type: 'line',
    data: {
        labels: monthsCash,
        datasets: [{
            label: 'Monthly Cash Donations',
            data: totalAmounts,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 5,
            fill: false
        }]
    },
    options: {
        responsive: true,
        scales: {
            x: {
                ticks: {
                    color: 'black'  // Black color for x-axis labels
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'  // Light black color for x-axis grid lines
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    color: 'black'  // Black color for y-axis labels
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'  // Light black color for y-axis grid lines
                }
            }
        }
    }
});

// Chart.js code for cashless donations
var ctxCashless = document.getElementById('cashlessDonationChart').getContext('2d');
var cashlessDonationChart = new Chart(ctxCashless, {
    type: 'line',
    data: {
        labels: monthsCashless,
        datasets: [{
            label: 'Monthly In-Kind Donations',
            data: totalQuantities,
            backgroundColor: 'rgba(255, 159, 64, 0.2)',
            borderColor: 'rgba(255, 159, 64, 1)',
            borderWidth: 5,
            fill: false
        }]
    },
    options: {
        responsive: true,
        scales: {
            x: {
                ticks: {
                    color: 'black'  // Black color for x-axis labels
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'  // Light black color for x-axis grid lines
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    color: 'black'  // Black color for y-axis labels
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'  // Light black color for y-axis grid lines
                }
            }
        }
    }
});

// Chart.js code for expenses
var ctxExpenses = document.getElementById('expenseChart').getContext('2d');
var expenseChart = new Chart(ctxExpenses, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($monthsExpenses); ?>,
        datasets: [{
            label: 'Monthly Expenses',
            data: <?php echo json_encode($totalExpenses); ?>,
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 5,
            fill: false
        }]
    },
    options: {
        responsive: true,
        scales: {
            x: {
                ticks: {
                    color: 'black'  // Black color for x-axis labels
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'  // Light black color for x-axis grid lines
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    color: 'black'  // Black color for y-axis labels
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'  // Light black color for y-axis grid lines
                }
            }
        }
    }
});


// JavaScript for filtering the expense table based on user input
document.getElementById("searchInputExpense").addEventListener("keyup", function() {
    var input, filter, table, tr, td, i, txtValue;
    input = this;
    filter = input.value.toUpperCase();
    table = document.getElementById("tableBodyExpense");
    tr = table.getElementsByTagName("tr");

    // Loop through all table rows, and hide those who don't match the search query
    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[1]; // Index 0 is the column for item name
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
});

// JavaScript for filtering the table based on user input
document.getElementById("searchInput").addEventListener("keyup", function() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("tableBody");
    tr = table.getElementsByTagName("tr");

    // Loop through all table rows, and hide those who don't match the search query
    for (i = 0; i < tr.length; i++) {
        var matchFound = false; // Flag to check if any column matches the filter

        // Search through columns: donor ID (0), donor name (1), type of donation (2), quantity (3), date (4)
        for (var j = 0; j < 5; j++) {
            td = tr[i].getElementsByTagName("td")[j];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    matchFound = true;
                }
            }
        }

        if (matchFound) {
            tr[i].style.display = "";
        } else {
            tr[i].style.display = "none";
        }
    }
});


// JavaScript for filtering the cashless donations table based on user input
document.getElementById("searchInputCashless").addEventListener("keyup", function() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchInputCashless");
    filter = input.value.toUpperCase();
    table = document.getElementById("tableBodyCashless");
    tr = table.getElementsByTagName("tr");

    // Loop through all table rows, and hide those who don't match the search query
    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[1]; // Index 1 is the column for donor name
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
});

function searchDonations() {
    // Declare variables
    var input, filter, tables, i, j, tr, td, txtValue;
    input = document.getElementById("search-input");
    filter = input.value.toUpperCase();
    tables = document.querySelectorAll(".transaction-table");

    // Loop through all tables
    for (i = 0; i < tables.length; i++) {
        var tbody = tables[i].getElementsByTagName("tbody");
        if (tbody.length > 0) {
            tr = tbody[0].getElementsByTagName("tr");

            // Loop through all table rows, and hide those who don't match the search query
            for (j = 0; j < tr.length; j++) {
                td = tr[j].getElementsByTagName("td")[2]; // Index 2 for donor column
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[j].style.display = "";
                    } else {
                        tr[j].style.display = "none";
                    }
                }
            }
        }
    }
}


function viewDetails(donor) {
    // Hide donor history section
    document.getElementById('donor-history').style.display = 'none';
    
    // Show donor details section
    document.getElementById('donor-details').style.display = 'block';
    
    // Load donor details via AJAX or set the donor in a query parameter
    document.getElementById('details-content').innerHTML = '<?php echo addslashes("<p>Loading...</p>"); ?>';
    
    // Make AJAX call to load donor details or redirect to PHP page
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById('details-content').innerHTML = this.responseText;
        }
    };
    xhr.open("GET", "fetch_donor_details.php?donor=" + donor, true);
    xhr.send();
}

window.onload = function () {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById('donor-history-table').innerHTML = this.responseText;
        }
    };
    xhr.open("GET", "fetch_donor_details.php", true);
    xhr.send();
};


function closeDetails() {
    // Hide donor details section
    document.getElementById('donor-details').style.display = 'none';
    
    // Show donor history section
    document.getElementById('donor-history').style.display = 'block';
}

function sendAppreciationLetter() {
    var donor = document.getElementById("donor-name").innerText; // Assuming you have an element with the donor's name
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "generate_letter.php?donor=" + encodeURIComponent(donor), true);
    xhr.responseType = 'blob';

    xhr.onload = function () {
        if (xhr.status === 200) {
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(xhr.response);
            link.download = "appreciation_letter_" + donor + ".doc";
            link.click();
        } else {
            alert("Failed to generate appreciation letter: " + xhr.responseText);
        }
    };

    xhr.send();
}

document.getElementById('user-settings-link').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent the default link behavior
    
    // Ask for confirmation
    if (confirm('Do you want to go to User Settings?')) {
        window.location.href = this.getAttribute('href'); // Navigate to user.php
    } else {
        window.location.href = 'home.php'; // Navigate to home.php
    }
});


const userInfo = document.querySelector('.user-info');

    // Add click event listener
    userInfo.addEventListener('click', function() {
        // Ask for confirmation
        if (confirm('Do you want to go to User Settings?')) {
            window.location.href = 'user.php'; // Redirect to user.php
        }
    });

function toggleLogout() {
    var logoutBtn = document.getElementById('logout');
    logoutBtn.style.display = (logoutBtn.style.display === 'none' || logoutBtn.style.display === '') ? 'inline-block' : 'none';
}

document.getElementById('logout-link').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent the default link action

    // Show confirmation dialog
    var confirmLogout = confirm('Are you sure you want to logout?');

    // If user confirms logout
    if (confirmLogout) {
        window.location.href = 'logout.php'; // Redirect to logout page
    } else {
        // Redirect to dashboard if logout is canceled
        window.location.href = 'home.php'; // Replace with your dashboard URL
    }
});

    </script>

</body>
</html>