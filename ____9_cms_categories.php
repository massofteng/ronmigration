<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM ro_categories WHERE target_page ='stadtleben' AND parent_id=0";
$result = mysqli_query($old_conn, $sql);
ini_set('max_execution_time', '0');
if ($result->num_rows > 0) {
    while ($row = mysqli_fetch_assoc($result)) {

        $parent_category_key = $row['category_key'];
        $parent_category_id = $row['category_id'];

        $parent_category_name = "select translation from core_translations WHERE lang_key='$parent_category_key' AND lang='en'";
        $parent_name_query = mysqli_query($old_conn, $parent_category_name);
        $categoryNames = mysqli_fetch_assoc($parent_name_query);

        $childs = "SELECT category_id, category_key FROM ro_categories WHERE target_page ='stadtleben' AND parent_id='. $parent_category_id.'";
        $childs = mysqli_query($old_conn, $childs);
        $ChildsCategoryArr = mysqli_fetch_row($childs);


        $current_date = date("Y:m:d H:i:s");
        $created_by = 1;
        $deleted_by = 0;
        $city_ids = ["1", "2", "3", "4", "5", "6", "7", "8", "9"];

        foreach ($categoryNames as $cat_name) {
            $city_ids = json_encode($city_ids);
            $insert_sql = "INSERT INTO cms_categories (
            `id`,
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
                '$parent_category_id', 
                 NULL, 
                'self', 
                '$cat_name', 
                '$city_ids',
                '$created_by',
                '$deleted_by',
                '$current_date', 
                '$current_date'
            )";
                     
            if ($new_conn->query($insert_sql) === TRUE) {

                foreach ($ChildsCategoryArr as $child_key) {

                    $child_category_id = $child_key[0];
                    $child_category_key = $child_key[1];
                   
                    $child_name_string = "select translation from core_translations WHERE lang_key='$child_category_key' AND lang='en'";
                    $child_name_arr = mysqli_query($old_conn, $child_name_string);
                    $childs = mysqli_fetch_assoc($child_name_arr);
                    $child_name = "";
                    if($childs){
                        $child_name = $childs["translation"];
                    }
                   

            // echo "<pre>";
            // print_r($parent_name['translation']);
            // exit;

                    $insert_child_sql = "INSERT INTO cms_categories (
                        `id`,
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
                            '$child_category_id', 
                            '$parent_category_id', 
                            '$cat_name',
                            '$child_name', 
                            '$city_ids',
                            '$created_by',
                            '$deleted_by',
                            '$current_date', 
                            '$current_date'
                        )";
                    $new_conn->query($insert_child_sql) === TRUE;
                }
            }
        }
    }
} else {
    echo "0 results found";
}
