<?php
session_start();
include 'includes/databaseconnect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php include "includes/header_links.php"; ?>
    <link rel="stylesheet" href="css/paymentdisplay.css">
    <title>Payment Display</title>
</head>
<body>
    <!-- Navbar -->
    <?php include "includes/outletnavbar.php"; ?>

    <!-- Message display -->
    <?php
    if (isset($_SESSION['message'])) {
        $messageType = $_SESSION['message']['type']; 
        $messageText = $_SESSION['message']['text']; 
        echo "
        <div class='alert alert-$messageType alert-dismissible fade show' role='alert'>
            <strong>Report!</strong> $messageText
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
        unset($_SESSION['message']);
    }
    ?>

    <!-- Payment Display Table -->
    <div class="payment-table">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Token No</th>
                    <th scope="col">User Type</th>
                    <th scope="col">Weight</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch payment records from the database
                $query = "SELECT token, user_type, weight, quantity, amount FROM payments";
                $result = mysqli_query($conn, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                                <td>{$row['token']}</td>
                                <td>{$row['user_type']}</td>
                                <td>{$row['weight']}</td>
                                <td>{$row['quantity']}</td>
                                <td>{$row['amount']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr>
                            <td colspan='5' class='text-center'>No payment records found.</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <?php include "includes/outletfooter.php"; ?>
</body>
</html>
