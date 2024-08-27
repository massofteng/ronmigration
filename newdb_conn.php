<?php
$servername = "localhost";
$username = "root";
$database = 'local_mv_2';
$password = "";

// Create connection
$new_conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($new_conn->connect_error) {
  die("Connection failed: " . $new_conn->connect_error);
}
//echo "New DB Connected successfully";
?>