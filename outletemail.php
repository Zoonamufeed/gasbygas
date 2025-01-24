<?php
session_start();
?>
<!Doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <?php include "includes/header_links.php"; ?>
        <link rel="stylesheet" href="css/smsheadoffice.css">
        <title>Outlet Email</title>
    </head>
    <body>
        <!-- navbar -->
        <?php include "includes/headofficenavbar.php"; ?>

<!-- Message display for approve and allocate date-->
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

<!-- SMS Message Form -->
<div class="headoffice-form">
            <form action="phpmailer/mailed.php" method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" aria-label="default input example" required />
              </div>
              <div class="mb-3">
                <label for="subject" class="form-label">Subject</label>
                <input type="text" class="form-control" id="subject" name="subject" aria-label="default input example" required />
              </div>
              <div class="mb-3">
                <div class="form-floating">
                    <textarea class="form-control" id="message" name="message" style="height: 250px" required></textarea>
                    <label for="message">Message!</label>
                  </div>
                </div>
              <button type="submit" class="btn btn-primary" name="send">Send Message</button>
            </form>
            </div>
             <!-- Footer -->
    <?php include "includes/outletfooter.php"; ?>

</body>
</html>