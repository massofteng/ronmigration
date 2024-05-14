<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM ro_forum_poll_answers";
$result = mysqli_query($old_conn, $sql);

//ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
ini_set('max_execution_time', '0'); // for infinite time of execution 
if ($result->num_rows > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    echo '<pre>'; 
    print_r($row);
    echo '</pre>';

    $answer_id = mysqli_real_escape_string($new_conn, $row['answer_id']);



    $community_discussion_id = mysqli_real_escape_string($new_conn, $row['topic_id']);
    $answer_value  = mysqli_real_escape_string($new_conn, $row['answer_value']);
    $answer_position = mysqli_real_escape_string($new_conn, $row['answer_position']);

    $vote_sql = mysqli_query($old_conn, "SELECT count(answer_id) FROM ro_forum_poll_votes WHERE 1=1 AND answer_id='$answer_id' AND topic_id='$community_discussion_id'");
    $vote_count = mysqli_fetch_assoc($vote_sql);
    $vote_count = mysqli_real_escape_string($new_conn, $vote_count['count(answer_id)']);


    $insert_sql = "INSERT INTO community_discussion_survey_details (
  `com_dis_id`,
  `option`,
  `option_order`,
  `total_vote`,
  `created_at`, 
  `updated_at`
  )
VALUES (
  '" . $community_discussion_id . "', 
  '" . $answer_value . "', 
  '" . $answer_position . "',
  '" . $vote_count . "',
  '" . date("Y:m:d H:i:s") . "',
  '" . date("Y:m:d H:i:s") . "'
  )";



    if ($new_conn->query($insert_sql) === TRUE) {
      echo $row['answer_value'] . ' ' . 'Added</br>';
    } else {
      //echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
    }
  }
} else {
  echo "0 results found";
}
