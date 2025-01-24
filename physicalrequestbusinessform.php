<?php
include 'includes/databaseconnect.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "No user Id available.";
    exit;
}

$userId = $_SESSION['user_id']; 

// Fetching data from admin table
$query = "SELECT preferred_outlet FROM `admin` WHERE id = '$userId'";
$result = mysqli_query($conn, $query);

// must initialize the values
$branch = '';

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $branch = $row['preferred_outlet'];
} else {
    echo "No data found for user_id: $userId"; 
}
// place Business order
if (isset($_POST['add_business'])) {
    $regno = $_POST['regno'];
    $businessname= $_POST['businessname'];
    $contactno = $_POST['contactno'];
    $weight = $_POST['weight'];
    $quantity = $_POST['quantity'];
    $email = $_POST['email'];
    $branch=$branch;
    
    $check_regno = mysqli_query($conn, "SELECT * FROM `business` WHERE regno = '$regno'");
// display message
    if (mysqli_num_rows($check_regno) > 0) {
        $_SESSION['message'] = [
            'type' => 'warning',
            'text' => 'Order has already been placed using this Registration Number.',
        ];
        header("Location: physicalrequestbusinessform.php");
        exit;
    }


    $insert_business = mysqli_query($conn, 
        "INSERT INTO `business` (regno, businessname, contactno, weight, quantity, branch, email) 
        VALUES ('$regno','$businessname', '$contactno', '$weight', '$quantity','$branch','$email')") or die('Query failed');

if ($insert_business) {
    $_SESSION['message'] = [
        'type' => 'primary',
        'text' => 'Successfully order placed.',
    ];
} else {
    $_SESSION['message'] = [
        'type' => 'danger',
        'text' => 'Failed to place order.',
    ];
}

header("Location: physicalrequestbusinessform.php");
exit;


}
?>
<!Doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php include "includes/header_links.php"; ?>
    <link rel="stylesheet" href="css/physicalrequestbusinessform.css">
    <title>Onsite Business Request Form</title>

</head>

<body>
    <!-- navbar -->
    <?php
         include "includes/outletnavbar.php";
         ?>
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
                <label for="Business-regNo" class="form-label">Business Reg No</label>
                <input type="text" class="form-control" id="Business-regNo" name="regno" aria-label="default input example" required>
            </div>
            <div class="mb-3">
                <label for="BName" class="form-label">Business Name</label>
                <input type="text" class="form-control" id="BName" name="businessname" aria-label="default input example" required>
            </div>
            <div class="mb-3">
                <label for="phone-no" class="form-label">Contact Number</label>
                <input type="text" class="form-control" id="phone-no" name="contactno" maxlength="10" aria-label="default input example" pattern="^\d{10}$"  title="Contact number must be exactly 10 digits" onkeypress="return /[0-9]/i.test(event.key)"  required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" aria-label="default input example"  required>
              </div>
                <div class="mb-3">   
                <label for="weight" class="form-label">Weight</label>
                <select class="form-select" aria-label="Default select example" name="weight" id="weight">
                    <option selected value="5KG">5KG</option>
                    <option value="12.5KG">12.5KG</option>
                    <option value="37.5KG">37.5KG</option>
                </select>
                </div>
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity</label>
                <select class="form-select" aria-label="Default select example" name="quantity" id="quantity">
                    <option selected value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
                </div>
                <div class="mb-3">
                <label for="branch" class="form-label">Branch</label>
                <input type="text" class="form-control" id="branch" name="branch" value="<?php echo htmlspecialchars($branch); ?>" aria-label="default input example" disabled />
              </div>
                <button type="submit" class="btn-request" name="add_business">Request</button>
            </div>
    </form>
    </div>
    

<!-- Footer -->
 <?php
 include "includes/outletfooter.php";
 ?>
    </body>
</html>