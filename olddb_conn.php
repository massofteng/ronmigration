<?php
$servername = "localhost";
$username = "root";
$database = 'old_mv_2';
$password = "";

// Create connection
$old_conn = new mysqli($servername, $username, $password, $database);

//print_r($old_conn);exit;

// Check connection
if ($old_conn->connect_error) {
  die("Connection failed: " . $old_conn->connect_error);
}
//echo "Old DB Connected successfully";
?>