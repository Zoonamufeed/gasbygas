<?php
session_start(); 

// Display message 
if (isset($_SESSION['message'])) {
    $messageType = $_SESSION['message']['type'];
    $messageText = $_SESSION['message']['text'];

    echo "
    <div class='alert alert-$messageType alert-dismissible fade show' role='alert'>
        <strong>Notice!</strong> $messageText
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>";

    unset($_SESSION['message']); 
}
?>
<!doctype html>
<html lang="en">

<head>
    <title>SignUp</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="css/signup.css">

</head>
    <body>
        <section class="ftco-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-12 col-lg-10">
                        <div class="login-wrap">
                            <div class="d-flex">
                                <div class="w-100">
                                    <img src="img/logo.jpg" alt="Logo" style="width: 100px; height: 100px;">
                                </div>
                                <div class="w-100">
                                    <h3 class="mb-4 Sign-up-h">Sign Up</h3>
                                </div>
                            </div>
                            <!-- form -->
                            <form action="includes/signup_sub.php" method="POST" class="signin-form">
                                <div class="form-group row mb-3">
                                    <div class="col-md-6">
                                        <label class="label" for="name">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="label" for="phone-no">Contact Number</label>
                                        <input type="text" class="form-control" id="phone-no" name="contact_no" placeholder="Phone Number" maxlength="10" aria-label="default input example" pattern="^\d{10}$"  title="Contact number must be exactly 10 digits" onkeypress="return /[0-9]/i.test(event.key)"   required>
                                    </div>
                                </div>
    
                                
                                <div class="form-group row mb-3">
                                    <div class="col-md-6">
                                        <label class="label" for="nic">NIC</label>
                                        <input type="text" class="form-control" id="nic" name="nic" placeholder="NIC" maxlength="12" pattern="^\d{12}$"  aria-describedby="nicdescription"  onkeypress="return /[0-9]/i.test(event.key)"  title="NIC must be exactly 12 digits"   required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="label" for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                    </div>
                                </div>
    
                                
                                <div class="form-group row mb-3">
                                    <div class="col-md-6">
                                        <label class="label" for="password">Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password" style="border-radius:2rem;"  name="password" placeholder="Password" required>
                                            <i class="bi bi-eye-slash toggle-password" data-target="password" onclick="togglePassword(this)"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="label" for="confirm-password">Confirm Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" style="border-radius:2rem;"  id="confirm-password" name="confirm_password" placeholder="Confirm Password" required>
                                            <i class="bi bi-eye-slash toggle-password" data-target="confirm-password" onclick="togglePassword(this)"></i>
                                        </div>
                                    </div>
                                </div>
    
                               
                                <div class="col-md-6">
                                    <label class="label">Select your admin type</label><br>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" id="headoffice-admin" name="admin_type" value="Headoffice" class="form-check-input" required>
                                        <label class="form-check-label" for="headoffice-admin">Head Office Staff</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" id="outlet-admin" name="admin_type" value="Outlet" class="form-check-input">
                                        <label class="form-check-label" for="outlet-admin">Outlet Manager</label>
                                    </div>
                                </div>
    
                               
                                <div id="outlet-location" style="display: none;">
                                    <div class="col-md-6">
                                        <label class="label" for="preferred-outlet">Preferred Outlet</label>
                                        <select id="preferred-outlet" name="preferred_outlet" class="form-control">
                                            <option value="" hidden>Select the nearest outlet</option>
                                            <option value="" hidden>Select the nearest outlet</option>
                                            <option value="Ampara">Ampara</option>
                                            <option value="Anuradhapura">Anuradhapura</option>
                                            <option value="Badulla">Badulla</option>
                                            <option value="Batticaloa">Batticaloa</option>
                                            <option value="Colombo">Colombo</option>
                                            <option value="Galle">Galle</option>
                                            <option value="Gampaha">Gampaha</option>
                                            <option value="Hambantota">Hambantota</option>
                                            <option value="Jaffna">Jaffna</option>
                                            <option value="Kalutara">Kalutara</option>
                                            <option value="Kandy">Kandy</option>
                                            <option value="Kegalle">Kegalle</option>
                                            <option value="Kilinochchi">Kilinochchi</option>
                                            <option value="Kurunegala">Kurunegala</option>
                                            <option value="Mannar">Mannar</option>
                                            <option value="Matale">Matale</option>
                                            <option value="Matara">Matara</option>
                                            <option value="Monaragala">Monaragala</option>
                                            <option value="Mullaitivu">Mullaitivu</option>
                                            <option value="Nuwara Eliya">Nuwara Eliya</option>
                                            <option value="Polonnaruwa">Polonnaruwa</option>
                                            <option value="Puttalam">Puttalam</option>
                                            <option value="Anuradhapura">Anuradhapura</option>
                                            <option value="Ratnapura">Ratnapura</option>
                                            <option value="Trincomalee">Trincomalee</option>
                                            <option value="Vavuniya">Vavuniya</option>
                                            
                                        </select>
                                    </div>
                                </div>
    
                                <div class="form-group d-flex justify-content-center">
                                    <input type="checkbox" id="terms" name="terms" required>
                                    <span><p style="margin-left: 10px;">I agree to the Terms & Conditions</p></span>
                                </div>
                                <div class="form-group d-flex justify-content-center">
                                    <button type="submit" class="btn btn-primary px-3" name="sign-up">Sign Up</button>
                                </div>

                                <div class="form-group d-md-flex"
                                style="justify-self: center; width: 360px; align-items: center;">
                                <div class="w-50 text-left">
                                    <p style="color: rgb(4, 24, 41); margin: 0%;">Already have an account?</p>
                                </div>
                                <div class="w-50 text-md-right">
                                    <a href="signin.php" class="form-control btn btn-primary submit px-3"
                                        style="width: 95px; height: 45px; ">Sign In</a>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    

    <script src="js/jquery.min.js"></script>
    <script src="js/popper.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/signupadmin.js"></script>

</body>

</html>