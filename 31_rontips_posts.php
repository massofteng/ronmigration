<?php
include("newdb_conn.php");
include("olddb_conn.php");
//and advert_id >=116150
$sql = "SELECT * FROM ro_advertisement where template_id=3";
$result = mysqli_query($old_conn, $sql);
ini_set('max_execution_time', '0');
$arrayCat = [];
if (mysqli_fetch_array($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        if (!empty($row['lang'])) {
            switch ($row['lang']) {
                case 'de':
                    $lang_id = 3;
                    break;
                case 'fr':
                    $lang_id = 2;
                    break;
                case 'en':
                    $lang_id = 1;
                    break;
                default:
                    $lang_id = 3;
                    break;
            }
        }

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

        $category_id = $row['category_id'];
        $sql2 = "SELECT post_type, category, sub_category FROM dummy_categories where category_id = $category_id";
        $discussion_type = '';
        $category = 0;
        $sub_category = 0;

        if (!array_key_exists($category_id, $arrayCat)) {
            $arrayCat[$category_id] = $category_id;
        }

        if ($result2 = mysqli_query($old_conn, $sql2)) {
            if (mysqli_num_rows($result2) > 0) {
                $row2 = mysqli_fetch_assoc($result2);
                $discussion_type = $row2['post_type'];
                $category = $row2['category'];
                $sub_category = $row2['sub_category'];
            }
        }

        $user_id = $row['user_id'];
        $subject = mysqli_real_escape_string($new_conn, $row['subject']);
        $description = mysqli_real_escape_string($new_conn, $row['message']);
        $created = date('Y-m-d', $row['created']);
        $publication_start = date('Y-m-d', $row['published']);
        $publication_end_date = date('Y-m-d', $row['expiration']);

        /*  if ($city_id != 0) {
            $insert_sql = "INSERT INTO cms_posts (   
                `id`,  
                `title`, 
                `category`,
                `short_description`, 
                `content_type`,
                `status`,
                `lang`,
                `city`,
                `start_date`,
                `end_date`,
                `nl_button_info`,    
                `created_at`,
                `created_by`,
                `creator_profile_id`
            )
            VALUES (
                '" . $row['advert_id'] . "',
                '" . $subject . "', 
                '" . $category . "', 
                '" . htmlentities($description) . "', 
                1,
                1, 
                '" . $lang_id . "', 
                '" . $city_id . "', 
                '" . $publication_start . "', 
                '" . $publication_end_date . "',  
                NULL,       
                '" . $created . "',    
                '" . $user_id . "',
                '" . $user_id . "'
            )";


            if ($new_conn->query($insert_sql) === TRUE) {
                echo $row['advert_id'] . ' Added</br>';
            } else {
                echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
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
                    '" . $lang_id. "',
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
        */
    }

    // echo "<pre>";
    // print_r($arrayCat);
    $catName = [];
    foreach ($arrayCat as $cat) {
        // Prepare the first SQL query
        $stmt1 = $old_conn->prepare("SELECT * FROM ro_categories WHERE category_id = ?");
        $stmt1->bind_param("i", $cat);
        $stmt1->execute();
        $ro_comment_sql_result = $stmt1->get_result();
    
        if ($ro_comment_sql_result->num_rows > 0) {
            $comment = $ro_comment_sql_result->fetch_assoc();
    
            $lang_key = $comment['category_key'];
    
            // Prepare the second SQL query
            $stmt2 = $old_conn->prepare("SELECT translation FROM core_translations WHERE lang_key = ? AND lang = 'en'");
            $stmt2->bind_param("s", $lang_key);
            $stmt2->execute();
            $ro_result = $stmt2->get_result();
    
            if ($ro_result->num_rows > 0) {
                $translation = $ro_result->fetch_assoc();
                $catName[$cat] = $translation['translation'];
            }
        }
    }
    echo "<pre>";
    print_r($catName);
    
} else {
    echo "0 results found";
}
