<?php
    include 'includes/databaseconnect.php';
    session_start();
    
    // redirect to otp page if the sessions are not available
    if (!isset($_SESSION['otp_email']) || !isset($_SESSION['otp'])) {
        header("Location: otprequest.php"); 
        exit;
    }

    //update the password
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
        $otpEntered = $_POST['otp']; 
        $newPassword = $_POST['newpassword']; 
        $confirmPassword = $_POST['confirm_password']; 
        $email = $_SESSION['otp_email']; 

       
        if ($_SESSION['otp'] == $otpEntered) {
            
            if ($newPassword === $confirmPassword) {
                
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                
                $query = "UPDATE customersignup SET password = '$hashedPassword' WHERE email = '$email'";
                $result = mysqli_query($conn, $query);

                if ($result) {
                    $_SESSION['message'] = [
                        'text' => "Password updated successfully!",
                        'type' => "success"
                    ];
                    
                    header("Location: SignIn.php");
                    exit;
                } else {
                    $_SESSION['message'] = [
                        'text' =>  "Failed to update password. Error: $error",
                        'type' => "danger"
                    ];
                }
            } else {
                $_SESSION['message'] = [
                    'text' => "Password and confirm password do not match.",
                    'type' => "danger"
                ];
            }
        } else {
            $_SESSION['message'] = [
                'text' => "Invalid OTP. Please try again.",
                'type' => "danger"
            ];
        }

        $query = "SELECT password FROM customersignup WHERE email = '$email'";
$result = mysqli_query($conn, $query);
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $hashedPassword = $row['password'];
} else {
    die("User not found");
}
    }

    
    

?>


<!doctype html>
<html lang="en">

<head>
    <title>Forgot Password</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<?php
    include 'includes/header_links.php';
 ?>
    
    <link rel="stylesheet" href="css/FrgtPaswrd.css ">
</head>

<body>
    <!-- Display Alert -->
    <div class="w-100" style="height:100vh">
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
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12 col-lg-10">
                    <div class="wrap d-md-flex">
                        <div class="login-wrap p-4 p-lg-5">
                            <form action="#" method="POST" class="FrgtPassword-form">
                                <h3 style="text-align: center;">
                                    Change Password
                                </h3>
                                <div class="form-group mb-3">
                                    <label class="label" for="name">Enter OTP</label>
                                    <input type="text" class="form-control" name="otp" placeholder="OTP" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="label" for="password">New Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control password" style="border-radius:2rem;"  name="newpassword" placeholder="Password"
                                            required>
                                        <i class="bi bi-eye-slash toggle-password" onclick="togglePassword(this)"></i>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="label" for="password">Confirm Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control password" style="border-radius:2rem;" name="confirm_password" placeholder="Confirm Password"
                                            required>
                                        <i class="bi bi-eye-slash toggle-password" onclick="togglePassword(this)"></i>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="form-control btn btn-primary submit px-3" name="change_password">Change
                                        Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/frgtPasrd.js"></script>
</body>

</html>

<style>
    body {
        margin: 0;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #f8f9fa;
    }

    .container {
        width: 100%;
        max-width: 600px;
        border-radius: 8px;
        padding: 20px;
        margin-top:2%;
    }

    .input-group {
        position: relative;
        border-radius:2rem;
    }

    .toggle-password {
        padding: 3px;
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        font-size: 20px;
        color: #6c757d;
    }

    .otp{
        
    }
</style>

