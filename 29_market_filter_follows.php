<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM ro_adverts_passive_search limit 5";

$result = mysqli_query($old_conn, $sql);

//ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
ini_set('max_execution_time', '0'); // for infinite time of execution
$lagn_group = [];

if (mysqli_fetch_array($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {

        $notify_alert = $newsletter = 0;
        if ($row['notify_alert'] == 'Y') {
            $notify_alert = 1;
        }
        if ($row['newsletter'] == 'Y') {
            $newsletter = 1;
        }

   $insert_sql = "INSERT INTO market_filter_follows (
     
      `category_id`, 
      `user_id`, 
      `profile_id`, 
      `get_instant_email`,
      `send_notification`,
      `created_at`,
      `updated_at`
      )
    VALUES (
      
      '" . $row['category_id'] . "', 
      '" . $row['user_id'] . "', 
      '" . $row['user_id'] . "', 
      '" . $notify_alert . "', 
      '" . $newsletter . "', 
      '" . date('Y-m-d H:i:s') . "',
      '" . date('Y-m-d H:i:s') . "'
      )";

        if ($new_conn->query($insert_sql) === TRUE) {
            echo $row['user_id'] . 'Added</br>';
        } else {
            echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
        }
    }
} else {
    echo "0 results found";
}