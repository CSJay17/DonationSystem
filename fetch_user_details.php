<?php
session_start();
include 'config.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gkdb";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch user details based on session username
    $user = [];
    if (isset($_SESSION['username'])) {
        $stmt = $pdo->prepare("SELECT `id`, `username`, `email` FROM `users` WHERE `username` = :username");
        $stmt->bindParam(':username', $_SESSION['username']);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
