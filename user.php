<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Settings</title>
    <!-- Include FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-biDE1s3e1mOBv41zrKLEk2ZRrhcKMmP94sxU76vJB92/YFQEW1aQaVMDEG0a0vKk3bFFHw0nQKwW88Wf3jyY5g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style type="text/css">
        /* Include your CSS styles here */
        /* General styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .admin-content {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            max-width: 600px;
            padding: 20px;
        }

        h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .form-container {
            margin-bottom: 20px;
        }

        .form-container.active {
            display: block;
        }

        .form-container h3 {
            border-bottom: 2px solid #ccc;
            color: #555;
            font-size: 20px;
            margin-bottom: 10px;
            padding-bottom: 10px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="date"] {
            width: calc(100% - 20px);
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        button[type="submit"],
        button[type="button"] {
            background-color: #007bff;
            border: none;
            color: #fff;
            cursor: pointer;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 16px;
        }

        button[type="submit"]:hover,
        button[type="button"]:hover {
            background-color: #0056b3;
        }

        .user-info {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .user-info i {
            margin-right: 10px;
        }

        .user-info p {
            margin: 0;
            font-size: 16px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .admin-content {
                padding: 10px;
            }

            input[type="text"],
            input[type="email"],
            input[type="password"],
            input[type="number"],
            input[type="date"] {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="admin-content" id="user-settings">
        <!-- Content for User Settings -->
        <h2>User Settings</h2>

        <!-- Update Profile Information Form -->
        <div class="form-container active">
            <h3>Update Profile Information</h3>
            <form action="update_profile.php" method="post">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo isset($user['username']) ? htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') : ''; ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo isset($user['email']) ? htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') : ''; ?>" required>

                <button type="submit" name="update_profile">Update Profile</button>
            </form>
        </div>

        <!-- Change Password Form -->
        <div class="form-container active">
            <h3>Change Password</h3>
            <form action="change_password.php" method="post">
                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password" required>

                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>

                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>

                <button type="submit" name="change_password">Change Password</button>
            </form>
        </div>

        <!-- Add New Administrator Form -->
        <div class="form-container active">
            <h3>Add New Administrator</h3>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <label for="new_username">Username:</label>
                <input type="text" id="new_username" name="new_username" required>

                <label for="new_email">Email:</label>
                <input type="email" id="new_email" name="new_email" required>

                <label for="new_password">Password:</label>
                <input type="password" id="new_password" name="new_password" required>

                <label for="new_role">Role:</label>
                <select id="new_role" name="new_role" required>
                    <option value="admin">Admin</option>
                    <option value="moderator">Moderator</option>
                </select>

                <button type="submit" name="add_admin">Add Administrator</button>
            </form>
        </div>
    </div>
</body>
</html>
