<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM ro_categories WHERE target_page ='all'";
$result = mysqli_query($old_conn, $sql);
ini_set('max_execution_time', '0');
if ($result->num_rows > 0) {
    while ($row = mysqli_fetch_assoc($result)) {

        $category_key = mysqli_real_escape_string($new_conn, $row['category_key']);
        $parent_id = $row['parent_id'];

        $parent_category_name = "select translation from core_translations WHERE lang_key='$category_key' AND lang='en'";
        $parent_name_query = mysqli_query($old_conn, $parent_category_name);
        $categoryNames = mysqli_fetch_assoc($parent_name_query);
        $parent_name = $categoryNames['translation'];

        $category_id = $row['category_id'];
        $city_id_query = "SELECT `city_id`,'lang' FROM ro_categories_cities WHERE category_id='$category_id'";
        $city_id_sql = mysqli_query($old_conn, $city_id_query);
        $city_names = array();
        $lang_id = 3;
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

        $city_ids = !empty($city_id) ? json_encode(array_unique($city_id)) : 0;

        //var_dump( $city_ids);exit;

        $created_by = 1;
        $deleted_by = 0;

        $insert_sql = "INSERT INTO forum_categories (
            `parent_id`,
            `parent_name`, 
            `name`,
            `city_id`,
            `lang_id`,
            `created_by`,
            `deleted_by`,
            `created_at`,
            `updated_at`
            )
            VALUES (
            '" . $parent_id . "', 
            '" . $parent_name . "', 
            '" . $parent_name . "', 
            '" . $city_ids . "',
            '" . $lang_id . "',
            '" . $created_by . "',
            '" . $deleted_by . "',
            '" . date("Y:m:d H:i:s") . "', 
            '" . date("Y:m:d H:i:s") . "' 
            )";

        if ($new_conn->query($insert_sql) === TRUE) {
            echo $parent_name . ' ' . 'Added</br>';
        } else {
            //echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
        }
    }
} else {
    echo "0 results found";
}
