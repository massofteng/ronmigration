<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM ro_advertisement where template_id=8";
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

        $location = [];
        if (!empty($row['google_address'])) {
            $location['address'] = $row['google_address'];
            $location['latitude'] = $row['google_lat'];
            $location['longitude'] = $row['google_lng'];
            $location = json_encode($location);
        } else {
            $location =  json_encode($location);
        }


        if ($result2 = mysqli_query($old_conn, $sql2)) {
            if (mysqli_num_rows($result2) > 0) {
                $row2 = mysqli_fetch_assoc($result2);
                $discussion_type = $row2['post_type'];
                $category = $row2['category'];
                $sub_category = $row2['sub_category'];
            }
        }

        $slug = "NULL";
        if (!empty($row['sp_events_title'])) {
            $slug = str_replace(" ", "-", $row['sp_events_title']);
        }

        $user_id = $row['user_id'];

        $status = ($row['active'] == "Y") ? 1 : 0;

        $published = date('Y-m-d H:i:s', $row['published']);
        $expiration = date('Y-m-d', $row['expiration']);
        $created = date('Y-m-d', $row['created']);

        $phone = "NULL";
        if (!empty($row['phone'])) {
            $phone = $row['phone'];
        }

        $sp_events_title = "NULL";
        if (!empty($row['sp_events_title'])) {
            $sp_events_title = mysqli_real_escape_string($new_conn, $row['sp_events_title']);
        }

        $sp_events_text = "NULL";
        if (!empty($row['sp_events_title'])) {
            $sp_events_text = mysqli_real_escape_string($new_conn, $row['sp_events_text']);
        }

        // $subject = mysqli_real_escape_string($new_conn, $row['subject']);

        $url = $row['sp_events_text_link'];

        if (empty($row['url'])) {
            $url = "NULL";
        } else {
            $url = htmlentities($url);
        }

        //Publication end will be created_at + 29 days

        $publication_end_date = date('Y-m-d', strtotime($created . ' + 29 days'));

        $post_id = $row['advert_id'];
        $check_sql = "SELECT * FROM market_discussions where id=$post_id";
        $check_re = mysqli_query($new_conn, $check_sql);
        if (mysqli_num_rows($check_re) < 1) {
            if ($city_id != 0) {
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
                    '" . $row['advert_id'] . "',
                    '" . $discussion_type . "',
                    '" . $post_type . "',
                    'user',
                    '" .  $sp_events_title . "',
                    NULL,
                    '" .  $sp_events_text . "',
                    '" . $category . "',
                    '" . $sub_category . "',
                    'price',
                    '" . $row['advert_price'] . "',
                    NULL,
                    NULL,
                    NULL,
                    '" . $publication_end_date . "',
                    0,
                    NULL,
                    '" . $phone . "',
                    '" . $row['email'] . "',
                    '" . $location . "',
                    NULL,
                    NULL,
                    '" . $row['city_id'] . "',
                    '" . $city_id . "',
                    '" . $city_id . "',
                    NULL,
                    NULL,
                    NULL,
                    NULL,
                    '" . $url . "',
                    '" . $status . "',
                    '" . $row['user_id'] . "',
                    '" . $row['profile_id'] . "',
                    '" . $created . "',
                    '" . $created . "'
                    )";
                // echo $insert_sql;exit;
                if ($new_conn->query($insert_sql) === TRUE) {
                    //echo $row['user_id'] . ' ' . 'Added</br>';
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
                $sp_start_time = 0;
                if (!empty($row['sp_events_time'])) {
                    $time = $row['sp_events_time'];
                    $parts = explode(":", $time);

                    if ($parts) {
                        $sp_events_time = $parts[0];
                    }

                    $first_two_digits = substr($sp_events_time, 0, 2);
                    if (ctype_digit($first_two_digits)) {
                        $sp_start_time = (int)$first_two_digits;
                        $sp_start_time =  $sp_start_time.':00'.':00';
                    } else {
                        $sp_start_time = 0;
                    }
                }

                if ($row['template_id'] == 8) {
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
                        '" . $sp_start_time . "',
                        NULL,
                        '" . $created . "',
                        '" . $created . "'
                        )";
                    if ($new_conn->query($insert_sql) === TRUE) {
                        echo $row['user_id'] . ' ' . 'Added</br>';
                    } else {
                        //echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
                    }
                }

                $post_id = $row['advert_id'];
                //and favorite_type='forum'
                $ro_favorites_sql = "SELECT * FROM ro_favorites where internal_id=$post_id";
                $ro_favorites_sql_result = mysqli_query($old_conn, $ro_favorites_sql);

                if (mysqli_num_rows($ro_favorites_sql_result) > 0) {
                    while ($favorite = mysqli_fetch_assoc($ro_favorites_sql_result)) {
                        //For likes
                        //1=cms
                        //2=forum
                        //3=market
                        $like_create = date('Y-m-d', $favorite['created']);

                        $likes_insert_sql = "INSERT INTO forum_post_likes (
                    `forum_post_id`,
                    `user_id`,
                    `profile_id`,
                    `module_id`,
                    `created_at`,
                    `updated_at`
                    )
                VALUES (
                    '" . $row['advert_id'] . "',
                    '" . $favorite['user_id'] . "',
                    '" . $favorite['user_id'] . "',
                    3,
                    '" . $like_create . "',
                    '" . $like_create . "'
                    )";
                        if ($new_conn->query($likes_insert_sql) === TRUE) {
                            //echo $row['user_id'] . ' ' . 'Added</br>';
                        }
                    }
                }

                $ro_comment_sql = "SELECT * FROM ro_advert_comment where advert_id=$post_id";
                $ro_comment_sql_result = mysqli_query($old_conn, $ro_comment_sql);

                if (mysqli_num_rows($ro_comment_sql_result) > 0) {
                    while ($comment = mysqli_fetch_assoc($ro_comment_sql_result)) {
                        $lang_idAr = [];
                        $city_idAr = [];
                        $comment_create = date('Y-m-d', $comment['created']);

                        if ($comment['city_id'] == 'zuerich') {
                            $city_id = 2;
                            $lang_id = 3;
                        } else if ($comment['city_id'] == 'zurich_en') {
                            $city_id = 1;
                            $lang_id = 1;
                        } else if ($comment['city_id'] == 'lausanne' || $comment['city_id'] == 'geneve') {
                            $city_id = 3;
                            $lang_id = 2; // Con
                        } else if ($comment['city_id'] == 'basel') {
                            $city_id = 4;
                            $lang_id = 3;
                        } else if ($comment['city_id'] == 'bern') {
                            $city_id = 5;
                            $lang_id = 3;
                        } else if ($comment['city_id'] == 'luzern') {
                            $city_id = 6;
                            $lang_id = 3;
                        } else if ($comment['city_id'] == 'st_gallen') {
                            $city_id = 7;
                            $lang_id = 3;
                        } else if ($comment['city_id'] == 'winterthur') {
                            $city_id = 8;
                            $lang_id = 3;
                        } else if ($comment['city_id'] == 'family') {
                            $city_id = 9;
                            $lang_id = 3;
                        } else {
                            $city_id = 0; //No city
                        }
                        if ($city_id != 0) {

                            $lang_idAr[] = strval($lang_id);
                            $city_idAr[] = strval($city_id);

                            $lang_id = json_encode($lang_idAr, true);
                            $city_id = json_encode($city_idAr, true);

                            $comments_insert_sql = "INSERT INTO forum_post_comments (
                    `post_id`,
                    `content_type`,
                    `discussion_type`,
                    `survey_type`,
                    `comment_id`,
                    `image`,
                    `address`,
                    `user_id`,
                    `profile_id`,
                    `total_likes`,
                    `total_dislikes`,
                    `city_id`,
                    `lang_id`,
                    `status`,
                    `module_id`,
                    `links`,
                    `created_at`,
                    `updated_at`
                    )
                VALUES (
                    '" . $row['advert_id'] . "',
                    NULL,
                    '" . $discussion_type . "',
                    NULL,
                    '" . $comment['comment_id'] . "',
                    NULL,
                    NULL,
                    '" . $comment['user_id'] . "',
                    '" . $comment['profile_id'] . "',
                    0,
                    0,
                    '" . $city_id . "',
                    '" . $lang_id . "',
                    1,
                    3,
                    NULL,
                    '" . $comment_create . "',
                    '" . $comment_create . "'
                    )";
                            if ($new_conn->query($comments_insert_sql) === TRUE) {
                                //echo $row['user_id'] . ' ' . 'Added</br>';
                            } else {
                                //echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
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
