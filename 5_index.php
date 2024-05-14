<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM core_users";
$result = mysqli_query($old_conn, $sql);
//ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
ini_set('max_execution_time', '0'); // for infinite time of execution 
if (mysqli_fetch_array($result) > 0) {
  while($row = mysqli_fetch_assoc($result)) {

    $status = 'Inactive';
    if($row['user_active']=="Y"){
        $status = 'Active';
    }

    $user_id = $row['user_id'];
    $created_at = date('Y-m-d H:i:s', $row['user_created']);

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

    if( $city_id!=99){
      $sql3 = "SELECT profile_id FROM ro_user_profiles where user_id=$user_id";
      $profile_id = 0;
      if ($result3 = mysqli_query($old_conn, $sql3)) {
        while ($row3 = mysqli_fetch_row($result3)) {
          $profile_id = $row3[0];
          
          $insert_sql = "INSERT INTO users (
            `id`,
            `name`, 
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
            '".$row['user_id']."',
            '".$row['user_firstname']."',
            '".$row['user_email']."',
            '".$row['user_password']."',
            '".$city_id."',
            '".$profile_id."',
            '".$profile_id."',
            '".$profile_id."',
            '".$status."',
            '".$created_at."'
            )";
          if ($new_conn->query($insert_sql) === TRUE) {
              echo $row['user_firstname']. ' '. 'Added</br>';
          } else {
              //echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
          }
       }
     } 
    }
  }
} else {
  echo "0 results found";
}
?>
