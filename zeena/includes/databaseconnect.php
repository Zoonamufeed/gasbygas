<!--Connecting database-->
<?Php
$conn = mysqli_connect("127.0.0.1","root","","gasbygas");
if(mysqli_connect_errno()){
    //throw an error
    echo "Failed to connect to MySQL! Please contact the admin.";
    return;
}
?>