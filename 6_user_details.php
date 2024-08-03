<?php
include("newdb_conn.php");
include("olddb_conn.php");

//where profiles.user_id > 352355
//349184
$sql = "SELECT * FROM ro_user_profiles AS profiles 
JOIN core_users AS users ON profiles.user_id = users.user_id where users.user_id = 2";
$result = mysqli_query($old_conn, $sql);

ini_set('max_execution_time', '0');
if (mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    echo "<pre>";
    var_dump($row );exit;
    $user_id = $row["user_id"];
    $profile_id = $row['profile_id'];

    if ($user_id) {

      $nickname = mysqli_real_escape_string($new_conn, $row['nickname']);
      $user_firstname = mysqli_real_escape_string($new_conn, $row['user_firstname']);
      $user_surname = mysqli_real_escape_string($new_conn, $row['user_lastname']);
      $user_profile_type = mysqli_real_escape_string($new_conn, $row['profile_type']);

      $short_description = !empty($row['about_me']) ?  mysqli_real_escape_string($new_conn, $row['about_me']) : NULL;

      $location = !empty($row['profile_location']) ? mysqli_real_escape_string($new_conn, $row['profile_location']) : NULL;
      $latitude = !empty($row['google_lat']) ? mysqli_real_escape_string($new_conn, $row['google_lat'])  : NULL;
      $longitude = !empty($row['google_lng']) ? mysqli_real_escape_string($new_conn, $row['google_lng'])  : NULL;
      $site = !empty($row['site']) ? mysqli_real_escape_string($new_conn, $row['site']) : "";
      // $unserializedArray = unserialize($serializedString);
      // $jsonString = json_encode($unserializedArray);
      $gender = $row['user_sex'];

      if ('company' == $user_profile_type) {
        $user_profile_type = 'Company';
      } elseif ('person' == $user_profile_type) {
        $user_profile_type = 'Individual';
      } else {
        $user_profile_type = NULL;
      }

      if ('m' == $gender) {
        $gender = 'Male';
      } elseif ('f' == $gender) {
        $gender = 'Female';
      } else {
        $gender = 'Others';
      }

      $birthday_query = "SELECT birthday, city_id FROM ro_users WHERE user_id='$user_id'";
      $birthday_query = mysqli_query($old_conn, $birthday_query);
      $birthday_result = mysqli_fetch_assoc($birthday_query);
      $birthday = NULL;
      if ($birthday_result['birthday'] != "0000-00-00") {
        $birthday = isset($birthday_result['birthday']) ? $birthday_result['birthday'] : '';
      }
      $dob = date('Y-m-d', strtotime($birthday));

      $created_at = date('Y-m-d H:i:s', $row['user_created']);

      if ($birthday_result['city_id'] == 'zuerich') {
        $city_id = 2;
      } else if ($birthday_result['city_id'] == 'zurich_en') {
        $city_id = 1;
      } else if ($birthday_result['city_id'] == 'lausanne' || $birthday_result['city_id'] == 'geneve') {
        $city_id = 3;
      } else if ($birthday_result['city_id'] == 'basel') {
        $city_id = 4;
      } else if ($birthday_result['city_id'] == 'bern') {
        $city_id = 5;
      } else if ($birthday_result['city_id'] == 'luzern') {
        $city_id = 6;
      } else if ($birthday_result['city_id'] == 'st_gallen') {
        $city_id = 7;
      } else if ($birthday_result['city_id'] == 'winterthur') {
        $city_id = 8;
      } else if ($birthday_result['city_id'] == 'family') {
        $city_id = 9;
      } else {
        $city_id = 0;
      }

      if ($city_id != 0) {
        $insert_sql = "INSERT INTO user_details (
      `id`,
      `user_id`,
      `profile_id`,
      `account_type`,
      `company_name`,
      `first_name`,
      `sur_name`,
      `nickname`,
      `nickname_for_city`,
      `gender`,
      `profile_img`,
      `cover_img`,
      `album_imgs`,
      `album_videos`,
      `short_description`,
      `description`,
      `pronoun`,
      `dob`,
      `links`,
      `location`,
      `company_locations`,
      `newsletter_city`,
      `reason`,
      `deleted_at`,
      `deleted_by_profile_id`,
      `created_at`, 
      `updated_at`,
      `industry`
  )
      VALUES (
      NULL,
      '" . $user_id . "', 
      '" . $profile_id . "', 
      '" . $user_profile_type . "', 
      NULL,
      '" . $user_firstname . "', 
      '" . $user_surname . "', 
      '" . $nickname . "', 
      0, 
      '" . $gender . "', 
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      '" . $dob  . "',
      NULL, 
      NULL, 
      NULL,
      NULL,
      NULL,
      NULL,
      0,
      '" . $created_at . "',
      '" . $created_at . "',
      NULL
      )";
        if ($new_conn->query($insert_sql) === TRUE) {
          echo $row['nickname'] . ' ' . 'Added</br>';
          $update_sql = "UPDATE users SET completed_profile = 1";
          mysqli_query($new_conn, $update_sql);
        } else {
          echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
        }
      }
    }
  }
} else {
  echo "0 results found";
}
