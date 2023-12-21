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

    $insert_sql = "INSERT INTO uploads (`id`,`user_id`, `profile_id`, `file_name`,`file_original_name`,`extension`,`file_size`,`city_id`,`type`, `relation`,`created_at`, `updated_at`)
    VALUES ('".$row['media_id']."', '".$row['owner_id']."', 1 ,'".$media_name."','".$media_name."','".$file_extension."',1,1,'image','".$row['is_public']."', '". date('Y-m-d H:i:s', $row['created'])."', '". date('Y-m-d H:i:s', $row['created'])."')";

    if ($new_conn->query($insert_sql) === TRUE) {
       echo $media_name. ' '. 'Added</br>';
    } else {
        //echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
    }
  }
} else {
  echo "0 results found";
}
