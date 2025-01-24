<?php
session_start();
require("includes/databaseconnect.php");

// Fetch business details
$business_query = mysqli_query($conn, "SELECT * FROM `business` WHERE status != 'deny'") or die('Query failed: ' . mysqli_error($conn));

$business_data = [];
if (mysqli_num_rows($business_query) > 0) {
    while ($business_row = mysqli_fetch_assoc($business_query)) {
        $business_data[] = $business_row;
    }
} else {
    
    $_SESSION['message'] = [
        'type' => 'warning',
        'text' => 'No business requests found.',
    ];
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['business_id'])) {
        $id = $_POST['business_id'];
        
        
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

        
        $update_query = "UPDATE `business` 
                         SET returned = '$returned', picked = '$picked' 
                         WHERE id = '$id'";
                         
        $result = mysqli_query($conn, $update_query);

       
        if (!$result) {
            die('Query failed: ' . mysqli_error($conn));
        }
    }
}


        // Edit button and allocating the business
        if (isset($_POST['close-edit'])) {
            $business_id = $_POST['business_id'];
            $business_name = $_POST['update_name'];
            $business_contactno = $_POST['update_contactno'];
            $business_regno = $_POST['update_regno'];
            $business_email = $_POST['update_email'];
            $business_weight = $_POST['update_weight'];
            $business_quantity = $_POST['update_quantity'];
            $approved_date = date("Y-m-d");
            $due_date = date("Y-m-d", strtotime("+2 weeks"));

        // Fetch current data to compare
    $fetch_current_query = "SELECT * FROM `business` WHERE id = '$business_id'";
    $fetch_current_result = mysqli_query($conn, $fetch_current_query);
    $current_data = mysqli_fetch_assoc($fetch_current_result);
    if (
        $business_regno == $current_data['regno'] &&
        $business_email == $current_data['email'] 
    ) {
        $_SESSION['message'] = [
            'type' => 'warning',
            'text' => 'No changes made. Failed to allocate new business!',
        ];
        header("Location: viewrequestbusiness.php");
        exit;
    }
    


    $update_query = "UPDATE `business` SET 
    businessname = '$business_name',
    contactno = '$business_contactno',
    regno = '$business_regno',
    email = '$business_email',
    weight = '$business_weight',
    quantity = '$business_quantity',
    approved_date = '$approved_date',
    due_date = '$due_date',
    returned = '0'
    WHERE id = '$business_id'";
    
    $update_result = mysqli_query($conn, $update_query);

    if ($update_result) {
        $_SESSION['message'] = [
        'type' => 'success',
        'text' => 'Reallocation of the business is successful!',
        ];
    } else {
        $_SESSION['message'] = [
        'type' => 'danger',
        'text' => 'Failed to reallocate the business!',
        ];
        }
header('Location: viewrequestbusiness.php');
 exit;
 }



// Fetch business details
$select_business = mysqli_query($conn, "SELECT * FROM `business` WHERE status != 'deny'") or die('Query failed: ' . mysqli_error($conn));

mysqli_data_seek($select_business, 0);

// Handle form submissions 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $business_id = $_POST['business_id'];
        $status = $_POST['status'];

        if ($status == 'Approve') {
            $approved_date = date("Y-m-d H:i:s");
            $due_date = date("Y-m-d H:i:s", strtotime("+2 weeks", strtotime($approved_date)));
            $update_query = "UPDATE `business` SET `approved_date` = '$approved_date', `due_date` = '$due_date', `status` = '$status' WHERE id = $business_id";
            mysqli_query($conn, $update_query) or die('Query failed: ' . mysqli_error($conn));
            $_SESSION['message'] = ['type' => 'success', 'text' => "Business status updated to 'Approve'!"];
        }
    }
}

 ?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include "includes/header_links.php"; ?>
    <link rel="stylesheet" href="css/viewrequestbusiness.css">
    <title>View Business Requests</title>
</head>
<body>
    <?php include "includes/outletnavbar.php"; ?>

    <!-- Display Messages -->
    <?php
    if (isset($_SESSION['message'])) {
        echo "
        <div class='alert alert-{$_SESSION['message']['type']} alert-dismissible fade show' role='alert'>
            {$_SESSION['message']['text']}
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
        unset($_SESSION['message']);
    }
    ?>

    <!-- Business Table -->
    <div class="viewbusiness-table table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Registration No</th>
                    <th>Business Name</th>
                    <th>Contact No</th>
                    <th>Email</th>
                    <th>Weight</th>
                    <th>Quantity</th>
                    <th>Due Date</th>
                    <th>Returned</th>
                    <th>Picked</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php


$current_date = date("Y-m-d");

if (mysqli_num_rows($select_business) > 0) {
    while ($row = mysqli_fetch_assoc($select_business)) {
        $due_date = isset($row['due_date']) ? date("Y-m-d", strtotime($row['due_date'])) : 'N/A';
        $due_message = ($current_date == $due_date) ? "<span class='text-danger'>Due date has arrived!</span>" : ''; 
        $edit_disabled = ($current_date == $due_date) ? '' : 'disabled'; 
        ?>
        <tr>
            <td><?php echo htmlspecialchars($row['regno']); ?></td>
            <td><?php echo htmlspecialchars($row['businessname']); ?></td>
            <td><?php echo htmlspecialchars($row['contactno']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['weight']); ?></td>
            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
            <td>
                <?php echo htmlspecialchars($due_date); ?>
                <?php echo $due_message; ?>
            </td>
            <form action="" method="post">
                <input type="hidden" name="business_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                <td>
                    <input type="checkbox" name="return-date" value="1" <?php echo ($row['returned'] == 1) ? 'checked' : ''; ?> onchange="this.form.submit();">
                </td>
                <td>
                    <input type="checkbox" name="pickup-date" value="1" <?php echo ($row['picked'] == 1) ? 'checked' : ''; ?> onchange="this.form.submit();">
                </td>
            </form>
            <td>
                <a href="viewrequestbusiness.php?edit=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-primary <?php echo $edit_disabled; ?>" <?php echo $edit_disabled; ?>>Edit</a>
            </td>
        </tr>
        <?php
    }
} else {
    echo "<tr><td colspan='10' class='text-center'>No business requests found.</td></tr>";
}
?>

</tbody>
        </table>
    </div>


 <!-- Edit Form -->
<?php
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_query = mysqli_query($conn, "SELECT * FROM `business` WHERE id = $edit_id") or die('Query failed: ' . mysqli_error($conn));
    if (mysqli_num_rows($edit_query) > 0) {
        $edit_data = mysqli_fetch_assoc($edit_query);
        ?>
        <div class="edit-form-container">
            <form action="" method="post" class="edit-form">
                <input type="hidden" name="business_id" value="<?php echo $edit_data['id']; ?>">

                <div class="mb-3">
                    <label for="update_regno" class="form-label">Registration No</label>
                    <input type="text" class="form-control" id="update_regno" name="update_regno" value="<?php echo $edit_data['regno']; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="update_name" class="form-label">Business Name</label>
                    <input type="text" class="form-control" id="update_name" name="update_name" value="<?php echo $edit_data['businessname']; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="update_contactno" class="form-label">Contact Number</label>
                    <input type="text" class="form-control" id="update_contactno" name="update_contactno" value="<?php echo $edit_data['contactno']; ?>" maxlength="10" required>
                </div>

                <div class="mb-3">
                    <label for="update_email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="update_email" name="update_email" value="<?php echo $edit_data['email']; ?>" required>
                </div>

                <div class="mb-3">
                    <label for="update_weight" class="form-label">Weight</label>
                    <input type="text" class="form-control" id="update_weight" value="<?php echo $edit_data['weight']; ?>" disabled>
                    <input type="hidden" name="update_weight" value="<?php echo $edit_data['weight']; ?>">
                </div>

                <div class="mb-3">
                    <label for="update_quantity" class="form-label">Quantity</label>
                    <input type="text" class="form-control" id="update_quantity" value="<?php echo $edit_data['quantity']; ?>" disabled>
                    <input type="hidden" name="update_quantity" value="<?php echo $edit_data['quantity']; ?>">
                </div>

                <div class="mb-3" style="display:none;>
                    <label for="return-date" class="form-label">Returned</label>
                    <input type="checkbox" name="returned" value="1" <?php echo $edit_data['returned'] == 1 ? 'checked' : ''; ?>>
                </div>

                <button type="submit" name="close-edit" class="btn btn-primary">Update</button>
                <button type="button" onclick="document.querySelector('.edit-form-container').style.display='none'" class="btn btn-secondary">Cancel</button>
            </form>
        </div>
        <?php
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

    <?php include "includes/outletfooter.php"; ?>
</body>
</html>