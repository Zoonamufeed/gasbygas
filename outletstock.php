<?php
session_start();
require("includes/databaseconnect.php");

// Fetch outlet details and available stock from the database, only if the outlet status is "approved"
$outlet_Query = mysqli_query($conn, "SELECT * FROM `outlet` WHERE status = 'approve'") or die('Query Failed');

// Display the updated stock values
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php include "includes/header_links.php"; ?>
    <link rel="stylesheet" href="css/outletstock.css"/>
    <title>HO Outlet Stock</title>
</head>
<body>
    <!-- Navbar -->
    <?php include "includes/headofficenavbar.php"; ?>

    <!-- Message display for approval -->
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

    <!-- Outlet Stock Table -->
    <div class="viewoutletstock-table table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                    <th scope="col" rowspan="2">Outlet Branch</th>
                    <th scope="col" rowspan="2">Contact No</th>
                    <th scope="col" colspan="4" class="stock-header">Stock</th>
                    <th scope="col" colspan="4" class="available-header">Available</th>
                  </tr>
                  <tr>
                    <th scope="col">2kg</th>
                    <th scope="col">5kg</th>
                    <th scope="col">12.5kg</th>
                    <th scope="col">37.5kg</th>
                    <th scope="col">2kg</th>
                    <th scope="col">5kg</th>
                    <th scope="col">12.5kg</th>
                    <th scope="col">37.5kg</th>
                  </tr>
            </thead>
            <tbody>
            <?php
            // Fetch approved outlets only
            $select_outlet = mysqli_query($conn, "SELECT * FROM `outlet` WHERE status = 'approve'") or die('Query failed');
            if (mysqli_num_rows($select_outlet) > 0) {
                while ($row = mysqli_fetch_assoc($select_outlet)) {
                    ?>
                    <tr>
                        <td><?php echo $row['preferred_outlet']; ?></td>
                        <td><?php echo $row['contact_no']; ?></td>
                        <td><?php echo $row['weight_2kg']; ?></td>
                        <td><?php echo $row['weight_5kg']; ?></td>
                        <td><?php echo $row['weight_12_5kg']; ?></td>
                        <td><?php echo $row['weight_37_5kg']; ?></td>
                        <td><?php echo $row['available_weight_2kg']; ?></td>
                        <td><?php echo $row['available_weight_5kg']; ?></td>
                        <td><?php echo $row['available_weight_12_5kg']; ?></td>
                        <td><?php echo $row['available_weight_37_5kg']; ?></td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='10'>No approved outlet data available.</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <?php include "includes/outletfooter.php"; ?>
</body>
</html>
