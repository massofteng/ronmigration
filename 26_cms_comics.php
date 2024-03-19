<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM ro_comics";
$result = mysqli_query($old_conn, $sql);
ini_set('max_execution_time', '0');
if (mysqli_fetch_array($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {

    $insert_sql = "INSERT INTO cms_comics ( 
      `id`, 
      `title`,
      `description`,
      `created_by`,
      `created_at`, 
      `updated_at`
      )
    VALUES (
      '" . $row['comic_id'] . "', 
      '" . $row['comic_title'] . "', 
      '" . $row['comic_description'] . "', 
      1,
      '".date('Y-m-d H:i:s', $row['created'])."',
      '".date('Y-m-d H:i:s', $row['created'])."'
      )";


    if ($new_conn->query($insert_sql) === TRUE) {
        echo $row['title']. ' '. 'Added</br>';
    } else {
      //echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
    }
  }
} else {
  echo "0 results found";
}
