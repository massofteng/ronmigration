<?php
$servername = "localhost";
$username = "root";
$database = 'ronreload_back';
$password = "";

// Create connection
$old_conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($old_conn->connect_error) {
  die("Connection failed: " . $old_conn->connect_error);
}
//echo "Old DB Connected successfully";
?>