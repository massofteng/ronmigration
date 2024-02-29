<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM   core_translations  where lang_key = '547_content'";


$result = mysqli_query($old_conn, $sql);

//ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
ini_set('max_execution_time', '0'); // for infinite time of execution
$lagn_group = [];

if (mysqli_fetch_array($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
  //     print_r($row['lang_key']);

       $_lagn_group['group'] = $row['lang_group'];
        $_lagn_group['key'] = $row['lang_key'];
        $_lagn_group['row'] = $row;
        $lagn_group[$row['lang_group'] . '_' . $row['lang_key']][] = $row;
    }
} else {
    echo "0 results found";
}

$new_row = [];
foreach ($lagn_group as $key => $lagngroup) {
    $_new_row['group'] = $lagngroup[0]['lang_group'];
    $_new_row['key'] = $lagngroup[0]['lang_key'];
    $_new_row['created_by'] = 0;
    $_new_row['modified'] = date('Y-m-d H:i:s',$lagngroup[0]['modified']);
    $_new_row['en_title'] = "";
    $_new_row['ger_title'] = "";
    $_new_row['fr_title'] = "";
    $_new_row['en_description'] = "";
    $_new_row['ger_description'] = "";
    $_new_row['fr_description'] = "";
    foreach ($lagngroup as $lag) {
       $translation= htmlentities($lag['translation']);
        if($lag['lang']=='en'){
            $_new_row['en_title'] = "";
            $_new_row['en_description'] = $translation;
        }elseif ($lag['lang']=='de'){
            $_new_row['ger_title'] = "";
            $_new_row['ger_description'] = $translation;
        }elseif ($lag['lang']=='fr'){
            $_new_row['fr_title'] = "";
            $_new_row['fr_description'] = $translation;
        }elseif($lag['lang']=='pt'){
            $_new_row['pt_title'] = "";
            $_new_row['pt_description'] = $translation;
        }

    }
    $new_row[] = $_new_row;
}



if (count($new_row) > 0) {
    foreach ($new_row as $langrow) {

     $insert_sql = "INSERT INTO core_city_writer_faqs (    
      `en_title`, 
      `ger_title`, 
      `fr_title`, 
      `en_description`, 
      `ger_description`, 
      `fr_description`, 
      `created_by`, 
      `created_at`, 
      `updated_at`
      )
    VALUES (
      '" . $langrow['en_title'] . "', 
      '" . $langrow['ger_title'] . "', 
      '" . $langrow['fr_title'] . "', 
      '" . $langrow['en_description'] . "', 
      '" . $langrow['ger_description'] . "', 
      '" . $langrow['fr_description'] . "',       
      '" . $langrow['created_by'] . "',       
      '" . $langrow['modified'] . "',
      '" . $langrow['modified'] .  "'
      )";

            if ($new_conn->query($insert_sql) === TRUE) {
                echo $langrow['group'] . '_' . $langrow['key'] . 'Added</br>';
            } else {
                echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
            }
        }

}