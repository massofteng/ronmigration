<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM ro_forum_poll_questions";
$result = mysqli_query($old_conn, $sql);

//ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
ini_set('max_execution_time', '0'); // for infinite time of execution 
if ($result->num_rows > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    $community_discussion_id = mysqli_real_escape_string($new_conn, $row['topic_id']);
    $title = mysqli_real_escape_string($new_conn, $row['question_title']);
    $description = mysqli_real_escape_string($new_conn, $row['question_text']);

    $insert_sql = "INSERT INTO community_discussion_survey_questions (
  `com_dis_id`,
  `title`,
  `description`,
  `created_at`, 
  `updated_at`
  )
VALUES (
  '" . $community_discussion_id . "', 
  '" . $title . "', 
  '" . $description . "',
  '" . date("Y:m:d H:i:s") . "',
  '" . date("Y:m:d H:i:s") . "'
  )";



    if ($new_conn->query($insert_sql) === TRUE) {
      echo $row['question_title'] . ' ' . 'Added</br>';
    } else {
      //echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
    }
  }
} else {
  echo "0 results found";
}
