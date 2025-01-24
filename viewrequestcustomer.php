<?php
session_start();
require("includes/databaseconnect.php");

// Fetch customer details
$customer_query = mysqli_query($conn, "SELECT * FROM `customer` WHERE status != 'deny'") or die('Query failed: ' . mysqli_error($conn));
$customer = [];
if (mysqli_num_rows($customer_query) > 0) {
    while ($customer_row = mysqli_fetch_assoc($customer_query)) {
        $customer[] = $customer_row;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Updating the customer Status  of returned and picked
    if (isset($_POST['customer_id'])) {
        $id = $_POST['customer_id'];
        if (isset($_POST['return-date'])) {
            $returned = 1;
        } else {
            $returned = 0;
        }
        
        if (isset($_POST['pickup-date'])) {
            $picked = 1;
        } else {
            $picked = 0;
        }
        // Update the database
        $update_query = "UPDATE `customer` 
                         SET returned = '$returned', picked = '$picked' 
                         WHERE id = '$id'";
        $result = mysqli_query($conn, $update_query);

        if (!$result) {
            die('Query failed: ' . mysqli_error($conn));
        }
    }

        // Edit button and allocating the customer
        if (isset($_POST['close-edit'])) {
            $customer_id = $_POST['customer_id'];
            $customer_name = $_POST['update_name'];
            $customer_contactno = $_POST['update_contactno'];
            $customer_nic = $_POST['update_nic'];
            $customer_email = $_POST['update_email'];
            $customer_weight = $_POST['update_weight'];
            $customer_quantity = $_POST['update_quantity'];
            $approved_date = date("Y-m-d");
            $due_date = date("Y-m-d", strtotime("+2 weeks"));

        // Fetch current data to compare
    $fetch_current_query = "SELECT * FROM `customer` WHERE id = '$customer_id'";
    $fetch_current_result = mysqli_query($conn, $fetch_current_query);
    $current_data = mysqli_fetch_assoc($fetch_current_result);
    // Checking if any changes were made
    if (
        $customer_nic == $current_data['nic'] 
    ) {
        $_SESSION['message'] = [
            'type' => 'warning',
            'text' => 'No changes made. Failed to allocate new customer!',
        ];
        header("Location: viewrequestcustomer.php?");
        exit;
    }
        


    $update_query = "UPDATE `customer` SET 
    name = '$customer_name',
    contactno = '$customer_contactno',
    nic = '$customer_nic',
    email = '$customer_email',
    weight = '$customer_weight',
    quantity = '$customer_quantity',
    approved_date = '$approved_date',
    due_date = '$due_date',
    returned = '0'
    WHERE id = '$customer_id'";
    
    $update_result = mysqli_query($conn, $update_query);

    if ($update_result) {
        $_SESSION['message'] = [
        'type' => 'success',
        'text' => 'Reallocation of the customer is successful!',
        ];
    } else {
        $_SESSION['message'] = [
        'type' => 'danger',
        'text' => 'Failed to reallocate the customer!',
        ];
        }
header('Location: viewrequestcustomer.php');
 exit;
 }
}
// Fetch customer details
$select_customer = mysqli_query($conn, "SELECT * FROM `customer` WHERE status != 'deny'") or die('Query failed: ' . mysqli_error($conn));



mysqli_data_seek($select_customer, 0);

// Handle form submissions (update due_date logic if needed)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $customer_id = $_POST['customer_id'];
        $status = $_POST['status'];

        if ($status == 'Approve') {
            $approved_date = date("Y-m-d H:i:s");
            $due_date = date("Y-m-d H:i:s", strtotime("+2 weeks", strtotime($approved_date)));
            $update_query = "UPDATE `customer` SET `approved_date` = '$approved_date', `due_date` = '$due_date', `status` = '$status' WHERE id = $customer_id";
            mysqli_query($conn, $update_query) or die('Query failed: ' . mysqli_error($conn));
            $_SESSION['message'] = ['type' => 'success', 'text' => "Customer status updated to 'Approve'!"];
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
    <link rel="stylesheet" href="css/viewrequestcustomer.css">
    <title>View Customer Request</title>
</head>
<body>
    <!-- Navbar -->
    <?php include "includes/outletnavbar.php"; ?>

    <!-- Message Display -->
    <?php
    if (isset($_SESSION['message'])) {
        $messageType = $_SESSION['message']['type'];
        $messageText = $_SESSION['message']['text'];
        echo "
        <div class='alert alert-$messageType alert-dismissible fade show' role='alert'>
            <strong>Notice:</strong> $messageText
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
        unset($_SESSION['message']);
    }
    ?>

    <!-- Display Customer Table -->
    <div class="viewcustomer-table">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Contact No</th>
                    <th scope="col">Email</th>
                    <th scope="col">NIC</th>
                    <th scope="col">Weight</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Due Date</th>
                    <th scope="col">Returned</th>
                    <th scope="col">Picked up</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
    <?php
    $current_date = date("Y-m-d");

    if (mysqli_num_rows($select_customer) > 0) {
        while ($row = mysqli_fetch_assoc($select_customer)) {
            $due_date = isset($row['due_date']) ? date("Y-m-d", strtotime($row['due_date'])) : 'N/A';
            $due_message = ($current_date == $due_date) ? "<span class='text-danger'>Due date has arrived!</span>" : ''; // Show a message if the due date is today
            $edit_disabled = ($current_date == $due_date) ? '' : 'disabled'; // Disable the edit button if due date hasn't arrived
            ?>
            <tr>
                <td><?php echo ($row['name']); ?></td>
                <td><?php echo ($row['contactno']); ?></td>
                <td><?php echo ($row['email']); ?></td>
                <td><?php echo ($row['nic']); ?></td>
                <td><?php echo ($row['weight']); ?></td>
                <td><?php echo ($row['quantity']); ?></td>
                <td>
                    <?php echo ($due_date); ?>
                    <?php echo $due_message; ?>
                </td>
                <form action="" method="post">
                    <input type="hidden" name="customer_id" value="<?php echo $row['id']; ?>">
                    <td>
                        <input type="checkbox" name="return-date" value="1" <?php echo ($row['returned'] == 1) ? 'checked' : ''; ?> onchange="this.form.submit();">
                    </td>
                    <td>
                        <input type="checkbox" name="pickup-date" value="1" <?php echo ($row['picked'] == 1) ? 'checked' : ''; ?> onchange="this.form.submit();">
                    </td>
                </form>
                <td>
                    <!-- Add the 'disabled' attribute based on the due date -->
                    <a href="viewrequestcustomer.php?edit=<?php echo $row['id']; ?>" class="btn btn-primary <?php echo $edit_disabled; ?>" <?php echo $edit_disabled; ?>>Edit</a>
                </td>
            </tr>
            <?php
        }
    } else {
        echo "<tr><td colspan='10' class='text-center'>No customer orders found.</td></tr>";
    }
    ?>
</tbody>


        </table>
    </div>
    
    <!-- Edit Form -->
     <?php
    if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_query = mysqli_query($conn, "SELECT * FROM `customer` WHERE id = '$edit_id'") or die('Query failed: ' . mysqli_error($conn));

    if (mysqli_num_rows($edit_query) > 0) {
        $fetch_edit = mysqli_fetch_assoc($edit_query);

        // Taking the current value of the returned checkbox
        $returnedChecked = ($fetch_edit['returned'] == 1) ? 'checked' : '';
        ?>
        <div class="outlet-form edit-form-container">
            <form action="" method="post" class="edit-form">
                <div class="mb-3">
                    <input type="hidden" name="customer_id" value="<?php echo $fetch_edit['id']; ?>">

                    <div class="mb-3">
                        <label for="update_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="update_name" name="update_name" value="<?php echo $fetch_edit['name']; ?>" required />
                    </div>

                    <div class="mb-3">
                        <label for="update_contactno" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="update_contactno" name="update_contactno" value="<?php echo $fetch_edit['contactno']; ?>" maxlength="10" pattern="^\d{10}$" title="Contact number must be exactly 10 digits" required />
                    </div>

                    <div class="mb-3">
                        <label for="update_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="update_email" name="update_email" value="<?php echo $fetch_edit['email']; ?>" required />
                    </div>

                    <div class="mb-3">
                        <label for="update_nic" class="form-label">NIC</label>
                        <input type="text" class="form-control" id="update_nic" name="update_nic" value="<?php echo $fetch_edit['nic']; ?>" maxlength="12" pattern="^\d{12}$" title="NIC must be exactly 12 digits" required />
                    </div>

                    <div class="mb-3">
                        <label for="update_weight" class="form-label">Weight</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="update_weight" 
                            value="<?php echo ($fetch_edit['weight']); ?>" 
                            disabled>
                        <input 
                            type="hidden" 
                            name="update_weight" 
                            value="<?php echo ($fetch_edit['weight']); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="update_quantity" class="form-label">Quantity</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="update_quantity" 
                            value="<?php echo ($fetch_edit['quantity']); ?>" 
                            disabled>
                        <input 
                            type="hidden" 
                            name="update_quantity" 
                            value="<?php echo ($fetch_edit['quantity']); ?>">
                    </div>

                    <div style="display:none;">
                        <input type="checkbox" name="return-date" value="1" <?php echo $returnedChecked; ?> />
                    </div>

                    <button type="submit" name="close-edit" class="btn btn-primary">Update</button>
                    <button type="button" onclick="document.querySelector('.edit-form-container').style.display='none'" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
        <?php
    } else {
        echo "<div class='alert alert-danger'>No customer found for editing!</div>";
    }
}
?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cancelButton = document.getElementById('close-edit');
    if (cancelButton) {
        cancelButton.addEventListener('click', function () {
            document.querySelector('.edit-form-container').style.display = 'none';
        });
    }
});
</script>


    <!-- Footer -->
    <?php include "includes/outletfooter.php"; ?>
</body>
</html>
