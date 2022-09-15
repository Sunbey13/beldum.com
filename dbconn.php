<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'beldum';

$conn = new mysqli($host, $user, $password, $dbname);
if($conn -> connect_error) {
    die('Error: ('.$conn->connect_errno.')'.$conn->connect_error);
}
?>