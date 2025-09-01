<?php
                include('config.php');

                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $item = $_POST['item'];
                    $price = $_POST['price'];
                    $date = $_POST['date'];

                    $errors = [];
                    if (empty($item)) {
                        $errors[] = "Item is required";
                    }
                    if (empty($price) || !is_numeric($price)) {
                        $errors[] = "Price is required and must be a number";
                    }
                    if (empty($date)) {
                        $errors[] = "Date is required";
                    }

                    if (empty($errors)) {
                        $sql = "INSERT INTO expense (item, price, date) VALUES ('$item', '$price', '$date')";
                        if ($conn->query($sql) === TRUE) {
                            echo '<script>alert("Expense added successfully!");</script>';
                            echo '<script>window.location = "home.php#expenses";</script>';
                        } else {
                            echo "Error: " . $sql . "<br>" . $conn->error;
                        }
                    } else {
                        foreach ($errors as $error) {
                            echo '<p style="color: red;">' . $error . '</p>';
                        }
                    }
                }

                $sql = "SELECT * FROM expense";
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<tr>';
                        echo '<td>' . $row['item'] . '</td>';
                        echo '<td>₱' . number_format($row['price'], 2) . '</td>';
                        echo '<td>' . $row['date'] . '</td>';
                        echo '<td><button class="action-button" onclick="location.href=\'edit_expense.php?action=edit_expense&id=' . $row['id'] . '\'">Edit</button> ';
                        echo '<button class="action-button" onclick="if(confirm(\'Are you sure you want to delete this item?\')) { location.href=\'delete_expense.php?id=' . $row['id'] . '\'; }">Delete</button></td>';

                        echo '</tr>';
                    }
                    echo '</tbody></table>';
                } else {
                    echo "<p>No expenses found.</p>";
                }

                mysqli_close($conn);
                ?>