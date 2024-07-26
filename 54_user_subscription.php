<?php
include("newdb_conn.php");
include("olddb_conn.php");

// Initialize counter for no payment data found
$no_payment_data_count = 0;

// Fetch user profiles with non-free upgrades
$sql = "SELECT * FROM ro_user_profiles WHERE profile_current_upgrade != 'free'";
$result = mysqli_query($old_conn, $sql);
ini_set('max_execution_time', '0');
// echo '<pre>';
// print_r($result->num_rows);
// echo '</pre>';

// exit;
if ($result->num_rows > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        
        $profile_type = $row['profile_type'] == "person" ? "individual" : "company";

        switch ($row['profile_current_upgrade']) {
            case "easy":
                $package_id = ($profile_type == "individual") ? 6 : 2;
                break;
            case "cool":
                $package_id = ($profile_type == "individual") ? 7 : 3;
                break;
            case "pro":
                $package_id = ($profile_type == "individual") ? 8 : 4;
                break;
            default:
                $package_id = ($profile_type == "company") ? 1 : 5;
                break;
        }

        $payment_sql = "
            SELECT price, invoice_id, profile_upgrade_period, created, expiration 
            FROM ro_profile_upgrade_orders 
            WHERE user_id = " . $row['user_id'] . " AND profile_id = " . $row['profile_id'] . "
            ORDER BY created DESC LIMIT 1";
        $result2 = mysqli_query($old_conn, $payment_sql);
        $payment_row = mysqli_fetch_assoc($result2);

        // echo '<pre>';
        // print_r($result2->num_rows);
        // echo '</pre>';

        // exit;

        if ($payment_row) {
            $invoice_no = isset($payment_row['invoice_id']) ? $payment_row['invoice_id'] : NULL;
            $price = isset($payment_row['price']) ? $payment_row['price'] : 0;

            $price_type = ($payment_row['profile_upgrade_period'] == "month") ? "monthly" : "yearly";

            $current_position = 'upgrade';

            $start_date = date('Y-m-d H:i:s', $payment_row['created']);

        //     echo '<pre>';
        // print_r($payment_row['created']);
        // echo '</pre>';

        // exit;

            // Handle expiration
            $expiration_date = isset($payment_row['expiration']) ? $payment_row['expiration'] : NULL;
            $expiration_date = date('Y-m-d H:i:s', $expiration_date);
            if (empty($expiration_date)) {
                if ($payment_row['profile_upgrade_period'] == "year") {
                    $expiration_date = date('Y-m-d H:i:s', strtotime('+' . $payment_row['profile_upgrade_period_amount'] . ' year', $payment_row['created']));
                } else {
                    $expiration_date = date('Y-m-d H:i:s', strtotime('+' . $payment_row['profile_upgrade_period_amount'] . ' month', $payment_row['created']));
                }
            }
            // $expiration_date = date('Y-m-d H:i:s', $expiration_date);
            // echo '<pre>';
            // print_r($expiration_date);
            // echo '</pre>';

            // exit;
            
            $today = date('Y-m-d H:i:s');
            $status = 'inactive';

            if ($expiration_date && $expiration_date > $today) {
                $status = 'active';
            }

            if ($expiration_date && $expiration_date > $today) {
                $insert_sql = "INSERT INTO subscription_user_purchases (
                    package_id, user_id, profile_id, invoice_no, price_type, 
                    price, current_position, start_date, end_date, status
                ) VALUES (
                    $package_id, 
                    '" . $row['user_id'] . "', 
                    '" . $row['profile_id'] . "', 
                    '" . $invoice_no . "', 
                    '" . $price_type . "', 
                    '" . $price . "', 
                    '" . $current_position . "', 
                    '" . $start_date . "',
                    '" . $expiration_date . "',
                    '" . $status . "'
                )";

                if ($new_conn->query($insert_sql) === TRUE) {
                    echo $row['user_id'] . ' ' . 'Added</br>';

                    $packageDetailsSql = "SELECT chat_count, local_chat_count, local_hero_chat_count, local_super_hero_chat_count 
                                          FROM subscription_package_details 
                                          WHERE package_id = $package_id 
                                          LIMIT 1";
                    $packageResult = mysqli_query($new_conn, $packageDetailsSql);
                    $packageDetails = mysqli_fetch_assoc($packageResult);

                    $freeChats = isset($packageDetails['chat_count']) ? $packageDetails['chat_count'] : 0;
                    $localChats = isset($packageDetails['local_chat_count']) ? $packageDetails['local_chat_count'] : 0;
                    $heroChats = isset($packageDetails['local_hero_chat_count']) ? $packageDetails['local_hero_chat_count'] : 0;
                    $superHeroChats = isset($packageDetails['local_super_hero_chat_count']) ? $packageDetails['local_super_hero_chat_count'] : 0;

                    // Debugging: Check the values before using them in the SQL query
                    echo "Debug Info: package_id=$package_id, freeChats=$freeChats, localChats=$localChats, heroChats=$heroChats, superHeroChats=$superHeroChats<br>";

                    $updateOrCreateSql = "INSERT INTO user_chat_assigns (
                        user_id, profile_id, pause_chat, assign_type, package_id, 
                        available_free_chat, available_local_chat, available_local_hero_chat, available_super_local_hero_chat, start_date, end_date, status
                    ) VALUES (
                        '" . $row['user_id'] . "', 
                        '" . $row['profile_id'] . "', 
                        1, 
                        'package', 
                        $package_id, 
                        $freeChats, 
                        $localChats, 
                        $heroChats, 
                        $superHeroChats, 
                        '" . $start_date . "', 
                        '" . $expiration_date . "', 
                        1
                    ) ON DUPLICATE KEY UPDATE 
                        pause_chat = 1, 
                        assign_type = 'package', 
                        package_id = $package_id, 
                        available_free_chat = $freeChats, 
                        available_local_chat = $localChats, 
                        available_local_hero_chat = $heroChats, 
                        available_super_local_hero_chat = $superHeroChats, 
                        start_date = '" . $start_date . "', 
                        end_date = '" . $expiration_date . "', 
                        status = 1";

                    if ($new_conn->query($updateOrCreateSql) === TRUE) {
                        echo $row['user_id'] . ' ' . 'User chat assigned/updated</br>';
                    } else {
                        echo "Error: " . $updateOrCreateSql . "<br>" . $new_conn->error;
                    }

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
