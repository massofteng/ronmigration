<?php
include("newdb_conn.php");
include("olddb_conn.php");

//ro_adverts_subscription 
$sql = "SELECT * FROM ro_user_profiles";
$result = mysqli_query($old_conn, $sql);
ini_set('max_execution_time', '0');
if ($result->num_rows > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    // echo '<pre>';
    // print_r($row);
    // echo '</pre>';
    // exit;
    $profile_type = $row['profile_type'] == "person" ? "individual" : "company";
    $price_type = $row['profile_type'] == "person" ? "monthly" : "yearly";

    if($row['profile_current_upgrade']=="easy" && $profile_type=="individual"){
      $package_id = 6;
    }
    else if($row['profile_current_upgrade']=="cool" && $profile_type=="individual"){
      $package_id = 7;
    }
    else if($row['profile_current_upgrade']=="pro" && $profile_type=="individual"){
      $package_id = 8;
    }
    else if($row['profile_current_upgrade']=="easy" && $profile_type=="company"){
      $package_id = 2;
    }
    else if($row['profile_current_upgrade']=="cool" && $profile_type=="company"){
      $package_id = 3;
    }
    else if($row['profile_current_upgrade']=="pro" && $profile_type=="company"){
      $package_id = 4;
    }else{
      $package_id = 1;
    }

    if($package_id!=1){
      $insert_sql = "INSERT INTO subscription_user_purchases (
          `package_id`,
          `user_id`,
          `profile_id`,
          `invoice_no`,
          `price_type`, 
          `price`,
          `start_date`,
          `end_date`
      )
      VALUES (
          $package_id, 
          '" . $row['user_id'] . "', 
          '" . $row['profile_id'] . "', 
          0, 
          '" . $price_type . "', 
          0,
          '" . date("Y-m-d H:i:s",$row['created']) . "',
          '" . date("Y-m-d H:i:s",$row['created']) . "'
      )";

      if ($new_conn->query($insert_sql) === TRUE) {
        echo $row['user_id'] . ' ' . 'Added</br>';
      } else {
        echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
      }
  }
}
} else {
  echo "0 results found";
}
