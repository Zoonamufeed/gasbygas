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
    function sendEmail($toEmail, $subject, $messageBody) 
    {
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
            $mail->addAddress($toEmail);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $messageBody;

            // Send the email
            $mail->send();
            return true; 
        } catch (Exception $e) {
            return false; 
        }
    }

    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
        $email = mysqli_real_escape_string($conn, $_POST['email']);

        
        $query = "SELECT * FROM admin WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            // Generate a 4-digit OTP
            $otp = random_int(1000, 9999);

          
            $_SESSION['otp'] = $otp;
            $_SESSION['otp_email'] = $email;

            // Send the OTP email
            $subject = "Your OTP for Password Reset";
            $messageBody = "<p>Hi,</p>
                            <p>Your OTP for resetting your password is: <strong>$otp</strong></p>
                            <p>This OTP is valid for 10 minutes.</p>
                            <p>Thanks,<br>GasByGas Team</p>";

            if (sendEmail($email, $subject, $messageBody)) 
            {
                $_SESSION['message'] = [
                    'text' => "An OTP has been sent to your email. Please check your inbox.",
                    'type' => "success"
                ];
                header("Location:ForgotPassword.php");
                exit;
            } 
            else 
            {
                $_SESSION['message'] = 
                [
                    'text' => "Failed to send the OTP. Please try again.",
                    'type' => "danger"
                ]; 
                header("Location: otprequest.php");
                exit;
            }
        } 
        else 
        {
            $_SESSION['message'] = 
            [
                'text' => "The email address you entered is not registered.",
                'type' => "danger"
            ];
            header("Location: SignUp.php");
                exit;
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

    <!-- Links -->
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
            <div class='alert alert-$messageType alert-dismissible fade show' role='alert' style='margin-top:0; margin-bottom:100px;'>
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
                            <form action="" method="POST" class="FrgtPassword-form">
                                <h3 style="text-align: center;">
                                    Request OTP
                                </h3>
                                <div class="form-group mb-3">
                                    <label class="label" for="name">Email</label>
                                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                                </div>
                                
                                <div class="form-group">
                                    <button class="form-control btn btn-primary submit px-3 w-50" style="margin-left:25%;"
                                    name="sendotp">Send OTP</button>
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
        margin-top:10%;
    }

    .input-group {
        position: relative;
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
</style>

