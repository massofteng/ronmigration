<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM ro_user_profiles AS profiles LEFT JOIN core_users AS users ON profiles.user_id = users.user_id WHERE 1=1";
$result = mysqli_query($old_conn, $sql);
//ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
ini_set('max_execution_time', '0'); // for infinite time of execution 
if (mysqli_fetch_array($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    // echo '<pre>';
    // print_r($row);
    // echo '</pre>';
    $user_id = $row["user_id"];
    $nickname = mysqli_real_escape_string($new_conn, $row['nickname']);
    $user_firstname = mysqli_real_escape_string($new_conn, $row['user_firstname']);
    $user_surname = mysqli_real_escape_string($new_conn, $row['user_lastname']);
    $user_profile_type = mysqli_real_escape_string($new_conn, $row['profile_type']);

    $short_description = !empty($row['about_me']) ? "'" . mysqli_real_escape_string($new_conn, $row['about_me']) . "'" : "NULL";

    $location = !empty($row['profile_location']) ? "'" . mysqli_real_escape_string($new_conn, $row['profile_location']) . "'" : "NULL";
    $latitude = !empty($row['google_lat']) ? "'" . mysqli_real_escape_string($new_conn, $row['google_lat']) . "'" : "NULL";
    $longitude = !empty($row['google_lng']) ? "'" . mysqli_real_escape_string($new_conn, $row['google_lng']) . "'" : "NULL";
    $language = !empty($row['ron_profile_lang']) ? "'" . mysqli_real_escape_string($new_conn, $row['ron_profile_lang']) . "'" : "NULL";
    $site = !empty($row['site']) ? "'" . mysqli_real_escape_string($new_conn, $row['site']) . "'" : "NULL";
    $gender = $row['user_sex'];

    /**
     * Format user profile type.
     */
    if ('company' == $user_profile_type) {
      $user_profile_type = 'Company';
    } elseif ('person' == $user_profile_type) {
      $user_profile_type = 'Individual';
    }

    /**
     * Format user gender
     */
    if ('m' == $gender) {
      $gender = 'Male';
    } elseif ('f' == $gender) {
      $gender = 'Female';
    }

    /**
     * Birthday 
     */
    $birthday_query = "SELECT birthday FROM ro_users WHERE user_id='$user_id'";
    $birthday_query = mysqli_query($old_conn, $birthday_query);
    $birthday_result = mysqli_fetch_assoc($birthday_query);
    $birthday = isset($birthday_result['birthday']) ? $birthday_result['birthday'] : '';

    $insert_sql = "INSERT INTO user_details (
  `user_id`,
  `profile_id`,
  `account_type`,
  `company_name`,
  `first_name`,
  `sur_name`,
  `nickname`,
  `nickname_for_city`,
  `gender`,
  `short_description`,
  `language`,
  `dob`,
  `location`,
  `links`,
  `created_at`, 
  `updated_at`
  )
VALUES (
  '" . $row['user_id'] . "', 
  '" . $row['profile_id'] . "', 
  '" . $user_profile_type . "', 
  'NULL',
  '" . $user_firstname . "', 
  '" . $user_surname . "', 
  '" . $nickname . "', 
  0, 
  '" . $gender . "', 
  " . $short_description . ", 
  " . $language . ",
  '" . date('Y-m-d H:i:s', strtotime($birthday)) . "',
  " . $location . ", 
  " . $site . ", 
  '" . date('Y-m-d H:i:s', $row['created']) . "',
  '" . date('Y-m-d H:i:s', $row['created']) . "'
  )";
    if ($new_conn->query($insert_sql) === TRUE) {
      echo $row['nickname'] . ' ' . 'Added</br>';
    } else {
      //echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
    }
  }
} else {
  echo "0 results found";
}
