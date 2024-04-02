<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM ro_profile_love";
$result = mysqli_query($old_conn, $sql);
ini_set('max_execution_time', '0');
if ($result->num_rows > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    // echo '<pre>';
    // print_r($row);
    // echo '</pre>';
    // exit;

    if($row['sp_love_sex']=='m'){
        $gender = 1;
    }else{
        $gender = 2; 
    }
    if($row['search_target']=='m'){
        $search_target = 1;
    }else{
        $search_target = 2; 
    }

    $age = $row['sp_love_age'] ? $row['sp_love_age'] : 0 ;

    $insert_sql = "INSERT INTO forum_love_registrations (
        `profile_id`,
        `user_id`,
        `city_id`,
        `lang_id`,
        `status`, 
        `looking_for`,
        `interested_in`,
        `gender`,
        `age`,
        `height`,
        `profile_picture`,
        `default_picture`,
        `body_figure`,
        `star_sign`
    )
    VALUES (
        '" . $row['profile_id'] . "', 
        '" . $row['user_id'] . "', 
        1,
        1,
        1,
        '" . $search_target . "',
        '" . json_encode($row['search_type']) . "',
        '" . $gender . "',
        '" . $age . "',
        '" . $row['sp_love_height'] . "',
        '" . 'a.jpg' . "',
        '" . 'b.jpg' . "',
        '" . $row['sp_love_constitution'] . "',
        '" . $row['sp_love_zodiac'] . "'
    )";


    if ($new_conn->query($insert_sql) === TRUE) {
      echo $row['user_id'] . ' ' . 'Added</br>';
    } else {
      echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
    }
  }
} else {
  echo "0 results found";
}
