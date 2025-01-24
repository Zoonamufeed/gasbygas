<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

// Start session to store message
session_start();

if (isset($_POST['send'])) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'gasoutlet6@gmail.com';
        $mail->Password = 'rfsz navv gxwv tlxp'; 
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('gasoutlet6@gmail.com');
        $mail->addAddress($_POST['email']); 
        $mail->addReplyTo('gasoutlet6@gmail.com');

        $mail->isHTML(true);
        $mail->Subject = $_POST['subject'];
        $mail->Body = $_POST['message'];

        $mail->send();
        
        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Email sent successfully!'
        ];
    } catch (Exception $e) {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo
        ];
    }

    //after the message
    header('Location: ../outletemail.php');
    exit();
}
?>



