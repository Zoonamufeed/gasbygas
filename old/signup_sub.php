<?php
include 'databaseconnect.php';
session_start();


if (isset($_POST['sign-up'])) {
    $name = ( $_POST['name']);
    $contact_no = ($_POST['contact_no']);
    $nic = ($_POST['nic']);
    $email = ($_POST['email']);
    $password = ( $_POST['password']);
    $hashed_password = sha1($password);
    $admin_type = ( $_POST['admin_type']);
    $preferred_outlet =($_POST['preferred_outlet']);

    $sql = "SELECT * FROM `admin` WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['message'] = [
            'type' => 'warning',
            'text' => 'There is already registered email.',
        ];
        header("Location: ../signup.php");
        exit;
    }
    // Check if NIC already exists
    $sql_nic = "SELECT * FROM `admin` WHERE nic = '$nic'";
    $result_nic = mysqli_query($conn, $sql_nic);

    if (mysqli_num_rows($result_nic) > 0) {
        $_SESSION['message'] = [
            'type' => 'warning',
            'text' => 'This NIC is already registered.',
        ];
        header("Location: ../signup.php");
        exit;
    }
    // Insert into database
    $sql = "INSERT INTO `admin` (name, contact_no, nic, email, password, admin_type, preferred_outlet) 
            VALUES ('$name', '$contact_no', '$nic', '$email', '$hashed_password', '$admin_type', '$preferred_outlet')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = [
            'type' => 'primary',
            'text' => 'Successfully signed up. Please Log in',
        ];
        header("Location: ../signin.php");
        exit;
    } else {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'Signup failed. Please try again later.',
        ];
    }

    header("Location: ../signup.php");
    exit;
}
?>
