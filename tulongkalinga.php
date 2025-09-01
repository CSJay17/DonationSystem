<?php
include('config.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donor = htmlspecialchars($_POST['donor']);
    $contact = htmlspecialchars($_POST['contact']);
    $amount = htmlspecialchars($_POST['amount']);
    $date = htmlspecialchars($_POST['date']);
    $purpose = htmlspecialchars($_POST['purpose']);
    $nameof = htmlspecialchars($_POST['nameof']);
    $mod_of = htmlspecialchars($_POST['mod_of']);

    // Store data in a temporary table or storage
    $sql = "INSERT INTO temporary_donations (donor, contact, amount, date, purpose, nameof, mod_of) 
            VALUES ('$donor', '$contact', '$amount', '$date', '$purpose', '$nameof', '$mod_of')";
    
    if ($conn->query($sql) === TRUE) {
        echo '<script>alert("Donation added successfully! Please wait for admin approval.");</script>';
        echo '<script>window.location = "home.php";</script>';
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
// Fetch 5 most recent records from 'cash' table
$sql = "SELECT `id`, `donor`, `contact`, `amount`, `mod_of`, `purpose`, `date`, `nameof`
        FROM `cash`
        ORDER BY `id` DESC
        LIMIT 5";

$result = $conn->query($sql);

$recent_records = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $recent_records[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donate Today</title>
    <link rel="icon" href="images/3.png" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            color: #343a40;
        }

        .header {
            background-color: #141A1E;
            color: #fff;
            padding: 16px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .header .header-content {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .header .logo {
            width: 50px; /* Adjust size as needed */
            height: auto;
            margin-right: 10px; /* Adjust spacing */
        }

        .header h1 {
            margin: 0;
            font-size: 32px; /* Adjust font size */
            font-weight: bold;
        }

        .hero-section {
            color: #343a40;
            padding: 100px 20px;
            text-align: center;
        }

        .hero-section h2 {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .hero-section p {
            font-size: 18px;
            margin-bottom: 40px;
        }

        .cta-button {
            background-color: #007bff;
            color: #fff;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
            font-size: 18px;
            text-decoration: none;
        }

        .cta-button:hover {
            background-color: #0056b3;
        }

        .carousel-item img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .donor-section {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 50px auto;
            text-align: center;
        }

        .donor-section h2 {
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }

        .donor-table {
            margin-top: 20px;
        }

        .donor-table th, .donor-table td {
            vertical-align: middle;
            padding: 8px;
        }

        .donation-section {
            background-color: #fff;
            padding: 50px 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 50px auto;
            text-align: center;
        }

        .donation-section h2 {
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }

        .donation-form {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f1f1f1;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .donation-form label {
            font-weight: bold;
            margin-bottom: 10px;
            display: block;
            text-align: left;
        }

        .donation-form input, .donation-form textarea {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .donate-button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
            font-size: 18px;
        }

        .donate-button:hover {
            background-color: #0056b3;
        }

     .background-section {
    position: relative;
    text-align: center;
    color: #fff;
    padding: 100px 20px;
    background: none; /* Remove background color */
    overflow: hidden; /* Ensure content doesn't overflow */
}

.background-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('images/6.jpg'); /* Replace with your image path */
    background-size: cover;
    background-position: center;
    opacity: 0.8; /* Adjust opacity as desired */
    z-index: -1;
}

.background-section .content {
    position: relative;
    z-index: 1;
}

.background-section h2 {
    font-size: 48px;
    margin-bottom: 20px;
}

.background-section p {
    font-size: 18px;
    margin-bottom: 40px;
}

.background-section .cta-button {
    background-color: #fff;
    color: #007bff;
    padding: 15px 30px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease, color 0.3s ease;
    font-size: 18px;
    text-decoration: none;
}

.background-section .cta-button:hover {
    background-color: #e3e3e3;
    color: #007bff;
}

        .footer {
            text-align: center;
            padding: 20px;
            background-color: #141A1E;
            color: #fff;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <img src="images/3.png" alt="Logo" class="logo">
            <h1>Donate Today</h1>
        </div>
    </header>

    <main class="main-content">

        <section id="background-section" class="background-section">
            <div class="background-bg"></div>
            <div class="content">
                <h2>Gawad Kalinga Laguna</h2>
                <p>See how your donations make a difference.</p>
                <a href="#" class="cta-button">Learn More</a>
            </div>
        </section>


        <section class="hero-section">
            <div class="content">
                <h2>Our Impact</h2>
                <p>See how your donations make a difference.</p>
                <a href="#" class="cta-button">Learn More</a>
            </div>
            <br>
            <h2>Support Our Cause</h2>
            <p>Your generous donation helps us continue our work.</p>
            <a href="#donation-section" class="cta-button">Donate Now</a>

            <div id="carouselExampleIndicators" class="carousel slide mt-5" data-ride="carousel">
                <ol class="carousel-indicators">
                    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                </ol>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img class="d-block mx-auto" src="images/4.jpg" alt="First slide">
                    </div>
                    <div class="carousel-item">
                        <img class="d-block mx-auto" src="images/5.jpg" alt="Second slide">
                    </div>
                    <div class="carousel-item">
                        <img class="d-block mx-auto" src="images/8.jpg" alt="Third slide">
                    </div>
                </div>
                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </section>

        <section class="donor-section">
            <h2>Recent Donations</h2>
            <div class="donor-table">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Donor</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Purpose</th>
                            <th scope="col">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_records as $record): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['donor']); ?></td>
                                <td><?php echo number_format($record['amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($record['purpose']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($record['date'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section id="donation-section" class="donation-section">
            <h2>Make a Donation</h2>
            <form class="donation-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="donor">Name</label>
                <input type="text" id="donor" name="donor" required>

                <label for="contact">Email/Phone</label>
                <input type="text" id="contact" name="contact" required>

                <label for="amount">Donation Amount</label>
                <input type="number" id="amount" name="amount" required>

                <label for="date">Date</label>
                <input type="date" id="date" name="date" required>

                <label for="purpose">Purpose</label>
                <input type="text" id="purpose" name="purpose" required>

                <label for="nameof">Name of Project</label>
                <input type="text" id="nameof" name="nameof" required>

                <label for="mod_of">Mode of Donation</label>
                <input type="text" id="mod_of" name="mod_of" required>

                <button type="submit" class="donate-button">Donate</button>
            </form>
        </section>
    </main>
    <footer class="footer">
        <p>&copy; 2024 Donate Today. All rights reserved.</p>
    </footer>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
