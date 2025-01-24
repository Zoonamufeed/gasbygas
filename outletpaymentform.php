<?php
session_start();
include 'includes/databaseconnect.php';

// Initialize variables
$userType = '';
$email = '';
$weight = '';
$quantity = '';
$amount = 0;
$tokenNo = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Logic for displaying token details
    if (isset($_POST['fetch_details']) && isset($_POST['token_no'])) {
        $tokenNo = $_POST['token_no'];

        // Fetch customer or business details based on token number
        $queryCustomer = "SELECT name AS user_type, weight, quantity, email FROM customer WHERE token = '$tokenNo'";
        $resultCustomer = mysqli_query($conn, $queryCustomer);

        if ($resultCustomer && mysqli_num_rows($resultCustomer) > 0) {
            $row = mysqli_fetch_assoc($resultCustomer);
            $userType = $row['user_type'];
            $weight = $row['weight'];
            $quantity = $row['quantity'];
            $email = $row['email'];
        } else {
            $queryBusiness = "SELECT businessname AS user_type, weight, quantity, email FROM business WHERE token = '$tokenNo'";
            $resultBusiness = mysqli_query($conn, $queryBusiness);

            if ($resultBusiness && mysqli_num_rows($resultBusiness) > 0) {
                $row = mysqli_fetch_assoc($resultBusiness);
                $userType = $row['user_type'];
                $weight = $row['weight'];
                $quantity = $row['quantity'];
                $email = $row['email'];
            } else {
                $_SESSION['message'] = [
                    'type' => 'danger',
                    'text' => 'Invalid token number.',
                ];
                header("Location: outletpaymentform.php");
                exit;
            }
        }

        // Define prices for each weight
        $prices = [
            '2KG' => 620,
            '5KG' => 1550,
            '12.5KG' => 3200,
            '37.5KG' => 5000,
        ];

        // Calculate amount
        $pricePerUnit = $prices[$weight] ?? 0;
        $amount = $pricePerUnit * $quantity;
    }

    // Logic for submitting the payment
    if (isset($_POST['submit_payment'])) {
        $tokenNo = $_POST['token_no'];
        $userType = $_POST['user_type'];
        $weight = $_POST['weight'];
        $quantity = $_POST['quantity'];
        $amount = $_POST['amount'];
        $email = $_POST['email'];

        // Prevent duplicate payment by checking existing token
        $checkPayment = "SELECT * FROM payments WHERE token = '$tokenNo'";
        $resultPayment = mysqli_query($conn, $checkPayment);

        if ($resultPayment && mysqli_num_rows($resultPayment) > 0) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Payment already recorded for this token number.',
            ];
        } else {
            // Insert payment into the database
            $insertPayment = "INSERT INTO payments (token, user_type, weight, quantity, amount, email) 
                              VALUES ('$tokenNo', '$userType', '$weight', '$quantity', '$amount', '$email')";

            if (mysqli_query($conn, $insertPayment)) {
                $_SESSION['message'] = [
                    'type' => 'success',
                    'text' => 'Payment recorded successfully.',
                ];
            } else {
                $_SESSION['message'] = [
                    'type' => 'danger',
                    'text' => 'Error recording payment: ' . mysqli_error($conn),
                ];
            }
        }

        // Clear form fields after submission
        header("Location: outletpaymentform.php");
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php include "includes/header_links.php"; ?>
    <link rel="stylesheet" href="css/outletpaymentform.css"/>
    <title>Customer Payment</title>
</head>
<body>
    <!-- navbar -->
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
    
  <!-- Onsite Payment Form -->
  <div class="outlet-form">
        <form method="POST" action="">
            <div class="mb-3">
                <h2 style="text-align: center; font-family: bootstrap-icon;">Gas Payment Form</h2>
            </div>
            <div class="mb-3">
                <label for="token-no" class="form-label">Token No</label>
                <input type="text" class="form-control" id="token-no" name="token_no" value="<?php echo htmlspecialchars($tokenNo); ?>" required>
                <br>
                <button type="submit" name="fetch_details" class="btn btn-primary">Enter Token Number</button>
            </div>
            <div class="mb-3">
                <label for="user-type" class="form-label">Individual User / Business User</label>
                <input type="text" class="form-control" id="user-type" name="user_type" value="<?php echo htmlspecialchars($userType); ?>" readonly>
            </div>
            <div class="mb-3">   
                <label for="weight" class="form-label">Weight</label>
                <input type="text" class="form-control" id="weight" name="weight" value="<?php echo htmlspecialchars($weight); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="text" class="form-control" id="quantity" name="quantity" value="<?php echo htmlspecialchars($quantity); ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="display-amount" class="form-label">Amount</label>
                <input class="form-control" type="text" id="display-amount" name="amount" value="<?php echo htmlspecialchars($amount); ?>" readonly>
            </div>
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <button type="submit" name="submit_payment" class="btn btn-primary">Submit</button>
        </form>
    </div>
    
    <!-- Footer -->
    <?php include "includes/outletfooter.php"; ?>
    
</body>
</html>