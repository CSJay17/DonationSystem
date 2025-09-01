<?php
// edit_cashless.php

include('config.php');

if (isset($_GET['id'])) {
    $edit_id = $_GET['id'];

    // Fetch the details of the specific cashless donation to edit
    $edit_sql = "SELECT * FROM cashless WHERE id = $edit_id";
    $edit_result = mysqli_query($conn, $edit_sql);
    $edit_row = mysqli_fetch_assoc($edit_result);

    if (!$edit_row) {
        echo '<script>alert("Cashless donation not found.");</script>';
        echo '<script>window.location = "home.php#cashless-donations";</script>';
        exit;
    }
} else {
    echo '<script>alert("No cashless donation selected for editing.");</script>';
    echo '<script>window.location = "home.php#cashless-donations";</script>';
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $donor = $_POST['donor'];
    $contact = $_POST['contact'];
    $type_of_donation = $_POST['type_of_donation'];
    $quantity = $_POST['quantity'];
    $date = $_POST['date'];
    $donation_for = $_POST['donation_for'];

    // Update the cashless donation
    $update_sql = "UPDATE cashless SET donor='$donor', contact='$contact', type_of_donation='$type_of_donation', quantity='$quantity', date='$date', donation_for='$donation_for' WHERE id=$id";
    if (mysqli_query($conn, $update_sql)) {
        echo '<script>alert("Cashless donation updated successfully.");</script>';
        echo '<script>window.location = "home.php#cashless-donations";</script>';
    } else {
        echo '<script>alert("Error updating cashless donation.");</script>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Cashless Donation</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

    <div class="form-container active">
    <h2>Edit Cashless Donation</h2>
    <form action="edit_cashless.php?id=<?php echo $edit_id; ?>" method="post">
        <input type="hidden" name="id" value="<?php echo $edit_row['id']; ?>">

        <input type="text" id="donor" name="donor" placeholder="Donor Name" value="<?php echo $edit_row['donor']; ?>" required>
        
        <input type="text" id="contact" name="contact" placeholder="Contact Number" value="<?php echo $edit_row['contact']; ?>" required>
        
        <input type="text" id="type_of_donation" name="type_of_donation" placeholder="Type of Donation" value="<?php echo $edit_row['type_of_donation']; ?>" required>
        
        <input type="number" id="quantity" name="quantity" placeholder="Quantity" value="<?php echo $edit_row['quantity']; ?>" required>
        
        <input type="date" id="date" name="date" placeholder="Date" value="<?php echo $edit_row['date']; ?>" required>
        
        <input type="text" id="donation_for" name="donation_for" placeholder="Donation For" value="<?php echo $edit_row['donation_for']; ?>" required>
        
        <button type="submit" name="update_cashless">Update</button>
    </form>
</div>


</body>
</html>
