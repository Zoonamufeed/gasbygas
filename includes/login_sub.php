
<?php
    session_start();
    require("databaseconnect.php");
    
    $sql = "SELECT * FROM admin WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    // Retrieve email and password 
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = sha1($_POST['password']); 
    
        $sql = "SELECT * FROM admin WHERE email = '$email' AND password = '$password'";
        $result = mysqli_query($conn, $sql);
    
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
    
            // Store session variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['admin_type'] = $row['admin_type'];
            $_SESSION['preferred_outlet'] = $row['preferred_outlet']; // Add preferred_outlet to session
    
            if ($row['admin_type'] === 'Headoffice') {
                $_SESSION['message'] = [
                    'type' => 'success',
                    'text' => 'Welcome, Successfully Logged in!'
                ];
                header("Location: ../headoffice.php");
            } elseif ($row['admin_type'] === 'Outlet') {
                $_SESSION['message'] = [
                    'type' => 'success',
                    'text' => 'Welcome, Successfully Logged in!'
                ];
                header("Location: ../outlet.php");
            } else {
                $_SESSION['message'] = [
                    'type' => 'danger',
                    'text' => 'Invalid admin type.'
                ];
                header("Location: ../signin.php");
            }
        } else {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Invalid email or password.'
            ];
            header("Location: ../signin.php");
        }
    }
    exit;
    ?>
    
