<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM core_tags";
$result = mysqli_query($new_conn, $sql);
//ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
ini_set('max_execution_time', '0'); // for infinite time of execution 
if (mysqli_fetch_array($result) > 0) {
  while($row = mysqli_fetch_assoc($result)) {
    $tag_name = mysqli_real_escape_string($new_conn, $row['name']);
    $sql2 = "SELECT * FROM ro_tags where `tag_word` like '".$tag_name."'";
    $result2 = mysqli_query($old_conn, $sql2);

  

    if (mysqli_fetch_array($result2) > 0) {
  
        while($row2 = mysqli_fetch_assoc($result2)) {
            $desql = "DELETE FROM ro_tags WHERE tag_id='".$row2['tag_id']."'";
            if ($old_conn->query($desql) === TRUE) {
                echo 'ok<br\>';
            }
         }
    }
  }
} else {
  echo "0 results found";
}
?>