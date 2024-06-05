<?php
include("newdb_conn.php");
include("olddb_conn.php");

//LIMIT 200000 OFFSET 100000 647383 655462
$sql = "SELECT * FROM core_users where user_id > 678676";
$result = mysqli_query($old_conn, $sql);
//ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
ini_set('max_execution_time', '0'); // for infinite time of execution 
if (mysqli_fetch_array($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {

    $status = 'Inactive';
    if ($row['user_active'] == "Y") {
      $status = 'Active';
    }

    $user_id = $row['user_id'];
    $created_at = date('Y-m-d H:i:s', $row['user_created']);

    $sql2 = "SELECT city_id FROM ro_users where user_id=$user_id";

    if ($result2 = mysqli_query($old_conn, $sql2)) {
      $row2 = mysqli_fetch_assoc($result2);
      // echo "<pre>";
      // echo $user_id;
      // print_r($row2);
      if ($row2['city_id'] == 'zuerich') {
        $city_id = 2;
      } else if ($row2['city_id'] == 'zurich_en') {
        $city_id = 1;
      } else if ($row2['city_id'] == 'lausanne' || $row2['city_id'] == 'geneve') {
        $city_id = 3;
      } else if ($row2['city_id'] == 'basel') {
        $city_id = 4;
      } else if ($row2['city_id'] == 'bern') {
        $city_id = 5;
      } else if ($row2['city_id'] == 'luzern') {
        $city_id = 6;
      } else if ($row2['city_id'] == 'st_gallen') {
        $city_id = 7;
      } else if ($row2['city_id'] == 'winterthur') {
        $city_id = 8;
      } else if ($row2['city_id'] == 'family') {
        $city_id = 9;
      } else {
        $city_id = 0; //No city
      }
    }

    if ($city_id != 0) {
    $sql3 = "SELECT profile_id FROM ro_user_profiles where user_id=$user_id limit 1";
    $profile_id = 0;
    $count = 0;
    if ($result3 = mysqli_query($old_conn, $sql3)) {
      $row3 = mysqli_fetch_assoc($result3);
        // echo "<pre>";
        // print_r($row3);exit;
      $profile_id = $row3['profile_id'];
      $password = mysqli_real_escape_string($new_conn, $row['user_password']);
      $insert_sql = "INSERT INTO users (
            `id`,
            `email`, 
            `password`,
            `city_id`,
            `active_profile_id`,
            `default_profile_id`,
            `default_registered_profile`,
            `status`,
            `created_at`
            )
          VALUES (
            '" . $row['user_id'] . "',
            '" . $row['user_email'] . "',
            '" . $password . "',
            '" . $city_id . "',
            '" . $profile_id . "',
            '" . $profile_id . "',
            '" . $profile_id . "',
            '" . $status . "',
            '" . $created_at . "'
            )";
      if ($new_conn->query($insert_sql) === TRUE) {
        echo $row['user_id'] . ' ' . 'Added</br>';
      } else {
        //echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
      }
    }
    // }else{
    //   echo $count++;
    //   echo ' Not in city</br>';
     }
  }
} else {
  echo "0 results found";
}
