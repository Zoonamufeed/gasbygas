<?php
session_start();
require("includes/databaseconnect.php");
// checks if the user_id is set or not null.
if (!isset($_SESSION['user_id'])) {
    echo "Error: User ID is not set in session.";
    exit;
}
//checking if the user_id is stored in the session or not
$userId = $_SESSION['user_id']; 

// Retriving the user_ID from session Fetching data from admin table
$query = "SELECT preferred_outlet, contact_no FROM admin WHERE id = '$userId'";
$result = mysqli_query($conn, $query);

// Must initialize the values
$preferredOutlet = '';
$contactNo = '';

// checking if result query sends any results if not error message
if ($result && mysqli_num_rows($result) > 0) {
    //extract the row by row , to store values
    $row = mysqli_fetch_assoc($result);
    $preferredOutlet = $row['preferred_outlet'];
    $contactNo = $row['contact_no'];
} else {
    echo "No data found for user_id: $userId"; 
}
//checks if the outlet_Form is submitted then it retives these (weight)values.
if (isset($_POST['outlet_form'])) {
    $requestDate = $_POST['requested_date'];
    $weight1 = $_POST['weight1'];
    $weight2 = $_POST['weight2']; 
    $weight3 = $_POST['weight3']; 
    $weight4 = $_POST['weight4']; 

    // extracting the current month and Checking if there is request made using the same month
    $month = date('Y-m'); 
    $checkRequestQuery = "SELECT * FROM outlet WHERE user_id = '$userId' AND DATE_FORMAT(request_date, '%Y-%m') = '$month'";// four digit for year and two digits for month.
    $checkRequestResult = mysqli_query($conn, $checkRequestQuery);

//if the user have made request on the month the error message is sent
    if (mysqli_num_rows($checkRequestResult) > 0) {
        $_SESSION['message'] = [
            'type' => 'danger', 
            'text' => 'You have already submitted a request this month.'
        ];
    } else {
//if user hasn't made a request it insert the data
        $insertQuery = "INSERT INTO outlet (`user_id`, `preferred_outlet`, `contact_no`, `request_date`, `weight_2kg`, `weight_5kg`, `weight_12_5kg`, `weight_37_5kg`)
                        VALUES ('$userId', '$preferredOutlet', '$contactNo', '$requestDate', '$weight1', '$weight2', '$weight3', '$weight4')";
// insert into sucessful message
        if (mysqli_query($conn, $insertQuery)) {
            $_SESSION['message'] = [
                'type' => 'success', 
                'text' => 'Request submitted successfully.'
            ];
//updates the outlet stock table availabe stock for respective preferred outlte.
            $updateStockQuery = "UPDATE outlet SET 
            available_weight_2kg = '$weight1',
            available_weight_5kg = '$weight2',
            available_weight_12_5kg = '$weight3',
            available_weight_37_5kg = '$weight4'
            WHERE preferred_outlet = '$preferredOutlet'";


            mysqli_query($conn, $updateStockQuery);

            header('Location: outlet.php');
            exit;
        } else {
            $_SESSION['message'] = [
                'type' => 'danger', 
                'text' => 'Failed to submit the request. Please try again.'
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php include "includes/header_links.php"; ?>
    <link rel="stylesheet" href="css/outlet.css"/>
    <title>Outlet Homepage</title>
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

    <!-- Outlet Request Form -->
    <div class="outlet-form">
        <form action="" method="post">
            <div class="mb-3">
                <label for="branch" class="form-label">Gas-By-Gas Branch</label>
                <input type="text" class="form-control" id="branch" name="branch" value="<?php echo htmlspecialchars($preferredOutlet); ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="contact-no" class="form-label">Contact Number</label>
                <input type="text" class="form-control" id="contact-no" name="contactno" value="<?php echo htmlspecialchars($contactNo); ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="request_date" class="form-label">Request Date</label>
                <input type="date" class="form-control" id="request_date" name="requested_date" aria-label="default input example" required>
            </div>
            <div class="mb-3">
                <fieldset class="border p-3 rounded">
                    <legend class="float-none w-auto px-3 fs-5">Quantity</legend>
                    <label for="2kgqty" class="form-label">2kg</label>
                    <input type="text" class="form-control" id="2kgqty" name="weight1" aria-label="default input example">
                    <label for="5kgqty" class="form-label">5kg</label>
                    <input type="text" class="form-control" id="5kgqty" name="weight2" aria-label="default input example">
                    <label for="12_5kgqty" class="form-label">12_5kg</label>
                    <input type="text" class="form-control" id="12.5kgqty" name="weight3" aria-label="default input example">
                    <label for="37_5kgqty" class="form-label">37.5kg</label>
                    <input type="text" class="form-control" id="37_5kgqty" name="weight4" aria-label="default input example">
                </fieldset>
            </div>
            <button type="submit" class="btn btn-primary" name="outlet_form">Request</button>
        </form>
    </div>

    <!-- Footer -->
    <?php include "includes/outletfooter.php"; ?>
</body>
</html>
