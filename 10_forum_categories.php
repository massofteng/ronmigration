<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM ro_categories WHERE target_page ='all'";
$result = mysqli_query($old_conn, $sql);
//ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
ini_set('max_execution_time', '0'); // for infinite time of execution 
if ($result->num_rows > 0) {
    while ($row = mysqli_fetch_assoc($result)) {

        $category_title = mysqli_real_escape_string($new_conn, $row['category_key']);
        $parent_id = $row['parent_id'];
        if (!empty($parent_id)) {
            $parent_name_sql = "SELECT `category_key` FROM ro_categories WHERE category_id='$parent_id'";
            $parent_name_query = mysqli_query($old_conn, $sql);
            $parent_name = mysqli_fetch_assoc($parent_name_query);
            $parent_name = $parent_name['category_key'];
        } else {
            $parent_name = '';
        }

        // convert city names to id.
        $category_id = $row['category_id'];
        $city_id_query = "SELECT `city_id` FROM ro_categories_cities WHERE category_id='$category_id'";
        $city_id_sql = mysqli_query($old_conn, $city_id_query);
        $city_names = array();
        if ($city_id_sql->num_rows > 0) {
            while ($row = mysqli_fetch_assoc($city_id_sql)) {
                $city_names[] = $row['city_id'];
            }
        }

        $city_id = array();
        if (is_array($city_names) && count($city_names)) {
            foreach ($city_names as $name) {
                if (!empty($name)) {
                    switch ($name) {
                        case 'zurich_en':
                            $city_id[] = 1;
                            break;
                        case 'zuerich':
                            $city_id[] = 2;
                            break;
                        case 'geneve':
                            $city_id[] = 3;
                            break;
                        case 'lausanne':
                            $city_id[] = 4;
                            break;
                        case 'basel':
                            $city_id[] = 5;
                            break;
                        case 'bern':
                            $city_id[] = 6;
                            break;
                        case 'luzern':
                            $city_id[] = 7;
                            break;
                        case 'st_gallen':
                            $city_id[] = 8;
                            break;
                        case 'winterthur':
                            $city_id[] = 9;
                            break;
                        case 'winterthur':
                            $city_id[] = 10;
                            break;
                        case 'family':
                            $city_id[] = 11;
                            break;
                        default:
                            break;
                    }
                }
            }
        }

        $city_ids = !empty($city_id) ? json_encode($city_id) : 0;

        // $city_id_query = "SELECT `ro_categories`"
        // $city_id = $row['city_id'];
        $created_by = 1;
        $deleted_by = 0;

        $insert_sql = "INSERT INTO forum_categories (
      `parent_id`,
      `parent_name`, 
      `name`,
      `city_id`,
      `created_by`,
      `deleted_by`,
      `created_at`,
      `updated_at`
      )
    VALUES (
      '" . $parent_id . "', 
      '" . $parent_name . "', 
      '" . $category_title . "', 
      '" . $city_ids . "',
      '" . $created_by . "',
      '" . $deleted_by . "',
      '" . date("Y:m:d H:i:s") . "', 
      '" . date("Y:m:d H:i:s") . "' 
      )";


        if ($new_conn->query($insert_sql) === TRUE) {
            echo $category_title . ' ' . 'Added</br>';
        } else {
            //echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
        }
    }
} else {
    echo "0 results found";
}
