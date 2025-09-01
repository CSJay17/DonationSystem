<?php
session_start();
include 'config.php';

    

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if user exists in the database
    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['email'] = $email;

        // Check specific email addresses for redirection
        $allowed_emails = array("cristobal.j.bscs@gmail.com", "pombo.aj.bscs@gmail.com", "ico.ml.bscs@gmail.com", "pajimola.dr.bscs@gmail.com", "minozo.js.bscs@gmail.com", "gallanosa.jr.bscs@gmail.com");
        if (in_array($email, $allowed_emails) || $user['role'] == 'admin' || $user['role'] == 'moderator') {
            header("Location: home.php");
        } else {
            header("Location: tulongkalinga.php");
        }
    } else {
        echo "<script>alert('Invalid email or password.');</script>";
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tulong Kalinga - Login</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            display: flex;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 80%; /* Adjusted width */
            max-width: 600px; /* Added max-width */
            height: 70vh; /* Adjusted height */
        }
        .left {
            background-image: url('images/1.jpg'); /* Change this to your image path */
            background-size: cover;
            background-position: center;
            width: 50%;
            height: 100%;
        }
        .right {
            padding: 20px;
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .right img {
            max-width: 150px; /* Adjust the size of the logo as needed */
            max-height: 150px; /* Adjust the size of the logo as needed */
            margin-bottom: 20px;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        input[type="email"], input[type="password"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* Ensure padding and border are included in the width */
        }
        input[type="submit"] {
            background-color: #ffc107;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #e0a800;
        }
        .register-link {
            text-align: center;
            margin-top: 10px;
        }
        .register-link a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left"></div>
        <div class="right">
            <BR>
            <img src="images/3.png" alt="Logo"> <!-- Add your logo image path here -->
            <h2>Login</h2>
            <form action="index.php" method="post">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="submit" value="Login">
            </form>
            <div class="register-link">
                <a href="forgot_password.php">Forgot Password</a>
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </div>
</body>
</html>
