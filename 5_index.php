<?php
include("newdb_conn.php");
include("olddb_conn.php");

//LIMIT 200000 OFFSET 100000 647383 655462
//AND core_users.user_id > 171752 limit 100000
//where ro_users.city_id = 'zurich_en'
//AND core_users.user_id > 121575

$sql = "SELECT core_users.user_id,core_users.user_created,core_users.user_email,core_users.user_password,core_users.user_active,ro_users.city_id FROM core_users inner join ro_users on core_users.user_id = ro_users.user_id  AND core_users.user_id > 690317
 limit 100000";

$result = mysqli_query($old_conn, $sql);
//ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
ini_set('max_execution_time', '0'); // for infinite time of execution 
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {

        $status = 'Inactive';
        if ($row['user_active'] == "Y") {
            $status = 'Active';
        }

        $user_id = $row['user_id'];
        $created_at = date('Y-m-d H:i:s', $row['user_created']);

        if ($row['city_id'] == 'zuerich') {
            $city_id = 2;
        } else if ($row['city_id'] == 'zurich_en') {
            $city_id = 1;
        } else if ($row['city_id'] == 'lausanne' || $row['city_id'] == 'geneve' || $row['city_id'] == 'romandie') {
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

        // echo "<pre>";
        // print_r($row);exit;
            $i = 0;
            if ($city_id != 0) {
                // $sql3 = "SELECT profile_id FROM ro_user_profiles where user_id=$user_id ORDER BY ASC limit 1";
                // $profile_id = 0;
                $count = 0;
                // if ($result3 = mysqli_query($old_conn, $sql3)) {
                //   $row3 = mysqli_fetch_assoc($result3);

                //   $profile_id = $row3['profile_id'];
                $password = mysqli_real_escape_string($new_conn, $row['user_password']);

                $insert_sql = "INSERT INTO users (
            `id`,
            `email`, 
            `password`,
            `city_id`,
            `active_profile_id`,
            `default_profile_id`,
            `default_registered_profile`,
            `status`,
            `created_at`
            )
          VALUES (
            '" . $row['user_id'] . "',
            '" . $row['user_email'] . "',
            '" . $password . "',
            '" . $city_id . "',
            '" . $row['user_id'] . "',
            '" . $row['user_id'] . "',
            '" . $row['user_id'] . "',
            '" . $status . "',
            '" . $created_at . "'
            )";
                if ($new_conn->query($insert_sql) === TRUE) {
                    echo $row['user_id'] . ' ' . 'Added</br>';
                } else {
                    //echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
                }
            

            /*$sql1 ="select * from ro_profile_followers where user_id=$user_id";
            $result1 = mysqli_query($old_conn, $sql1);
            ini_set('max_execution_time', '0');
            if (mysqli_num_rows($result1) > 0) {
                while ($row1 = mysqli_fetch_assoc($result1)) {
                    $created = date('Y-m-d H:i:s', $row1['created']);
                    $follow_insert_sql = "INSERT INTO user_follows (
                    `user_id`, 
                    `profile_id`,
                    `follow_user_id`,
                    `follow_user_profile_id`,
                    `get_instant_email`,
                    `get_one_time_email`,
                    `send_notification`,
                    `created_at`,
                    `updated_at`
                    )
                    VALUES (
                    '" . $row1['user_id'] . "',
                    '" . $row1['profile_id'] . "',
                    '" . $row1['follower_id'] . "',
                    '" . $row1['follower_id'] . "',
                    0,
                    1,
                    1,
                    '" . $created . "',
                    '" . $created . "'
                    )";
                    if ($new_conn->query($follow_insert_sql) === TRUE) {
                        //echo $row['user_id'] . ' ' . 'Added</br>';
                    } else {
                        //echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
                    }
                }
            } */
            //}
        } else {
            $i++;
        }
    }
    echo $i;
} else {
    echo "0 results found";
}
