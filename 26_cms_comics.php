<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM ro_comics";
$result = mysqli_query($old_conn, $sql);
//ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
ini_set('max_execution_time', '0'); // for infinite time of execution 
if (mysqli_fetch_array($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {

    /*
    echo '<pre>';
    print_r($row);
    echo '</pre>';
    */

    $comic_title = mysqli_real_escape_string($new_conn, $row['comic_title']);
    // $user_firstname = mysqli_real_escape_string($new_conn, $row['user_firstname']);
    // $user_lastname = mysqli_real_escape_string($new_conn, $row['sort_name']);

    // $show_name = 0;
    // if($row['show_nickname']=="Y"){
    //   $show_name = 1;
    // }

    $insert_sql = "INSERT INTO cms_comics (
      `title`, 
      `description`,
      `created_by`,
      `created_at`
      )
    VALUES (
      '" . $comic_title . "',
      '" . $row['comic_description'] . "', 
      0,
      '" . date('Y-m-d H:i:s', $row['created']) . "'
      )";


    if ($new_conn->query($insert_sql) === TRUE) {
      echo $comic_title . ' ' . 'Added</br>';
    } else {
      //echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
    }
  }
} else {
  echo "0 results found";
}
