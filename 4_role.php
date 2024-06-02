<?php
include("newdb_conn.php");
include("olddb_conn.php");

ini_set('max_execution_time', '0');

$sql = "
SELECT
    TU.user_id,
    GROUP_CONCAT(TG.group_id) AS `role_ids`,
    GROUP_CONCAT(TG.group_name) AS `role_names`
FROM
    core_users TU
JOIN (
    SELECT TGU.user_id, TGU.group_id, TUG.group_name
    FROM core_group_users TGU
    JOIN core_usergroups TUG USING (group_id)
) TG ON TU.user_id = TG.user_id
GROUP BY TU.user_id";

$result = mysqli_query($old_conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {

        $role_ids = [];
        $user_id = $row['user_id'];
        $role_ids =  explode(",", $row['role_ids']);
        $profile_ids = [];

        $sql2 = "SELECT profile_id FROM ro_user_profiles WHERE user_id=$user_id";
        $result2 = mysqli_query($old_conn, $sql2);

        if ($result2) {
            while ($row2 = mysqli_fetch_assoc($result2)) {
                $profile_ids[] = $row2['profile_id'];
            }

            for ($i = 0; $i < count($profile_ids); $i++) {
                $model = 'App' . '\\' . 'Models' . '\\' . 'UserProfiles';
                $escaped_special_text = $new_conn->real_escape_string($model);
                if ($profile_ids[$i] && @$role_ids[$i]) {
                    switch ($role_ids[$i]) {
                        case 4:
                        case 45:
                            $role_id = 10;
                            break;
                        case 46:
                            $role_id = 4;
                            break;
                        case 53:
                            $role_id = 2;
                            break;
                        default:
                            $role_id = 11;
                            break;
                    }

                    // echo  $role_id;
                    // echo "<pre>";
                    // echo $profile_ids[$i];
                    // exit;

                    $insert_sql = "INSERT INTO model_has_roles (
                        `role_id`,
                        `model_type`, 
                        `model_id`
                        )
                      VALUES (
                        '" . $role_id . "', 
                        '". $escaped_special_text."',
                        '" . $profile_ids[$i] . "'
                        )";

                    if ($new_conn->query($insert_sql) === TRUE) {
                        echo $profile_ids[$i] . ' ' . 'Added</br>';
                    } else {
                        echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
                    }
                } else if ($profile_ids[$i] && empty($role_ids[$i])) {
                    $insert_sql = "INSERT INTO model_has_roles (
                            `role_id`,
                            `model_type`, 
                            `model_id`
                            )
                          VALUES (
                            11, 
                            '".$escaped_special_text."', 
                            '" . $profile_ids[$i] . "'
                            )";

                    if ($new_conn->query($insert_sql) === TRUE) {
                        echo $profile_ids[$i] . ' ' . 'Added</br>';
                    } else {
                        echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
                    }
                }
            }
        } else {
            echo "Failed to fetch profiles for user_id $user_id: " . mysqli_error($old_conn) . "<br>";
        }
    }
} else {
    echo "Failed to fetch users and roles: " . mysqli_error($old_conn) . "<br>";
}

