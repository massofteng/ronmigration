<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM ro_news";
$result = mysqli_query($old_conn, $sql);

//ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
ini_set('max_execution_time', '0'); // for infinite time of execution
$lagn_group = [];
//echo "<pre>";
if (mysqli_fetch_array($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {

        $publication_start=$publication_end="";
        $created_at=date('Y-m-d H:i:s');
        $status=0;
        $created_by=0;
        if (!empty($row['created'])) {
            $created_at = date('Y-m-d H:i:s', $row['created']);
        }
        if (!empty($row['publication_start'])) {
            $publication_start = date('Y-m-d H:i:s', ($row['publication_start']));
        }
        if (!empty($row['publication_end'])) {
            $publication_end = date('Y-m-d H:i:s', ($row['publication_end']));
        }

        if (!empty($row['active'])) {
            if($row['active']=='Y'){
                $status=1;
            }
        }

   $insert_sql = "INSERT INTO cms_posts (     
      `title`, 
      `short_description`, 
      
      `status`,
      `lang`,
      `city`,
      `start_date`,
      `end_date`,
      `button_text`,
      `button_url`,      
      `created_at`,
      `created_by`
      )
    VALUES (
      
      '" . $row['title'] . "', 
      '" . htmlentities($row['text']) . "', 
      
      '" . $status . "', 
      '" . $row['lang'] . "', 
      '" . $row['city_id'] . "', 
      '" . $publication_start . "', 
      '" . $publication_end . "', 
      '" . $row['link_1_title'] . "', 
      '" . $row['link_1_url'] . "',         
      '" . $created_at . "',    
      '" . $created_by . "'
      )";


        if ($new_conn->query($insert_sql) === TRUE) {
            echo $row['title'] . 'Added</br>';
        } else {
            echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
        }
    }
} else {
    echo "0 results found";
}