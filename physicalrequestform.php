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

//  initializing 
$branch = '';

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $branch = $row['preferred_outlet'];
} else {
    echo "No data found for user_id: $userId"; 
}
// place customer order
if (isset($_POST['add_customer'])) {
    $name = $_POST['name'];
    $contactno = $_POST['contactno'];
    $nic= $_POST['nic'];
    $weight = $_POST['weight'];
    $quantity = $_POST['quantity'];
    $email = $_POST['email'];
    $branch =$branch;
    
    $check_nic = mysqli_query($conn, "SELECT * FROM `customer` WHERE nic = '$nic'");
    // display message
    if (mysqli_num_rows($check_nic) > 0) {
        $_SESSION['message'] = [
            'type' => 'warning',
            'text' => 'Order has already been placed using this NIC.',
        ];
        header("Location: physicalrequestform.php");
        exit;
    }
    $insert_customer = mysqli_query($conn, 
        "INSERT INTO `customer` (name, contactno, nic, weight, quantity, branch,email) 
        VALUES ('$name', '$contactno', '$nic', '$weight', '$quantity','$branch','$email')") or die('Query failed');
//display message
if ($insert_customer) {
    $_SESSION['message'] = [
        'type' => 'primary',
        'text' => 'Successfully placed order.',
    ];
} else {
    $_SESSION['message'] = [
        'type' => 'danger',
        'text' => 'Failed to place order.',
    ];
}

header("Location: physicalrequestform.php");
exit;






}
?>
<!Doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <?php include "includes/header_links.php"; ?>
        <link rel="stylesheet" href="css/physicalrequestform.css"/>
        <title> Onsite Customer Request Form</title>
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


          <!-- Onsite Request Form -->
        <div class="outlet-form">
          <form action="" method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" aria-label="default input example" required />
              </div>
              <div class="mb-3">
                <label for="contactno" class="form-label">Contact Number</label>
                <input type="text" class="form-control" id="contactno" name="contactno" maxlength="10" pattern="^\d{10}$"  title="Contact number must be exactly 10 digits" onkeypress="return /[0-9]/i.test(event.key)" aria-label="default input example"  required>
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
              </div>
              <div class="mb-3">
                <label for="nic" class="form-label">NIC</label>
                <input type="text" class="form-control" id="nic" name="nic" maxlength="12" pattern="^\d{12}$"  aria-describedby="nicdescription"  onkeypress="return /[0-9]/i.test(event.key)"  title="NIC must be exactly 12 digits"   required>
                <div id="nicdescription" class="form-text">
                    Your National Identity Card must be 12 characters long.</div>
                </div>
                <div class="mb-3">   
                <label for="weight" class="form-label">Weight</label>
                <select class="form-select" aria-label="Default select example" name="weight" id="weight">
                    <option selected value="2KG">2KG</option>
                    <option value="5KG">5KG</option>
                    <option value="12.5KG">12.5KG</option>
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
            <button type="submit" class="btn btn-primary" name="add_customer">Request</button>
          </form>
          </div>
          


<!-- Footer -->
 <?php
 include "includes/outletfooter.php";
 ?>
    </body>
</html>