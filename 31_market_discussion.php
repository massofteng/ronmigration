<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM ro_advertisement where template_id=8 limit 1000";
$result = mysqli_query($old_conn, $sql);
ini_set('max_execution_time', '0');
if (mysqli_fetch_array($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {

        if ($row['city_id'] == 'zuerich') {
            $city_id = 2;
        } else if ($row['city_id'] == 'zurich_en') {
            $city_id = 1;
        } else if ($row['city_id'] == 'lausanne' || $row['city_id'] == 'geneve') {
            $city_id = 3;
        } else if ($row['city_id'] == 'basel') {
            $city_id = 4;
        } else if ($row['city_id'] == 'bern') {
            $city_id = 5;
        } else if ($row['city_id'] == 'luzern') {
            $city_id = 6;
        } else if ($row['city_id'] == 'st_gallen') {
            $city_id = 7;
        } else if ($row['city_id'] == 'winterthur') {
            $city_id = 8;
        } else if ($row['city_id'] == 'family') {
            $city_id = 9;
        } else {
            $city_id = 0; //No city
        }

        $post_type = "offer";
        if ($row['advert_type'] == "gesuch") {
            $post_type = "wanted";
        }

        $category_id = $row['category_id'];
        $sql2 = "SELECT post_type, category, sub_category FROM dummy_categories where category_id = $category_id";
        $discussion_type = '';
        $category = 0;
        $sub_category = 0;


        if ($result2 = mysqli_query($old_conn, $sql2)) {
            if (mysqli_num_rows( $result2 ) > 0) {
                $row2 = mysqli_fetch_assoc($result2);
                $discussion_type = $row2['post_type'];
                $category = $row2['category'];
                $sub_category = $row2['sub_category'];
            }
        }



        $slug = str_replace(" ", "-", $row['sp_events_title']);
        $user_id = $row['user_id'];

        $status = ($row['active'] == "Y") ? 1 : 0;

        $published = date('Y-m-d H:i:s', $row['published']);
        $expiration = date('Y-m-d', $row['expiration']);
        $created = date('Y-m-d', $row['created']);

        $phone = "NULL";
        if(!empty($row['phone'])){
            $phone = $row['phone'];
        }

        //Publication end will be created_at + 29 days

        $publication_end_date = date('Y-m-d', strtotime($created . ' + 29 days'));

        if ($city_id != 0) {
            $insert_sql = "INSERT INTO market_discussions (
                `id`,
                `discussion_type`,
                `post_type`,
                `post_source`,
                `title`,
                'slug',
                `description`,
                `category_id`,
                `sub_category_id`,
                `pricing`,
                'price',
                'trade_product',
                'donation_method',
                'donation_organization_name',
                'publication_end_date',
                'one_year_publication_plan',
                'communication_method',
                'email_address',
                'phone_number',
                'location',
                'event_organizer',
                'zip_code',
                'city',
                'city_id',
                'publication_city',
                'is_course',
                'frequency_of_course',
                'tags',
                'image',
                'link',
                `status`,
                `created_by`,
                `creator_profile_id`,
                `created_at`,
                'updated_at'
                )
             VALUES (
                '" . $row['advert_id'] . "',
                '" . $discussion_type . "',
                '" . $post_type . "',
                '" . 'user' . "',
                '" . $row['sp_events_title'] . "',
                NULL,
                '" . $row['sp_events_text'] . "',
                '" . $row['subject'] . "',
                '" . $category . "',
                '" . $sub_category . "',
                'price',
                '" . $row['advert_price'] . "',
                NULL,
                $phone,
                '" . $row['email'] . "',
                '" .  $publication_end_date . "',
                NULL,
                NULL,
                '" . $sub_category . "',
                '" . $row['google_address'] . "',
                '" . $row['sp_events_organizer'] . "',
                NULL,
                '" . $row['city_id'] . "',
                '" . $city_id . "',
                '" . $city_id . "',
                NULL,
                NULL,
                NULL,
                NULL,
                '" . $row['sp_events_text_link'] . "',
                '" . $status . "',
                '" . $row['user_id'] . "',
                '" . $row['profile_id'] . "',
                '" . $created . "',
                '" . $created . "'
                )";
            echo $insert_sql;exit;    
            if ($new_conn->query($insert_sql) === TRUE) {
                echo $row['user_id'] . ' ' . 'Added</br>';
            } else {
                //echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
            }

            // echo "<pre>";
            // print_r($category);
            // exit;

            $event_start_date = date('Y-m-d', $row['sp_events_date']);
            $event_start_day = date('l', $row['sp_events_date']);

            //days ->event start date a je bar thakbe seta ber kore set korte hobe
            $sp_events_time = 0;
            $time = $row['sp_events_time'];
            $parts = explode(":", $time);
            if ($parts) {
                $sp_events_time = $parts[0];
            }

            if ($raw['template_id'] == 8) {
                $insert_sql = "INSERT INTO market_events_details (
                    `market_dis_id`,
                    `event_start_date`,
                    `event_end_date`,
                    `days`,
                    `event_start_time`,
                    `event_end_time`,
                    `created_at`,
                    `updated_at`
                    )
                 VALUES (
                    '" . $row['advert_id'] . "',
                    '" . $event_start_date . "',
                    NULL,
                    '" . $event_start_day . "',
                    '" . $sp_events_time . "',
                    NULL,
                    '" . $created . "',
                    '" . $created . "
                    )";
                if ($new_conn->query($insert_sql) === TRUE) {
                    echo $row['user_id'] . ' ' . 'Added</br>';
                } else {
                    //echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
                }
            }
        }
    }
} else {
    echo "0 results found";
}
