<?php
include("newdb_conn.php");
include("olddb_conn.php");

//AND ro_newsletter_orders.advert_id = 485358"

$sql = "SELECT order_id, advert_id, newsletter_id, order_cities, category_id, user_id, profile_id,content_header,content_text FROM ro_newsletter_orders 
JOIN ro_newsletter_content ON ro_newsletter_orders.advert_id = ro_newsletter_content.relation_id where category_id > 0
";

$result = mysqli_query($old_conn, $sql);
ini_set('max_execution_time', '0');
if (mysqli_fetch_array($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {

        if ($row['order_cities'] == 'zuerich') {
            $city_id = 2;
        } else if ($row['order_cities'] == 'zurich_en') {
            $city_id = 1;
        } else if ($row['order_cities'] == 'lausanne' || $row['order_cities'] == 'geneve' || $row['order_cities'] == 'romandie') {
            $city_id = 3;
        } else if ($row['order_cities'] == 'basel') {
            $city_id = 4;
        } else if ($row['order_cities'] == 'bern') {
            $city_id = 5;
        } else if ($row['order_cities'] == 'luzern') {
            $city_id = 6;
        } else if ($row['order_cities'] == 'st_gallen') {
            $city_id = 7;
        } else if ($row['order_cities'] == 'winterthur') {
            $city_id = 8;
        } else if ($row['order_cities'] == 'family') {
            $city_id = 9;
        } else {
            $city_id = 0; //No city
        }

        if ($city_id != 0) {
            $newsletter_id = $row['newsletter_id'];
            $sql2 = "SELECT * FROM ro_newsletter where newsletter_id = $newsletter_id";

            if ($result2 = mysqli_query($old_conn, $sql2)) {
                $row2 = mysqli_fetch_assoc($result2);
                if ($row2) {
                    $category_id = $row['category_id'];
                    $sql3 = "SELECT post_type FROM dummy_categories where category_id = $category_id";
                    $discussion_type = '';
                    if ($result3 = mysqli_query($old_conn, $sql3)) {
                        if ($result3 && mysqli_num_rows($result3) > 0) {
                            $row3 = mysqli_fetch_assoc($result3);
                            $discussion_type = $row3['post_type'];

                            //For getting price from ro_transaction
                            $user_id = $row['user_id'];
                            $profile_id = $row['profile_id'];
                            $transaction_price = 0;
                            $transaction_id = 0;
                            $transaction_date = NULL;

                            $transaction = "SELECT transaction_price,transaction_id,transaction_date FROM ro_transactions where user_id = $user_id";
                            if ($transaction_result = mysqli_query($old_conn, $transaction)) {
                                if ($transaction_result && mysqli_num_rows($transaction_result) > 0) {
                                    $transaction_result_row = mysqli_fetch_assoc($transaction_result);
                                    $transaction_price = $transaction_result_row['transaction_price'];
                                    $transaction_id = $transaction_result_row['transaction_id'];
                                    $transaction_date = $transaction_result_row['transaction_date'];
                                }
                            }

                            //Get order date
                            $order_date = "0000-00-00";
                            $order_id = $row['order_id'];
                            $order_date = "SELECT newsletter_date FROM ro_newsletter_order_dates
                             WHERE newsletter_date = $order_id 
                              AND newsletter_date > CURDATE() 
                            ORDER BY newsletter_date asc";
                            $order_date_result = mysqli_query($old_conn, $order_date);

                            $future_date_result_row = [];
                            if ($order_date_result && mysqli_num_rows($order_date_result) > 0) {
                                while ($order_row = mysqli_fetch_assoc($order_date_result)) {
                                    if (empty($order_row['newsletter_date'])) {
                                        $updated_at = $created_at = date('Y-m-d H:i:s');
                                        $future_date_result_row[] =  $updated_at;
                                    } else {
                                        $future_date_result_row[] = $order_row['newsletter_date'];
                                        $updated_at = $created_at = $order_row['newsletter_date'] . ' 00:00:00';
                                    }
                                }
                            } else {
                                $updated_at = $created_at = date('Y-m-d H:i:s');
                                $future_date_result_row[] =  $updated_at;
                            }

                            $status = ($row2['newsletter_status'] == "ready") ? 1 : 0;


                            $content_header = mysqli_real_escape_string($new_conn, $row['content_header']);
                            $content_text = mysqli_real_escape_string($new_conn, $row['content_text']);

                            $advert_id = $row['advert_id'];
                            $check_sql = "SELECT * FROM market_discussions where id=$advert_id";
                            $check_re = mysqli_query($new_conn, $check_sql);
                            // echo mysqli_num_rows($check_re);exit;
                            if (mysqli_num_rows($check_re) < 0) {
                                //echo 'No';
                                $insert_sql = "INSERT INTO market_discussions (
                                        `id`,
                                            `discussion_type`,
                                            `post_type`,
                                            `post_source`,
                                            `title`,
                                            `slug`,
                                            `description`,
                                            `category_id`,
                                            `sub_category_id`,
                                            `pricing`,
                                            `price`,
                                            `trade_product`,
                                            `donation_method`,
                                            `donation_organization_name`,
                                            `publication_end_date`,
                                            `one_year_publication_plan`,
                                            `communication_method`,
                                            `phone_number`,
                                            `email_address`,
                                            `location`,
                                            `event_organizer`,
                                            `zip_code`,
                                            `city`,
                                            `city_id`,
                                            `publication_city`,
                                            `is_course`,
                                            `frequency_of_course`,
                                            `tags`,
                                            `image`,
                                            `link`,
                                            `status`,
                                            `created_by`,
                                            `creator_profile_id`,
                                            `created_at`,
                                            `updated_at`
                                            )
                                        VALUES (
                                            '" . $advert_id . "',
                                            'commercial',
                                            NULL,
                                            'user',
                                            '".$content_header."',
                                            NULL,
                                             '".$content_text."',
                                            1',
                                            1,
                                            'price',
                                            NULL,
                                            NULL,
                                            NULL,
                                            NULL,
                                            NULL,
                                            0,
                                            NULL,
                                            NULL,
                                            NULL,
                                            NULL,
                                            NULL,
                                            NULL,
                                            '" . $city_id . "',
                                            '" . $city_id . "',
                                            '" . $city_id . "',
                                            NULL,
                                            NULL,
                                            NULL,
                                            NULL,
                                            NULL,
                                            0,
                                            '" . $user_id . "',
                                            '" . $profile_id . "',
                                            '" . $created_at . "',
                                            '" . $created_at . "'
                                            )";
                                //echo $insert_sql;
                                //exit;
                                if ($new_conn->query($insert_sql) === TRUE) {
                                    //echo $row['user_id'] . ' ' . 'Added</br>';
                                } else {
                                    //echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
                                }
                            }

                            //echo  $status ;exit;

                            $insert_sql = "INSERT INTO boost_post_process (
                            `post_id`, 
                            `discussion_type`,
                            `date_type`,
                            `posting_process`,
                            `advertisement_type`,
                            `placement`,
                            `invoice_no`,
                            `user_id`,
                            `profile_id`,
                            `is_highlights`,
                            `status`,
                            `created_at`,
                            `updated_at`
                            )
                         VALUES (
                            '" . $newsletter_id . "',
                            '" . $discussion_type . "',
                            'normal',
                            'boostPost',
                            'newsletter',
                            NULL,
                            NULL,
                            '" . $user_id . "',
                            '" . $profile_id . "',
                            0,
                            '" . $status . "',
                            '" . $created_at . "',
                            '" . $updated_at . "'
                            )";

                            if ($new_conn->query($insert_sql) === TRUE) {
                                //echo $row['user_id'] . ' ' . 'Added</br>';
                            }

                            $boosts_process_id = $new_conn->insert_id;
                            $date = json_encode([$created_at]); // Ensure $created_at is a valid variable

                            $insert_boosts_details_sql = "INSERT INTO boost_post_process_details (
                            boost_post_process_id,
                            discussion_type,
                            newsletter_id,
                            block_name,
                            advertisement_type,
                            placement,
                            city_id,
                            `date`,
                            total_amount,
                            discount,
                            payable_qty,
                            `status`,
                            post_id,
                            user_id,
                            profile_id,
                            created_at,
                            updated_at
                        ) VALUES (
                            '$boosts_process_id',
                            '$discussion_type',
                            NULL,
                            NULL,
                            'website',
                            NULL,
                            '$city_id',
                            '$date',
                            '$transaction_price',
                            NULL,
                            0,
                            '$status',
                            '$newsletter_id',
                            '$user_id',
                            '$profile_id',
                            '$created_at',
                            '$updated_at'
                        )";
                            //echo $insert_boosts_details_sql;
                            if ($new_conn->query($insert_boosts_details_sql) === TRUE) {
                                // echo $row['user_id'] . ' ' . 'Added</br>';
                            }

                            //For insert payment leadger
                            //Billing info contact info thake ashbe

                            if (empty($transaction_date)) {
                                $transaction_date = date("Y-m-d");
                            } else {
                                $transaction_date = date('Y-m-d', $transaction_date);
                            }

                            $contact_info = "SELECT * FROM ro_contact_info WHERE user_id = $user_id ORDER BY user_id ASC LIMIT 1";
                            $contact_info_result = mysqli_query($old_conn, $contact_info);
                            $billing_info = "NULL";


                            if ($contact_info_result && mysqli_num_rows($contact_info_result) > 0) {
                                $billing_info = [];
                                $contact = mysqli_fetch_assoc($contact_info_result);

                                $new_conn->set_charset("utf8");

                                $firstname = mysqli_real_escape_string($new_conn, $contact['firstname']);
                                $billing_info['firstname'] = $firstname;

                                $lastname = mysqli_real_escape_string($new_conn, $contact['lastname']);
                                $billing_info['lastname'] =  $lastname;

                                $company = 'NoCN';
                                if($contact['company']){
                                    $company = mysqli_real_escape_string($new_conn, $contact['company']);
                                }
                                $billing_info['company_name'] =  $company;

                                $billing_info['email'] = $contact['email'];

                                $city = mysqli_real_escape_string($new_conn, $contact['city']);
                                $billing_info['city'] = $city;

                                $billing_info = json_encode($billing_info);
                            }

                            //echo $transaction_date;exit;
                            $purpose = json_encode(["place_ad_on_website"]);
                            $insert_payment_leadger_sql = "INSERT INTO payment_leadgers (
                            `transaction_id`,
                            `prefix`, 
                            `invoice_no`,
                            `payment_type`,
                            `user_type`,
                            `subscription_purchase_id`,
                            `post_id`,
                            `custom_package_purchase_id`,
                            `boost_process_id`,
                            `amount`,
                            `discount`,
                            `vat`,
                            `total_amount`,
                            `currency`,
                            `billing_info`,
                            `purpose`,
                            `payment_method`,
                            `payment_date`,
                            `user_id`,
                            `profile_id`,
                            `status`,
                            `created_at`,
                            `updated_at`,
                            `city_id`,
                            `discussion_type`,
                            `profile_type`
                            )
                         VALUES (
                            '" . $transaction_id . "',
                            NULL,
                            NULL,
                            '" . 'one_time' . "',
                            '" . 'normal' . "',
                            NULL,
                            '" . $newsletter_id . "',
                            NULL,
                            '" . $boosts_process_id . "',
                            '" . $transaction_price . "',
                            0,
                            0,
                            '" . $transaction_price . "',
                            '" . 'CHF' . "',
                            '" . $billing_info . "',
                            '" .  $purpose . "',
                            0,
                            '" . $transaction_date . "',
                            '" . $user_id . "',
                            '" . $profile_id . "',
                            '" . $status . "',
                            '" . $created_at . "',
                            '" . $updated_at . "',
                            '" . $city_id . "',
                            '" .  $discussion_type . "',
                            '" . 'individual' . "'
                            )";
                            if ($new_conn->query($insert_payment_leadger_sql) === TRUE) {
                                // echo $row['user_id'] . ' ' . 'Added</br>';
                            }

                            //For insert payment leadger details
                            $payment_leadgers_id = $new_conn->insert_id;
                            $insert_payment_leadger_details_sql = "INSERT INTO payment_leadger_details (
                            `payment_leadgers_id`, 
                            `invoice_no`,
                            `meta_key`,
                            `amount`,
                            `discount`,
                            `user_id`,
                            `profile_id`,
                            `created_at`,
                            `updated_at`
                            )
                         VALUES (
                            '" . $payment_leadgers_id . "',
                            NULL,
                            'place_ad_on_website',
                            '" . $transaction_price . "',
                            0,
                            '" . $user_id . "',
                            '" . $profile_id . "',
                            '" . $created_at . "',
                            '" . $updated_at . "'
                            )";
                            if ($new_conn->query($insert_payment_leadger_details_sql) === TRUE) {
                                echo $row['user_id'] . ' ' . 'Added</br>';
                            }
                        }
                    }
                }
            }
        }
    }
} else {
    echo "0 results found";
}
