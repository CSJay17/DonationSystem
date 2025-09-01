<?php
session_start();
include('config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if the email exists in your database
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Generate a unique token for password reset
        $token = bin2hex(random_bytes(32));

        // Store the token in the database
        $updateQuery = "UPDATE users SET reset_token='$token' WHERE email='$email'";
        mysqli_query($conn, $updateQuery);

        // Send reset link via email (using Gmail SMTP for testing)
        $resetLink = "http://localhost/gkdms/reset_password.php?token=$token";
        $to = $email;
        $subject = 'Password Reset Link';
        $message = 'Click on the following link to reset your password: ' . $resetLink;

        $smtpUsername = 'cristobal.j.bscs@gmail.com';
        $smtpPassword = 'uwhr iunf snct yiof'; // Use App Passwords for Gmail
        $smtpHost = 'smtp.gmail.com';
        $smtpPort = 465;

        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = $smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtpUsername;
            $mail->Password   = $smtpPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $smtpPort;

            //Recipients
            $mail->setFrom($smtpUsername, 'Your Name');
            $mail->addAddress($to);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;

            $mail->send();
            $_SESSION['success'] = 'Check your email for the password reset link.';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
        }

        // Redirect to a success page
        header("location: forgot_password.php");
        exit;
    } else {
        // Email does not exist
        $_SESSION['error'] = 'Email address not found.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Forgot Password</div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo htmlspecialchars($_SESSION['success']); ?>
                            </div>
                            <?php unset($_SESSION['success']); ?>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($_SESSION['error']); ?>
                            </div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>
                        <form action="forgot_password.php" method="POST">
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary">Reset Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
