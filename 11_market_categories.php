<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM ro_categories where target_page='marktplatz'";
$result = mysqli_query($old_conn, $sql);
ini_set('max_execution_time', '0');
if (mysqli_fetch_array($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $category_key = mysqli_real_escape_string($new_conn, $row['category_key']);
        $parent_id = $row['parent_id'];
        $have_subcategory = 0;
        if (!empty($parent_id)) {
            $have_subcategory = 1;
        } else {
            $parent_id = $row['category_id'];
        }

        $parent_category_name = "select translation from core_translations WHERE lang_key='$category_key' AND lang='en'";
        $parent_name_query = mysqli_query($old_conn, $parent_category_name);
        $categoryNames = mysqli_fetch_assoc($parent_name_query);
        $category_name = $categoryNames['translation'];

        $searching_category_id = $row['category_id'];

        $city_id_query = "SELECT city_id, lang FROM ro_categories_cities WHERE category_id='$searching_category_id'";
        $city_id_sql = mysqli_query($old_conn, $city_id_query);
        $lang_names = array();
        $city_id = [];
        if ($city_id_sql->num_rows > 0) {
            while ($row = mysqli_fetch_assoc($city_id_sql)) {
                if (!empty($row['city_id'])) {
                    switch (strtolower($row['city_id'])) {
                        case 'zuerich':
                            $city_id[] = 2;
                            break;
                        case 'zurich_en':
                            $city_id[] = 1;
                            break;
                        case 'lausanne':
                        case 'geneve':
                            $city_id[] = 3;
                            break;
                        case 'basel':
                            $city_id[] = 4;
                            break;
                        case 'bern':
                            $city_id[] = 5;
                            break;
                        case 'luzern':
                            $city_id[] = 6;
                            break;
                        case 'st_gallen':
                            $city_id[] = 7;
                            break;
                        case 'winterthur':
                            $city_id[] = 8;
                            break;
                        case 'family':
                            $city_id[] = 9;
                            break;
                        default:
                            break;
                    }
                }
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
            }
        }

        $lang_ids = !empty($lang_id) ? json_encode($lang_id) : 0;
        if (empty($lang_ids)) {
            $category_language_query = "SELECT lang FROM ro_categories_categories WHERE category_id='$searching_category_id'";
            $category_language_query = mysqli_query($old_conn, $category_language_query);
            if ($category_language_query->num_rows > 0) {
                $category_language = mysqli_fetch_assoc($category_language_query);
                $category_language = $category_language['lang'];
                if (!empty($category_language)) {
                    switch ($category_language) {
                        case 'en':
                            $lang_id[] = 1;
                            break;
                        case 'fr':
                            $lang_id[] = 2;
                            break;
                        case 'de':
                            $lang_id[] = 3;
                            break;
                        default:
                            $lang_id[] = 3;
                            break;
                    }
                }
            }
        }

        $lang_ids = !empty($lang_id) ? json_encode($lang_id) :  json_encode(3);
       // $lang_ids = 3;

        // echo '<pre>';
        // print_r($lang_ids);
        // echo '</pre>';
        // die();

        $created_by = 1;
        $deleted_by = 0;

        if (!empty($city_id)) {

            $insert_sql = "INSERT INTO market_categories (
                `parent_id`,
                `name`,
                `have_subcategory`,
                `lang_id`,
                `city_id`,
                `created_by`,
                `deleted_by`,
                `created_at`,
                `updated_at`
                )
                VALUES (
                '" . $parent_id . "',
                '" . $category_name . "', 
                '" . $have_subcategory . "', 
                '" . $lang_ids . "', 
                '" . json_encode(array_unique($city_id)) . "',
                '" . $created_by . "',
                '" . $deleted_by . "',
                '" . date("Y-m-d H:i:s") . "', 
                '" . date("Y-m-d H:i:s") . "' 
                )";



            if ($new_conn->query($insert_sql) === TRUE) {
                echo $category_name . ' ' . 'Added</br>';
            } else {
               
            }
        }
    }
} else {
    echo "0 results found";
}
