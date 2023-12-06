<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM ro_newsletter WHERE 1=1 AND extrablatt='Y'";
$result = mysqli_query($old_conn, $sql);

//ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
ini_set('max_execution_time', '0'); // for infinite time of execution 
if ($result->num_rows > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    //1=left, 2=center, 3=right
    $newsletter_title = mysqli_real_escape_string($new_conn, $row['newsletter_title']);
    $created_by = $row['creator_id'];
    $publish_date = date('Y-m-d H:i:s', $row['start_on']);
    $created_at = date("Y-m-d H:i:s", $row['created']);
    $newsletter_status = $row['newsletter_status'];
    /**
     * Sync newsletter statuses.
     * 
     * new structure: 0=draft, 1=publish, 2=archive, 3=expire
     * old structure: 'new','ready','paused','complete'
     * 
     * sync: new = draft, ready = publish, paused = archive, complete = expire
     */
    if ('new' == $newsletter_status) {
      $newsletter_status = 0;
    } elseif ('ready' == $newsletter_status) {
      $newsletter_status = 1;
    } elseif ('paused' == $newsletter_status) {
      $newsletter_status = 2;
    } elseif ('complete' == $newsletter_status) {
      $newsletter_status = 3;
    }

    $insert_sql = "INSERT INTO cms_special_newsletters (
      `title`, 
      `slug`,
      `publish_date`,
      `status`,
      `created_by`,
      `created_at`,
      `updated_at`
      )
    VALUES (
      '" . $newsletter_title . "',
      '',
      '" . $publish_date . "', 
      '" . $newsletter_status . "',
      '" . $created_by . "',
      '" . $created_at . "',
      '" . $created_at . "'
      )";

    //var_dump($insert_sql);
    if ($new_conn->query($insert_sql) === TRUE) {
      echo $newsletter_title . ' ' . 'Added</br>';
    } else {
      //echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
    }
  }
} else {
  echo "0 results found";
}
