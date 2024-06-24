<?php
include("newdb_conn.php");
include("olddb_conn.php");

// Initialize counter for no payment data found
$no_payment_data_count = 0;

//ro_adverts_subscription 
$sql = "SELECT * FROM ro_user_profiles";
$result = mysqli_query($old_conn, $sql);
ini_set('max_execution_time', '0');
if ($result->num_rows > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        
        $profile_type = $row['profile_type'] == "person" ? "individual" : "company";

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

        $payment_row = "SELECT price, invoice_id, profile_upgrade_period, created, expiration FROM ro_profile_upgrade_orders WHERE user_id = " . $row['user_id'] . " AND profile_id = " . $row['profile_id'];
        $result2 = mysqli_query($old_conn, $payment_row);
        $payment_row = mysqli_fetch_assoc($result2);

        if ($payment_row) {
            $invoice_no = isset($payment_row['invoice_id']) ? $payment_row['invoice_id'] : NULL;
            $price = isset($payment_row['price']) ? $payment_row['price'] : 0;

            $price_type = "yearly";
            if($payment_row['profile_upgrade_period'] == "month"){
                $price_type = "monthly";
            }
            if($payment_row['profile_upgrade_period'] == "year"){
                $price_type = "yearly";
            }

            $current_position = 'upgrade';
            
            $start_date = date('Y-m-d H:i:s', $payment_row['created']);

            $end_date = date('Y-m-d H:i:s', $payment_row['created']);

            if($package_id != 1 && $price > 0){
                $insert_sql = "INSERT INTO subscription_user_purchases (
                    `package_id`,
                    `user_id`,
                    `profile_id`,
                    `invoice_no`,
                    `price_type`, 
                    `price`,
                    `current_position`,
                    `start_date`,
                    `end_date`
                )
                VALUES (
                    $package_id, 
                    '" . $row['user_id'] . "', 
                    '" . $row['profile_id'] . "', 
                    '" . $invoice_no . "', 
                    '" . $price_type . "', 
                    '" . $price . "', 
                    '" . $current_position . "', 
                    '" . date("Y-m-d H:i:s", $payment_row['created']) . "',
                    '" . date("Y-m-d H:i:s", $payment_row['expiration']) . "'
                )";

                if ($new_conn->query($insert_sql) === TRUE) {
                    echo $row['user_id'] . ' ' . 'Added</br>';
                } else {
                    echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
                }
            }
        } else {
            echo "No payment data found for user_id " . $row['user_id'] . " and profile_id " . $row['profile_id'] . "<br>";
            $no_payment_data_count++;
        }
    }
} else {
    echo "0 results found";
}

// Echo the total count of no payment data found
echo "Total count of no payment data found: " . $no_payment_data_count . "<br>";
?>
