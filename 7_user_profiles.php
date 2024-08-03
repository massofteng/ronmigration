<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM ro_user_profiles";
$result = mysqli_query($old_conn, $sql);
ini_set('max_execution_time', '0');
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        //  echo '<pre>';
        //     print_r($row);
        //     echo '</pre>';exit;
        if ($row['created'] == "1970-01-01 00:00:00") {
            $created = date('Y-m-d H:i:s');
            $updated = date('Y-m-d H:i:s');
        } else {
            $created = date('Y-m-d H:i:s', $row['created']);
            $updated = date('Y-m-d H:i:s', $row['created']);
        }
        $user_id = $row["user_id"];
        $city_query = "SELECT  city_id FROM ro_users WHERE user_id='$user_id'";
        $city_query = mysqli_query($old_conn, $city_query);
        $city_result = mysqli_fetch_assoc($city_query);

        if ($city_result['city_id'] == 'zuerich') {
            $city_id = 2;
        } else if ($city_result['city_id'] == 'zurich_en') {
            $city_id = 1;
        } else if ($city_result['city_id'] == 'lausanne' || $city_result['city_id'] == 'geneve') {
            $city_id = 3;
        } else if ($city_result['city_id'] == 'basel') {
            $city_id = 4;
        } else if ($city_result['city_id'] == 'bern') {
            $city_id = 5;
        } else if ($city_result['city_id'] == 'luzern') {
            $city_id = 6;
        } else if ($city_result['city_id'] == 'st_gallen') {
            $city_id = 7;
        } else if ($city_result['city_id'] == 'winterthur') {
            $city_id = 8;
        } else if ($city_result['city_id'] == 'family') {
            $city_id = 9;
        } else {
            $city_id = 0;
        }

        if ($city_id != 0) {
            $insert_sql = "INSERT INTO user_profiles (
                `id`,
                `user_id`,
                `status`,
                `delete_reason`,
                `created_at`, 
                `updated_at`,
                `deleted_at`,
                `deleted_by_profile_id`
                )
                VALUES (
                '" . $row['profile_id'] . "', 
                '" . $row['user_id'] . "', 
                'Active',
                NULL,
                '" . $created . "',
                '" . $updated . "',
                NULL,
                0
                )";
            if ($new_conn->query($insert_sql) === TRUE) {
                echo $row['nickname'] . ' ' . 'Added</br>';
            } else {
                //echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
            }
        }
    }
} else {
    echo "0 results found";
}
