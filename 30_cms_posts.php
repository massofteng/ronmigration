<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM ro_news";
$result = mysqli_query($old_conn, $sql);

ini_set('max_execution_time', '0');
$lagn_group = [];
if (mysqli_fetch_array($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {

        $publication_start=$publication_end="";
        $created_at=date('Y-m-d H:i:s');
        $status=0;
        $created_by=2;    
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

        if($row['city_id'] =='zuerich'){
            $city_id = 2;
        }else if($row['city_id']=='zurich_en'){
            $city_id = 1;
        }else if($row['city_id']=='lausanne'){
            $city_id = 3;
        }else if($row['city_id']=='luzern'){
            $city_id = 6;
        }else if($row['city_id']=='st_gallen'){
            $city_id = 7;
        }else if($row['city_id']=='winterthur'){
            $city_id = 8;
        }else{
            $city_id = 99; //No city
        }   
        
        if($row['lang']=='en'){
            $lang_id = 1;
        }else if($row['lang']=='fr'){
            $lang_id = 2;
        }else if($row['lang']=='de'){
            $lang_id = 3;
        }else{
            $lang_id = '';
        }

        $sql3 = "SELECT profile_id FROM ro_user_profiles where user_id=2 and is_current ='Y' limit 1";
        $profile_id = 0;
        if ($result3 = mysqli_query($old_conn, $sql3)) {
          while ($row3 = mysqli_fetch_row($result3)) {
            $profile_id = $row3[0];
          }
        } 

        if( $city_id!=99){
        $insert_sql = "INSERT INTO cms_posts (     
                `title`, 
                `short_description`, 
                `content_type`,
                `status`,
                `lang`,
                `city`,
                `start_date`,
                `end_date`,
                `button_text`,
                `button_url`,      
                `created_at`,
                `created_by`,
                `creator_profile_id`
            )
            VALUES (
                '" . $row['title'] . "', 
                '" . htmlentities($row['text']) . "', 
                1,
                '" . $status . "', 
                '" . $lang_id . "', 
                '" . $city_id . "', 
                '" . $publication_start . "', 
                '" . $publication_end . "', 
                '" . $row['link_1_title'] . "', 
                '" . $row['link_1_url'] . "',         
                '" . $created_at . "',    
                '" . $created_by . "',
                '" . $profile_id ."'
            )";

            if ($new_conn->query($insert_sql) === TRUE) {
                echo $row['title'] . 'Added</br>';
            } else {
                echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
            }
        }
    }
} else {
    echo "0 results found";
}