<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM media_library";
$result = mysqli_query($old_conn, $sql);

ini_set('max_execution_time', '0');
if (mysqli_fetch_array($result) > 0) {
    //print_r($result);
  while($row = mysqli_fetch_assoc($result)) {

    // echo 'salam';
    // print_r($row['media_name']);exit;

    $file_extension = '';
    if($row['media_name']){
        $file_extension = substr($row['media_name'], strpos($row['media_name'], ".") + 1);  
        if(strlen($file_extension) > 9){
            $file_extension = 'jpg';
        }
    }  

    $media_name = mysqli_real_escape_string($new_conn, $row['media_name']);

    $user_id = $row['owner_id'];
    $sql2 = "SELECT city_id FROM ro_users where user_id=$user_id";

    if ($result2 = mysqli_query($old_conn, $sql2)) {
       $row2 = mysqli_fetch_assoc($result2);
        if($row2['city_id'] =='zuerich'){
          $city_id = 2;
        }else if($row2['city_id']=='zurich_en'){
          $city_id = 1;
        }else if($row2['city_id']=='lausanne'){
          $city_id = 3;
        }else if($row2['city_id']=='luzern'){
          $city_id = 6;
        }else if($row2['city_id']=='st_gallen'){
          $city_id = 7;
        }else if($row2['city_id']=='winterthur'){
          $city_id = 8;
        }else{
          $city_id = 99; //No city
        }
    }

    $sql3 = "SELECT profile_id FROM ro_user_profiles where user_id=$user_id and is_current ='Y' limit 1";
    $profile_id = 0;
    if ($result3 = mysqli_query($old_conn, $sql3)) {
      while ($row3 = mysqli_fetch_row($result3)) {
        $profile_id = $row3[0];
      }
    } 

    if( $city_id!=99){
      $insert_sql = "INSERT INTO uploads (
        `id`,
        `user_id`, 
        `profile_id`, 
        `file_name`,
        `file_original_name`,
        `extension`,
        `file_size`,
        `city_id`,
        `type`, 
        `relation`
        )
      VALUES (
        '".$row['media_id']."',
        '".$user_id."', 
        '".$profile_id."',
        '".$media_name."',
        '".$media_name."',
        '".$file_extension."',
        1,
        '".$city_id."',
         'image',
         '".$row['is_public']."'
         )";

      if ($new_conn->query($insert_sql) === TRUE) {
        echo $media_name. ' '. 'Added</br>';
      } else {
          //echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
      }
    }
  }
} else {
  echo "0 results found";
}
