<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM core_translations  where lang_group = 'CMS_Elements'";

$result = mysqli_query($old_conn, $sql);

//ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
ini_set('max_execution_time', '0'); // for infinite time of execution
$lagn_group = [];

if (mysqli_fetch_array($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
      //  print_r($row);

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
    foreach ($lagngroup as $lag) {
        $_lang[$lag['lang']] = $lag['translation'];
        $_langdescription[$lag['lang']] = $lag['description'];
    }
    $_new_row['meta_title'] = json_encode($_lang);
    $_new_row['meta_description'] = json_encode($_langdescription);
    $new_row[] = $_new_row;
}

if (count($new_row) > 0) {
    foreach ($new_row as $langrow) {

     $insert_sql = "INSERT INTO seo_configurations (
      `page_name`, 
      `meta_title`, 
      `meta_description`, 
      `created_at`, 
      `updated_at`
      )
    VALUES (
      '" . $langrow['key'] . "', 
      '', 
      '" . $langrow['meta_description'] . "', 
      '" . date('Y-m-d H:i:s') . "',
      '" . date('Y-m-d H:i:s') . "'
      )";

            if ($new_conn->query($insert_sql) === TRUE) {
                echo $langrow['group'] . '_' . $langrow['key'] . 'Added</br>';
            } else {
                echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
            }
        }

}