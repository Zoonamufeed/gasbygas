<?php
session_start(); 

// Display session message if it exists
if (isset($_SESSION['message'])) {
    $messageType = $_SESSION['message']['type'];
    $messageText = $_SESSION['message']['text'];

    echo "
    <div class='alert alert-$messageType alert-dismissible fade show' role='alert'>
        <strong>Notice!</strong> $messageText
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>";

    unset($_SESSION['message']); // Clear the message after displaying it
}
?>
<!doctype html>
<html lang="en">

<head>
    <title>SignIn</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="css/signin.css">

</head>

<body>
    
<section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12 col-lg-10">
                    <div class="wrap d-md-flex">
                        <div class="text-wrap p-4 p-lg-5 text-center d-flex align-items-center order-md-last">
                            <div class="text w-100">
                                <h2>Welcome Back</h2>
                                <p>Don't have an account?</p>
                                <a href="SignUp.html" class="btn btn-white btn-outline-white">Sign Up</a>
                            </div>
                        </div>
                        <div class="login-wrap p-4 p-lg-5">
                            <div class="d-flex">
                                <div class="w-100">
                                    <h3 class="mb-4">Sign In</h3>
                                </div>
                                <div class="w-100">
                                    <p class="social-media d-flex justify-content-end">
                                        <a href="#" class="social-icon d-flex align-items-center justify-content-center">
                                            <span class="fa fa-facebook"></span>
                                        </a>
                                        <a href="#" class="social-icon d-flex align-items-center justify-content-center">
                                            <span class="fa fa-twitter"></span>
                                        </a>
                                    </p>
                                </div>
                            </div>

                            <form action="includes/login_sub.php" method="POST" class="signin-form">
                                <div class="form-group mb-3">
                                    <label class="label" for="Email">Email</label>
                                    <input type="Email" class="form-control" placeholder="Email" name="email" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="label" for="password">Password</label>
                                    <div class="input-group">
                                        <input type="password" name="password" class="form-control password" placeholder="Password" required>
                                        <i class="bi bi-eye-slash toggle-password" onclick="togglePassword(this)"></i>
                                    </div>
                                </div>
                                <div class="form-group d-flex justify-content-center">
                                    <button type="submit" class="form-control btn btn-primary px-3" name="sign-in">Sign In</button>
                                </div>
                                <div class="form-group d-md-flex">
                                    <div class="w-50 text-left">
                                        <label class="checkbox-wrap checkbox-primary mb-0">Remember Me
                                            <input type="checkbox" checked>
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                    <div class="w-50 text-md-right">
                                        <a href="ForgotPassword.html">Forgot Password</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    

    <script src="js/siginadmin.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>