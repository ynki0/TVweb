<?php
$servername = "localhost";
$username = "admin";
$password = "0SWwZpG004AwaKK2";
$dbname = "cinder";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
