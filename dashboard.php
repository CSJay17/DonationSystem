<?php
    // Include your database connection here
    include('config.php');

    // Query to fetch total amount from cash table
    $queryTotalAmount = "SELECT SUM(amount) AS amount FROM cash";
    $resultTotalAmount = mysqli_query($conn, $queryTotalAmount);
    $rowTotalAmount = mysqli_fetch_assoc($resultTotalAmount);
    $totalAmount = $rowTotalAmount['mount'];

    // Query to fetch total quantity from cashless table
    $queryTotalQuantity = "SELECT SUM(quantity) AS quantity FROM cashless";
    $resultTotalQuantity = mysqli_query($conn, $queryTotalQuantity);
    $rowTotalQuantity = mysqli_fetch_assoc($resultTotalQuantity);
    $totalQuantity = $rowTotalQuantity['quantity'];

    // Query to fetch total number of users
    $queryTotalUsers = "SELECT COUNT(*) AS users FROM users";
    $resultTotalUsers = mysqli_query($conn, $queryTotalUsers);
    $rowTotalUsers = mysqli_fetch_assoc($resultTotalUsers);
    $totalUsers = $rowTotalUsers['users'];
?>