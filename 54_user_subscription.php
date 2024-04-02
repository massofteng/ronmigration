<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM ro_adverts_subscription";
$result = mysqli_query($old_conn, $sql);
ini_set('max_execution_time', '0');
if ($result->num_rows > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    // echo '<pre>';
    // print_r($row);
    // echo '</pre>';
    // exit;

    $price_type = $row['type'] == "year" ? "Yearly" : "Monthly";

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
        '" . $row['subscription_id'] . "', 
        '" . $row['user_id'] . "', 
        '" . $row['user_id'] . "', 
        '" . $row['invoice_id'] . "', 
        '" . $price_type . "', 
        '" . $row['price'] . "',
        '" . date("Y-m-d H:i:s",$row['created']) . "',
        '" . date("Y-m-d H:i:s",$row['expiration']) . "'
    )";


    if ($new_conn->query($insert_sql) === TRUE) {
      echo $row['user_id'] . ' ' . 'Added</br>';
    } else {
      echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
    }
  }
} else {
  echo "0 results found";
}
