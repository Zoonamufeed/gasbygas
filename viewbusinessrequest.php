<?php
session_start();
require("includes/databaseconnect.php");
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// based on approve button
if (isset($_GET['approve'])) {
    $businessId = $_GET['approve'];
    $approvedDate = date('Y-m-d H:i:s');
    $dueDate = date('Y-m-d H:i:s', strtotime('+2 weeks'));

    // Fetch the highest token from both business and customer tables
    $tokenQuery = mysqli_query($conn, "
        SELECT MAX(token) AS max_token FROM (
            SELECT token FROM `business`
            UNION ALL
            SELECT token FROM `customer`
        ) AS combined_tokens
    ");
    $tokenResult = mysqli_fetch_assoc($tokenQuery);
    $lastToken = $tokenResult['max_token'];

    // Generate the next token
    $token = ($lastToken !== null) ? $lastToken + 1 : 1;

    // Fetch business details
    $businessQuery = mysqli_query($conn, "SELECT email, branch, weight, quantity FROM `business` WHERE id = '$businessId'");
    if (!$businessQuery || mysqli_num_rows($businessQuery) === 0) {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'Business not found.',
        ];
        header('Location: viewbusinessrequest.php');
        exit;
    }
    $business = mysqli_fetch_assoc($businessQuery);
    $businessEmail = $business['email'];
    $businessBranch = $business['branch'];
    $requestedWeight = (float) filter_var($business['weight'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION); // Convert to float
    $requestedQuantity = $business['quantity'];

    // Fetch outlet details (stock and delivery date)
    $outletQuery = mysqli_query($conn, "SELECT * FROM `outlet` WHERE preferred_outlet = '$businessBranch'");
    if (!$outletQuery || mysqli_num_rows($outletQuery) === 0) {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'No outlet found for the selected branch.',
        ];
        header('Location: viewbusinessrequest.php');
        exit;
    }
    $outlet = mysqli_fetch_assoc($outletQuery);

    // Map weights to available stock in the outlet
    $availableStock = [
        '2' => $outlet['available_weight_2kg'],
        '5' => $outlet['available_weight_5kg'],
        '12.5' => $outlet['available_weight_12_5kg'],
        '37.5' => $outlet['available_weight_37_5kg'],
    ];

    // Check if requested stock exceeds available stock
    if ($availableStock[(string)$requestedWeight] < $requestedQuantity) {
        // Show message when requested stock exceeds available stock
        $_SESSION['message'] = [
            'type' => 'warning',
            'text' => "Requested quantity for $requestedWeight KG exceeds available stock. Available: {$availableStock[(string)$requestedWeight]}, Requested: $requestedQuantity KG.",
        ];
        header('Location: viewbusinessrequest.php');
        exit;
    }

    // Deduct stock for the requested weight
    $availableStock[(string)$requestedWeight] -= $requestedQuantity;

    // Update the outlet stock in the database
    $updateOutletQuery = "UPDATE `outlet` SET 
        available_weight_2kg = '{$availableStock['2']}',
        available_weight_5kg = '{$availableStock['5']}',
        available_weight_12_5kg = '{$availableStock['12.5']}',
        available_weight_37_5kg = '{$availableStock['37.5']}'
        WHERE preferred_outlet = '$businessBranch'";

    if (!mysqli_query($conn, $updateOutletQuery)) {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'Failed to update outlet stock. Error: ' . mysqli_error($conn),
        ];
        header('Location: viewbusinessrequest.php');
        exit;
    }

    // Approve the business request
    $updateBusinessQuery = "UPDATE `business` SET 
        approved_date = '$approvedDate',
        due_date = '$dueDate',  
        status = 'Approve', 
        token = '$token' 
        WHERE id = '$businessId'";

    if (mysqli_query($conn, $updateBusinessQuery)) {
        
        // Send email notification to the business owner
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'gasbygas6@gmail.com'; 
            $mail->Password = 'aspi gyyp rlvv itzw'; 
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('gasbygas6@gmail.com');
            $mail->addAddress($businessEmail);

            $mail->isHTML(true);
            $mail->Subject = 'Business Request Approved';
            $mail->Body = "Business Request, <br><br>Your business request has been approved.<br><br>
            <strong>Your token number:</strong> $token. <br><br>
            <strong>Approved Date: </strong>$approvedDate.<br><br>
            <strong>Due Date:</strong> $dueDate<br><br> Thank you!";

            $mail->send();
            $_SESSION['message'] = [
                'type' => 'success',
                'text' => 'Business approved and email sent successfully.',
            ];
        } catch (Exception $e) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => "Business approved but email could not be sent. Mailer Error: {$mail->ErrorInfo}",
            ];
        }
    } else {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'Failed to approve the business request.',
        ];
    }

    header('Location: viewbusinessrequest.php');
    exit;
}



if (isset($_GET['deny'])) {
    $businessId = $_GET['deny'];

    // Fetch business email
    $businessQuery = mysqli_query($conn, "SELECT email FROM `business` WHERE id = '$businessId'");
    $business = mysqli_fetch_assoc($businessQuery);
    $businessEmail = $business['email'];

    // Update business status to 'Deny'
    $updateQuery = "UPDATE `business` SET approved_date = NULL, status = 'Deny' WHERE id = '$businessId'";
    if (mysqli_query($conn, $updateQuery)) {
    
        // Send email notification
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'gasbygas6@gmail.com';
            $mail->Password = 'aspi gyyp rlvv itzw'; 
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('gasbygas6@gmail.com'); 
            $mail->addAddress($businessEmail);

            $mail->isHTML(true);
            $mail->Subject = 'Business Request Denied';
            $mail->Body = "Dear Business Owner, <br><br>Unfortunately, your business request has been denied. 
            <br><br>Thank you!";

            $mail->send();
            $_SESSION['message'] = [
                'type' => 'warning',
                'text' => 'Business request denied and email sent successfully.',
            ];
        } catch (Exception $e) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => "Business request denied but email could not be sent. Mailer Error: {$mail->ErrorInfo}",
            ];
        }
    } else {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'Failed to deny the business request.',
        ];
    }
    header('Location: viewbusinessrequest.php');
    exit;
}

// Fetch business details
$business_query = mysqli_query($conn, "SELECT * FROM `business`") or die('Query failed');
$businesses = [];
if (mysqli_num_rows($business_query) > 0) {
    while ($business_row = mysqli_fetch_assoc($business_query)) {
        $businesses[] = $business_row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Bootstrap -->
    <?php include "includes/header_links.php"; ?>
    <link rel="stylesheet" href="css/viewbusinessrequest.css"/>
    <title>View Business Request</title>
</head>
<body>
    <!-- navbar -->
    <?php include "includes/headofficenavbar.php"; ?>

    <!-- Message display for approve and deny -->
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

    <!-- View Business Request -->
    <div class="viewcustomer-table">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Business Name</th>
                    <th scope="col">Contact No</th>
                    <th scope="col">Weight</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Branch</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $select_business = mysqli_query($conn, "SELECT * FROM `business`") or die('Query failed');
            if (mysqli_num_rows($select_business) > 0) {
                while ($row = mysqli_fetch_assoc($select_business)) {
            ?>
                <tr>
                    <td><?php echo $row['businessname']; ?></td>
                    <td><?php echo $row['contactno'];?></td>
                    <td><?php echo $row['weight'];?></td>
                    <td><?php echo $row['quantity'];?></td>
                    <td><?php echo $row['branch'];?></td>
                    <td>
    <?php 
    if ($row['status'] === 'Approve'): ?>
        <button class="btn btn-success" disabled>Approved</button>
    <?php elseif ($row['status'] === 'Deny'): ?>
        <button class="btn btn-danger" disabled>Deny</button>
    <?php else: ?>
        <a href="viewbusinessrequest.php?approve=<?php echo $row['id']; ?>" class="btn btn-primary">Approve</a>
        <a href="viewbusinessrequest.php?deny=<?php echo $row['id']; ?>" class="btn btn-danger">Deny</a>
    <?php endif; ?>
</td>

                </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='8' class='empty'>No business requests found </td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <?php include "includes/outletfooter.php"; ?>
</body>
</html>
