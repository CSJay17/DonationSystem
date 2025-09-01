<?php
// edit_cash.php

include('config.php');
session_start(); // Ensure session is started

if (isset($_GET['id'])) {
    $edit_id = $_GET['id'];

    // Fetch the details of the specific cash donation to edit
    $edit_sql = "SELECT * FROM cash WHERE id = $edit_id";
    $edit_result = mysqli_query($conn, $edit_sql);
    $edit_row = mysqli_fetch_assoc($edit_result);

    if (!$edit_row) {
        echo '<script>alert("Cash donation not found.");</script>';
        echo '<script>window.location = "home.php#cash-donations";</script>';
        exit;
    }
} else {
    echo '<script>alert("No cash donation selected for editing.");</script>';
    echo '<script>window.location = "home.php#cash-donations";</script>';
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $donor = $_POST['donor'];
    $contact = $_POST['contact'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $purpose = $_POST['purpose'];
    $nameof = $_POST['nameof']; // Get the username from session
    $mod_of = $_POST['mod_of']; // Get mod_of from the form input

    // Update the cash donation
    $update_sql = "UPDATE cash SET donor='$donor', contact='$contact', amount='$amount', date='$date', purpose='$purpose', nameof='$nameof', mod_of='$mod_of' WHERE id=$id";
    if (mysqli_query($conn, $update_sql)) {
        echo '<script>alert("Cash donation updated successfully.");</script>';
        echo '<script>window.location = "home.php#cash-donations";</script>';
    } else {
        echo '<script>alert("Error updating cash donation.");</script>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Cash Donation</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

    <div class="form-container active">
    <h2>Edit Cash Donation</h2>
    <form action="edit_cash.php?id=<?php echo $edit_id; ?>" method="post">
        <input type="hidden" name="id" value="<?php echo $edit_row['id']; ?>">

        <input type="text" id="donor" name="donor" placeholder="Donor Name" value="<?php echo $edit_row['donor']; ?>" required>
        
        <input type="text" id="contact" name="contact" placeholder="Contact Number" value="<?php echo $edit_row['contact']; ?>" required>
        
        <input type="number" id="amount" name="amount" placeholder="Amount" value="<?php echo $edit_row['amount']; ?>" required>
        
        <input type="text" id="mod_of" name="mod_of" placeholder="Mod of Donation" value="<?php echo $edit_row['mod_of']; ?>" required>
        
        <input type="date" id="date" name="date" placeholder="Date" value="<?php echo $edit_row['date']; ?>" required>
        
        <input type="text" id="purpose" name="purpose" placeholder="Purpose" value="<?php echo $edit_row['purpose']; ?>" required>

        <input type="text" id="nameof" name="nameof" placeholder="Added By" value="<?php echo $edit_row['nameof']; ?>" required>

        <button type="submit" name="update">Update</button>
    </form>
</div>


</body>
</html>
