<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM ro_tags";
$result = mysqli_query($old_conn, $sql);
//ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
ini_set('max_execution_time', '0'); // for infinite time of execution 
if ($result->num_rows > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    $tag_name = mysqli_real_escape_string($old_conn, $row['tag_word']);
    $created_by = 1;
    $insert_sql = "INSERT INTO core_tags (
      `name`,
      `tag_module`,
      `community_category_id`,
      `ron_tips_category_id`,
      `community_city_id`,
      `rons_tips_city_id`,
      `created_by`,
      `created_at`,
      `updated_at`
      )
    VALUES (
      '" . $tag_name . "',
      '[]',
      '[]',
      '[]',
      '[]',
      '[]',
      '" . $created_by . "',
      '" . date("Y:m:d H:i:s") . "', 
      '" . date("Y:m:d H:i:s") . "' 
      )";


    if ($new_conn->query($insert_sql) === TRUE) {
      echo $tag_name . ' ' . 'Added</br>';
    } else {
      //echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
    }
  }

} else {
  echo "0 results found";
}
