<?php
session_start();
require("includes/databaseconnect.php");

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

//email for approve
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to send the approval email
function sendEmail($toEmail, $subject, $messageBody) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'gasbygas6@gmail.com';
        $mail->Password = 'aspi gyyp rlvv itzw'; // Replace with App Password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('gasbygas6@gmail.com');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $messageBody;

        // Send the email
        $mail->send();
        return true; // Email sent successfully
    } catch (Exception $e) {
        return false; // Email failed to send
    }
}




//For approve button
if (isset($_GET['approve'])) {
    $outletId = $_GET['approve'];
    // Check if delivery date is set
    $deliveryCheckQuery = mysqli_query
    ($conn, "SELECT delivery_date FROM outlet  WHERE id = '$outletId'");

    if ($deliveryCheckQuery && mysqli_num_rows($deliveryCheckQuery) > 0) {
        $deliveryRow = mysqli_fetch_assoc($deliveryCheckQuery);

        // Check if delivery_date is allocated
        if (empty($deliveryRow['delivery_date'])) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Approval cannot proceed. Please set a delivery date first.'
            ];

            header('Location: headoffice.php');
            exit;
        }
    } else {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'Failed to check the delivery date for the outlet.'
        ];

        header('Location: headoffice.php');
        exit;
    }
    // Fetching email from the admin table
    $outletQuery = mysqli_query($conn, "
        SELECT admin.email 
        FROM outlet 
        JOIN admin ON outlet.user_id = admin.id 
        WHERE outlet.id = '$outletId'
    ");

    if ($outletQuery && mysqli_num_rows($outletQuery) > 0) {
        $outlet = mysqli_fetch_assoc($outletQuery);
        $email = $outlet['email'];
        //message sent when approevd
        $subject = "Gas Request Approved";
        $messageBody = "Dear Outlet,<br><br>Your gas request has been <b>approved</b>. 
        Please contact support for more details.<br>07771234567<br><br>Thank you,<br>Head Office.";

        // Send approval email
        if (sendEmail($email, $subject, $messageBody)) {
            $updateStatus = mysqli_query($conn, "
                UPDATE outlet 
                SET status = 'Approve' 
                WHERE id = '$outletId'
            ");


            if ($updateStatus) {
                $_SESSION['message'] = [
                    'type' => 'success',
                    'text' => 'Approval successful! Email sent to the outlet.'
                ];
            } else {
                $_SESSION['message'] = [
                    'type' => 'danger',
                    'text' => 'Failed to update status in the database.'
                ];
            }
        } else {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Failed to send approval email.'
            ];
        }
    } else {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'Failed to fetch the email for the selected outlet.'
        ];
    }

    header('Location: headoffice.php'); // Redirect to avoid re-execution on refresh
    exit;
}

// Deny button logic
if (isset($_GET['deny'])) {
    $outletId = $_GET['deny'];

    // Fetch the email from the admin table using the user_id from the outlet table
    $outletQuery = mysqli_query($conn, "
        SELECT admin.email 
        FROM outlet 
        JOIN admin ON outlet.user_id = admin.id 
        WHERE outlet.id = '$outletId'
    ");

    if ($outletQuery && mysqli_num_rows($outletQuery) > 0) {
        $outlet = mysqli_fetch_assoc($outletQuery);
        $email = $outlet['email'];

        //email sent when denied
        $subject = "Gas Request Denied";
        $messageBody = "Dear Outlet,<br><br>Your gas request has been <strong>denied</strong>.
         Please contact support for more details.<br>07771234567<br><br>Thank you,<br>Head Office.";

        // Send denial email
        if (sendEmail($email, $subject, $messageBody)) {
            $updateStatus = mysqli_query($conn, "
                UPDATE outlet 
                SET status = 'Deny' 
                WHERE id = '$outletId'
            ");

            if ($updateStatus) {
                $_SESSION['message'] = [
                    'type' => 'success',
                    'text' => 'Request denied successfully! Email sent to the outlet.'
                ];
            } else {
                $_SESSION['message'] = [
                    'type' => 'danger',
                    'text' => 'Failed to update status in the database.'
                ];
            }
        } else {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Failed to send denial email.'
            ];
        }
    } else {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'Failed to fetch the email for the selected outlet.'
        ];
    }

    header('Location: headoffice.php'); // Redirect to avoid re-execution on refresh
    exit;
}

// Deny button logic
if (isset($_GET['deny'])) {
    $outletId = $_GET['deny'];

    // Fetch the email from the admin table using the user_id from the outlet table
    $outletQuery = mysqli_query($conn, "
        SELECT admin.email 
        FROM outlet 
        JOIN admin ON outlet.user_id = admin.id 
        WHERE outlet.id = '$outletId'
    ");

    if ($outletQuery && mysqli_num_rows($outletQuery) > 0) {
        $outlet = mysqli_fetch_assoc($outletQuery);
        $email = $outlet['email'];

        // Send denial email
        if (sendApprovalEmail($email, 'Your gas request has been denied.')) {
            // Update the status in the database
            $updateStatus = mysqli_query($conn, "
                UPDATE outlet 
                SET status = 'Denied' 
                WHERE id = '$outletId'
            ");

            if ($updateStatus) {
                $_SESSION['message'] = [
                    'type' => 'success',
                    'text' => 'Request denied successfully! Email sent to the outlet.'
                ];
            } else {
                $_SESSION['message'] = [
                    'type' => 'danger',
                    'text' => 'Failed to update status in the database.'
                ];
            }
        } else {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Failed to send denial email.'
            ];
        }
    } else {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'Failed to fetch the email for the selected outlet.'
        ];
    }

    header('Location: headoffice.php'); // Redirect to avoid re-execution on refresh
    exit;
}




// Fetch outlet details
$outlet_Query = mysqli_query($conn, "SELECT * FROM `outlet`") or die('Query Failed');
$outlets = [];
if (mysqli_num_rows($outlet_Query) > 0) {
    while ($outlet_row = mysqli_fetch_assoc($outlet_Query)) {
        $outlets[] = $outlet_row;
    }
}

if (!isset($_SESSION['user_id'])) {
    echo "Error: User ID is not set in session.";
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch outlet details from the database
$query = "SELECT preferred_outlet, contact_no, request_date, delivery_date FROM outlet WHERE id = '$userId'";
$result = mysqli_query($conn, $query);

// Initializing the variables
$preferredOutlet = '';
$contactNo = '';
$requestDate = '';
$deliveryDate = '';

// if ($result && mysqli_num_rows($result) > 0) {
//     $row = mysqli_fetch_assoc($result);
//     $preferredOutlet = $row['preferred_outlet'];
//     $contactNo = $row['contact_no'];
//     $requestDate = $row['request_date'];
//     $deliveryDate = $row['delivery_date'];
// } else {
//     $_SESSION['message'] = [
//         'type' => 'danger',
//         'text' => "No outlet data found for user_id: $userId"
//     ];
// }
// Handling the form submission to update the delivery date
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['outlet_id']) && isset($_POST['delivery_date'])) {
    $outlet_id = $_POST['outlet_id'];
    $delivery_date = $_POST['delivery_date'];

    $update_query = "UPDATE `outlet` SET delivery_date = '$delivery_date' WHERE id = '$outlet_id'";
    $update_result = mysqli_query($conn, $update_query);

    if (mysqli_query($conn, $update_query)) {
        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Delivery date updated successfully.'
        ];
        header('Location: headoffice.php'); 
        exit;
    } else {
        $_SESSION['message'] = [
            'type' => 'danger',
             'text' => 'Failed to update delivery date. Please try again.'
            ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php include "includes/header_links.php"; ?>
    <link rel="stylesheet" href="css/headoffice.css"/>
    <title>Head office Homepage</title>
</head>
<body>
    <!-- Navbar -->
    <?php include "includes/headofficenavbar.php"; ?>

    <!-- Message -->
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

    <!-- Gas Approval Table by Headoffice -->
    <div class="viewoutlet-table table-responsive">
        <table class="table table-striped">
        <thead>
        <tr>
          <th scope="col" rowspan="2">Outlet Branch</th>
          <th scope="col" rowspan="2">Contact No</th>
          <th scope="col" rowspan="2">Request Date</th>
          <th scope="col" rowspan="2">Delivery Date</th>
          <th scope="col" colspan="4"  class="quantity-header">Quantity</th>
          <th scope="col" rowspan="2">Stock</th>
          <th scope="col" rowspan="2">Action</th>
        </tr>
        <tr>
          <th scope="col">2kg</th>
          <th scope="col">3kg</th>
          <th scope="col">5kg</th>
          <th scope="col">10kg</th>
        </tr>
      </thead>
            <tbody>
                <?php
                $select_outlet = mysqli_query($conn, "SELECT * FROM `outlet`") or die('Query failed');
                  if (mysqli_num_rows($select_outlet) > 0) {
                    while ($row = mysqli_fetch_assoc($select_outlet)) {
                      $total_stock = $row['weight_2kg'] + $row['weight_5kg'] + $row['weight_12_5kg'] + $row['weight_37_5kg'];
                        ?>
                        <tr>
                            <td><b><?php echo $row['preferred_outlet']; ?></b></td>
                            <td><?php echo $row['contact_no']; ?></td>
                            <td><?php echo $row['request_date']; ?></td>
                            <td><?php echo $row['delivery_date'] ? $row['delivery_date'] : 'Not Allocated'; ?></td>
                            <td><?php echo $row['weight_2kg']?></td>
                            <td><?php echo $row['weight_5kg']?></td>
                            <td><?php echo $row['weight_12_5kg']?></td>
                            <td><?php echo $row['weight_37_5kg']?></td>
                            <td><?php echo $total_stock; ?></td>
                            <td>
                                <?php if ($row['status'] === 'Approve'): ?>
                            <button class="btn btn-success" disabled>Approved</button>
                                <?php elseif ($row['status'] === 'Deny'): ?>
                                <button class="btn btn-danger" disabled>Denied</button>
                                <?php else: ?>
                                    <a href="headoffice.php?edit=<?php echo $row['id']; ?>" class="btn btn-primary">Edit</a>
                                    <a href="headoffice.php?approve=<?php echo $row['id']; ?>" class="btn btn-primary">Approve</a>
                                    <a href="headoffice.php?deny=<?php echo $row['id']; ?>" class="btn btn-primary">Deny</a>
                                <?php endif; ?>
                            </td>

                            
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='6' class='empty'>No outlet data available</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Form -->
    <?php
    if (isset($_GET['edit'])) {
        $edit_id = $_GET['edit'];
        $edit_query = mysqli_query($conn, "SELECT * FROM `outlet` WHERE id = $edit_id");
        if (mysqli_num_rows($edit_query) > 0) {
            $fetch_edit = mysqli_fetch_assoc($edit_query);
            ?>
            <div class="edit-form-container">
                <div class="edit-form">
                    <form action="" method="post">
                        <div class="mb-3">
                            <label for="branch" class="form-label">Gas-By-Gas Branch</label>
                            <input type="text" class="form-control" id="branch" name="branch" value="<?php echo $fetch_edit['preferred_outlet']; ?>" disabled />
                        </div>
                        <div class="mb-3">
                            <label for="contact-no" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="contact-no" name="contactno" value="<?php echo $fetch_edit['contact_no']; ?>" disabled />
                        </div>
                        <div class="mb-3">
                            <label for="request_date" class="form-label">Request Date</label>
                            <input type="text" class="form-control" id="request_date" name="request_date" value="<?php echo $fetch_edit['request_date']; ?>" disabled />
                        </div>
                        <div class="mb-3">
                            <label for="delivery_date" class="form-label">Delivery Date</label>
                            <input type="date" class="form-control" id="delivery_date" name="delivery_date" value="<?php echo $fetch_edit['delivery_date']; ?>" required />
                        </div>
                        <input type="hidden" name="outlet_id" value="<?php echo $fetch_edit['id']; ?>" />
                        <button type="submit" class=" btn-primary close-edit">Allocate Delivery Date</button>
                        <button type="button" class="btn-primary option-btn" onclick="document.querySelector('.edit-form-container').style.display='none'">Cancel</button>
                    </form>
                </div>
            </div>
            <?php
        }
    }
    ?>

    <!-- Footer -->
    <?php include "includes/outletfooter.php"; ?>

</body>
</html>


