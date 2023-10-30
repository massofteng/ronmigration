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

        $insert_sql = "INSERT INTO users (
            `name`,
            `email`,
            `password`,
            `active_profile_id`,
            `status`,
            `created_at`,
            `updated_at`
        ) VALUES (
            '" . $user_name . "',
            '" . $user_email . "',
            '" . $user_password . "',
            '" . $user_active_profile_id . "',
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
