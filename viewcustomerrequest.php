<?php
session_start();
require("includes/databaseconnect.php");

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;




if (isset($_GET['approve'])) {
    $customerId = $_GET['approve'];
    $approvedDate = date('Y-m-d H:i:s');
    $dueDate = date('Y-m-d H:i:s', strtotime('+2 weeks'));

    $tokenQuery = mysqli_query($conn, "SELECT GREATEST(
        COALESCE((SELECT MAX(token) FROM `customer`), 0), 
        COALESCE((SELECT MAX(token) FROM `business`), 0)
    ) AS max_token");
    $tokenResult = mysqli_fetch_assoc($tokenQuery);
    $lastToken = $tokenResult['max_token'];

    // if the last token is null it Increment the token
    $token = ($lastToken !== null) ? $lastToken + 1 : 1;

    // Fetch customer details
    $customerQuery = mysqli_query($conn, "SELECT email, name, branch, weight, quantity FROM `customer` WHERE id = '$customerId'");
    if (!$customerQuery || mysqli_num_rows($customerQuery) === 0) {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'Customer not found.',
        ];
        header('Location: viewcustomerrequest.php');
        exit;
    }
    $customer = mysqli_fetch_assoc($customerQuery);
    $customerEmail = $customer['email'];
    $customerName = $customer['name'];
    $requestedWeight = (float) filter_var($customer['weight'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $requestedQuantity = $customer['quantity'];
    $customerBranch = $customer['branch'];

    // Fetch outlet details
    $outletQuery = mysqli_query($conn, "SELECT * FROM `outlet` WHERE preferred_outlet = '$customerBranch'");
    if (!$outletQuery || mysqli_num_rows($outletQuery) === 0) {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'No outlet found for the selected branch.',
        ];
        header('Location: viewcustomerrequest.php');
        exit;
    }
    $outlet = mysqli_fetch_assoc($outletQuery);

    // Map weights to available stock
    $availableStock = [
        '2' => $outlet['available_weight_2kg'],
        '5' => $outlet['available_weight_5kg'],
        '12.5' => $outlet['available_weight_12_5kg'],
        '37.5' => $outlet['available_weight_37_5kg'],
    ];

    // Check if requested stock exceeds available stock
    if ($availableStock[(string)$requestedWeight] < $requestedQuantity) {
        $_SESSION['message'] = [
            'type' => 'warning',
            'text' => "Requested quantity for $requestedWeight KG exceeds available stock. Available: {$availableStock[(string)$requestedWeight]}, Requested: $requestedQuantity.",
        ];
        header('Location: viewcustomerrequest.php');
        exit;
    }

    
    $availableStock[(string)$requestedWeight] -= $requestedQuantity;

    
    $updateOutletQuery = "UPDATE `outlet` SET 
        available_weight_2kg = '{$availableStock['2']}',
        available_weight_5kg = '{$availableStock['5']}',
        available_weight_12_5kg = '{$availableStock['12.5']}',
        available_weight_37_5kg = '{$availableStock['37.5']}'
        WHERE preferred_outlet = '$customerBranch'";
    mysqli_query($conn, $updateOutletQuery) or die('Failed to update outlet stock: ' . mysqli_error($conn));

    
    $updateCustomerQuery = "UPDATE `customer` SET 
        approved_date = '$approvedDate', 
        due_date = '$dueDate', 
        status = 'Approve', 
        token = '$token' 
        WHERE id = '$customerId'";
    mysqli_query($conn, $updateCustomerQuery) or die('Failed to update customer approval: ' . mysqli_error($conn));

    // Send approval email
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
        $mail->addAddress($customerEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Order Approved';
        $mail->Body = "
            Dear $customerName,<br><br>
            Your order has been approved. Below are the details:<br><br>
            <strong>Token Number:</strong> $token<br><br>
            <strong>Pickup Date:</strong> $approvedDate<br>
            <strong>Due Date:</strong> $dueDate<br><br>
            Thank you for choosing our service!
        ";

        $mail->send();
        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Order approved successfully. Stock updated and email sent.',
        ];
    } catch (Exception $e) {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => "Order approved but email could not be sent. Mailer Error: {$mail->ErrorInfo}",
        ];
    }

    header('Location: viewcustomerrequest.php');
    exit;
}





if (isset($_GET['deny'])) {
    $customerId = $_GET['deny'];

    // Fetch customer email
    $customerQuery = mysqli_query($conn, "SELECT email FROM `customer` WHERE id = '$customerId'");
    $customer = mysqli_fetch_assoc($customerQuery);
    $customerEmail = $customer['email'];

  
    $updateQuery = "UPDATE `customer` SET approved_date = NULL, status = 'Deny' WHERE id = '$customerId'";
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
            $mail->addAddress($customerEmail);

            $mail->isHTML(true);
            $mail->Subject = 'Order Denied';
            $mail->Body = "Dear Customer, <br><br>Unfortunately, your order has been denied. 
            <br><br>Thank you!";

            $mail->send();
            $_SESSION['message'] = [
                'type' => 'warning',
                'text' => 'Order denied and email sent successfully.',
            ];
        } catch (Exception $e) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => "Order denied but email could not be sent. Mailer Error: {$mail->ErrorInfo}",
            ];
        }
    } else {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'Failed to deny the order.',
        ];
    }
    header('Location: viewcustomerrequest.php');
    exit;
}

// Fetch customer details
$customer_query = mysqli_query($conn, "SELECT * FROM `customer`") or die('Query failed');
$customers = [];
if (mysqli_num_rows($customer_query) > 0) {
    while ($customer_row = mysqli_fetch_assoc($customer_query)) {
        $customers[] = $customer_row;
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
    <link rel="stylesheet" href="css/viewcustomerrequest.css"/>
    <title>View Customer Request</title>
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

    <!-- View Customer Request -->
    <div class="viewcustomer-table">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Contact No</th>
                    <th scope="col">NIC</th>
                    <th scope="col">Weight</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Branch</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $select_customer = mysqli_query($conn, "SELECT * FROM `customer`") or die('Query failed');
            if (mysqli_num_rows($select_customer) > 0) {
                while ($row = mysqli_fetch_assoc($select_customer)) {
            ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['contactno'];?></td>
                    <td><?php echo $row['nic'];?></td>
                    <td><?php echo $row['weight'];?></td>
                    <td><?php echo $row['quantity'];?></td>
                    <td><?php echo $row['branch'];?></td>
                    <td>
                        <?php 
                        if ($row['status'] === 'Approve'): ?>
                            <button class="btn btn-success" disabled>Approved</button>
                        <?php elseif ($row['status'] === 'Deny'): ?>
                            <button class="btn btn-danger" disabled>Denied</button>
                        <?php else: ?>
                            <?php 
                            // Fetch the outlet status for the branch
                            $outletQuery = mysqli_query($conn, "SELECT status FROM `outlet` WHERE preferred_outlet = '{$row['branch']}'");
                            $outlet = mysqli_fetch_assoc($outletQuery);

                            // Show Approve button only if outlet is approved and not denied or approved already
                            if ($outlet['status'] === 'Approve' && $row['status'] !== 'Approve' && $row['status'] !== 'Deny'): ?>
                                <a href="viewcustomerrequest.php?approve=<?php echo $row['id']; ?>" class="btn btn-primary">Approve</a>
                            <?php endif; ?>

                            <!-- Deny button is shown unless the status is Denied -->
                            <?php if ($row['status'] !== 'Deny'): ?>
                                <a href="viewcustomerrequest.php?deny=<?php echo $row['id']; ?>" class="btn btn-danger">Deny</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>



                </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='8' class='empty'>No customer order placed </td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <?php include "includes/outletfooter.php"; ?>
</body>
</html>
