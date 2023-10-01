<?php
$servername = "localhost";
$username = "root";
$database = 'ronreload';
$password = "";

// Create connection
$new_conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($new_conn->connect_error) {
  die("Connection failed: " . $new_conn->connect_error);
}
//echo "New DB Connected successfully";
?>