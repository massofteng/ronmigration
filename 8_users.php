<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM ro_user_profiles AS profiles LEFT JOIN core_users AS users ON profiles.user_id = users.user_id WHERE 1=1";
$result = mysqli_query($old_conn, $sql);

ini_set('max_execution_time', '0'); // for infinite time of execution 

if ($result->num_rows > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // echo '<pre>';
        // print_r($row);
        // echo '</pre>';
        $user_id = $row['user_id'];
        $user_name = mysqli_real_escape_string($new_conn, $row['user_firstname'] . $row['user_lastname']);
        $user_email = filter_var($row['user_email'], FILTER_SANITIZE_EMAIL);
        $user_status = $row['user_active'];
        if ('N' == $user_status) {
            $user_status = 'Inactive';
        } else if ('Y' == $user_status) {
            $user_status = 'Active';
        }
        $user_password = $row['user_password'];
        $user_active_profile_id = $row['profile_id'];
        $user_created_at = date("Y-m-d H:i:s", $row['user_created']);

        $city_id_query = "SELECT city_id FROM ro_users WHERE user_id='$user_id'";
        $city_id_result = mysqli_query($old_conn, $city_id_query);
        $city_name = mysqli_fetch_assoc($city_id_result);
        $city_name = $city_name['city_id'];
        $city_id = 0;
        if (!empty($city_name)) {
            switch ($name) {
                case 'zurich_en':
                  $city_id[] = 1;
                  break;
                case 'zuerich':
                  $city_id[] = 2;
                  break;
                // case 'geneve':
                //   $city_id[] = 3;
                //   break;
                case 'lausanne':
                  $city_id[] = 3;
                  break;
                // case 'basel':
                //   $city_id[] = 5;
                //   break;
                // case 'bern':
                //   $city_id[] = 6;
                //   break;
                case 'luzern':
                  $city_id[] = 6;
                  break;
                case 'st_gallen':
                  $city_id[] = 7;
                  break;
                // case 'winterthur':
                //   $city_id[] = 9;
                //   break;
                case 'winterthur':
                  $city_id[] = 8;
                  break;
                // case 'family':
                //   $city_id[] = 11;
                //   break;
                default:
                  break;
              }
        }

        $insert_sql = "INSERT INTO users (
            `name`,
            `email`,
            `password`,
            `city_id`,
            `active_profile_id`,
            `default_profile_id`,
            `status`,
            `created_at`,
            `updated_at`
        ) VALUES (
            '" . $user_name . "',
            '" . $user_email . "',
            '" . $user_password . "',
            '" . $user_active_profile_id . "',
            '" . $user_active_profile_id . "',
            '" . $city_id . "',
            '" . $user_status . "',
            '" . $user_created_at . "',
            '" . $user_created_at . "'
        )";

        if ($new_conn->query($insert_sql) === TRUE) {
            echo $user_name . ' ' . 'Added</br>';
        } else {
            //echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
        }
    }
} else {
    echo "0 results found";
}
