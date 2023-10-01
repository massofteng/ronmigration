<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM ro_landing_pages_city_kuche";
$result = mysqli_query($old_conn, $sql);
//ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
ini_set('max_execution_time', '0'); // for infinite time of execution 
if (mysqli_fetch_array($result) > 0) {
  while($row = mysqli_fetch_assoc($result)) {

    echo '<pre>';
    var_dump($row);exit;
    $status = 'Inactive';
    if($row['user_active']=="Y"){
        $status = 'Active';
    }

    $insert_sql = "INSERT INTO users (`id`,`name`, `email`, `password`,`city_id`,`status`)
    VALUES ('".$row['user_id']."', '".$row['user_firstname']."', '".$row['user_email']."','".$row['user_password']."',0,'".$status."')";
    
    if ($new_conn->query($insert_sql) === TRUE) {
        echo $row['user_firstname']. ' '. 'Added</br>';
    } else {
        //echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
    }
  }
} else {
  echo "0 results found";
}
?>