<?php
// edit_expense.php

include('config.php');

if (isset($_GET['id'])) {
    $edit_id = $_GET['id'];

    // Fetch the details of the specific expense to edit
    $edit_sql = "SELECT * FROM expense WHERE id = $edit_id";
    $edit_result = mysqli_query($conn, $edit_sql);
    $edit_row = mysqli_fetch_assoc($edit_result);

    if (!$edit_row) {
        echo '<script>alert("Expense not found.");</script>';
        echo '<script>window.location = "home.php#expenses";</script>';
        exit;
    }
} else {
    echo '<script>alert("No expense selected for editing.");</script>';
    echo '<script>window.location = "home.php#expenses";</script>';
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $item = $_POST['item'];
    $price = $_POST['price'];
    $date = $_POST['date'];
    $details = $_POST['details'];

    // Update the expense
    $update_sql = "UPDATE expense SET item='$item', price='$price', date='$date', details='$details' WHERE id=$id";
    if (mysqli_query($conn, $update_sql)) {
        echo '<script>alert("Expense updated successfully.");</script>';
        echo '<script>window.location = "home.php#expenses";</script>';
    } else {
        echo '<script>alert("Error updating expense.");</script>';
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Expense</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        form {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-top: 10px;
        }

        input[type="text"],
        input[type="date"],
        textarea {
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
            box-sizing: border-box;
        }

        input[type="submit"] {
            padding: 10px;
            background: #007bff;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

    <h2>Edit Expense</h2>
    <form action="edit_expense.php?id=<?php echo $edit_id; ?>" method="post">
        <input type="hidden" name="id" value="<?php echo $edit_row['id']; ?>">
        <label for="item">Item:</label>
        <input type="text" id="item" name="item" value="<?php echo htmlspecialchars($edit_row['item']); ?>" required>
        <br>
        <label for="price">Price:</label>
        <input type="text" id="price" name="price" value="<?php echo htmlspecialchars($edit_row['price']); ?>" required>
        <br>
        <label for="date">Date:</label>
        <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($edit_row['date']); ?>" required>
        <br>
        <label for="details">Details:</label>
        <textarea id="details" name="details" required><?php echo htmlspecialchars($edit_row['details']); ?></textarea>
        <br>
        <input type="submit" value="Update Expense">
    </form>

</body>
</html>
