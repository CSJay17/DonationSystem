<?php
session_start();
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = mysqli_real_escape_string($conn, $_POST['token']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Hash the passwor

    // Update the password in the database
    $query = "UPDATE users SET password='$password', reset_token='' WHERE reset_token='$token'";
    if (mysqli_query($conn, $query)) {
        // Password updated successfully
        header("location: index.php?success=Password reset successfully. Please login with your new password.");
        exit;
    } else {
        // Error updating password
        header("location: reset_password.php?token=$token&error=Error resetting password.");
        exit;
    }
}
?>

<?php
session_start();
include('config.php');

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Display reset password form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reset Password</title>
        <!-- Include Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Reset Password</div>
                        <div class="card-body">
                            <?php
                            if (isset($_GET['error'])) {
                                echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_GET['error']) . '</div>';
                            }
                            ?>
                            <form action="reset_password.php" method="POST">
                                <input type="hidden" name="token" value="<?php echo $token; ?>">
                                <div class="form-group">
                                    <label for="password">New Password</label>
                                    <input type="password" id="password" name="password" class="form-control" required>
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
    <?php
} else {
    header("location: forgot_password.php");
    exit;
}
?>
