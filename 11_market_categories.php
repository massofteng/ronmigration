<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM ro_categories where target_page='marktplatz'";
$result = mysqli_query($old_conn, $sql);
//ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
ini_set('max_execution_time', '0'); // for infinite time of execution 
if (mysqli_fetch_array($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {

    $category_title = mysqli_real_escape_string($new_conn, $row['category_key']);
    $parent_id = $row['parent_id'];
    if (!empty($parent_id)) {
      $parent_name_sql = "SELECT `category_key` FROM ro_categories WHERE category_id='$parent_id'";
      $parent_name_query = mysqli_query($old_conn, $sql);
      $parent_name = mysqli_fetch_assoc($parent_name_query);
      $parent_name = $parent_name['category_key'];
    } else {
      $parent_name = '';
    }

    // convert city names to id.
    $category_id = $row['category_id'];
    $city_id_query = "SELECT * FROM ro_categories_cities WHERE category_id='$category_id'";
    $city_id_sql = mysqli_query($old_conn, $city_id_query);
    $city_names = array();
    $lang_names = array();
    if ($city_id_sql->num_rows > 0) {
      while ($row = mysqli_fetch_assoc($city_id_sql)) {
        $city_names[] = $row['city_id'];
        $lang[] = $row['lang'];
      }
    }

    $city_id = array();
    if (is_array($city_names) && count($city_names)) {
      foreach ($city_names as $name) {
        if (!empty($name)) {
          switch ($name) {
            case 'zurich_en':
              $city_id[] = 1;
              break;
            case 'zuerich':
              $city_id[] = 2;
              break;
            case 'geneve':
              $city_id[] = 3;
              break;
            case 'lausanne':
              $city_id[] = 4;
              break;
            case 'basel':
              $city_id[] = 5;
              break;
            case 'bern':
              $city_id[] = 6;
              break;
            case 'luzern':
              $city_id[] = 7;
              break;
            case 'st_gallen':
              $city_id[] = 8;
              break;
            case 'winterthur':
              $city_id[] = 9;
              break;
            case 'winterthur':
              $city_id[] = 10;
              break;
            case 'family':
              $city_id[] = 11;
              break;
            default:
              break;
          }
        }
      }
    }

    $city_ids = !empty($city_id) ? json_encode($city_id) : 0;

    //langs
    $lang_id = array();
    if (is_array($lang_names) && count($lang_names)) {
      foreach ($lang_names as $lang_name) {
        if (!empty($lang_name)) {
          switch ($lang_name) {
            case 'en':
              $lang_id[] = 1;
              break;
            case 'fr':
              $lang_id[] = 2;
              break;
            case 'de':
              $lang_id[] = 3;
              break;
            default:
              break;
          }
        }
      }
    }

    $lang_ids = !empty($lang_id) ? json_encode($lang_id) : 0;
    if (empty($lang_ids)) {
      $category_language_query = "SELECT lang FROM ro_categories_categories WHERE category_id='$category_id'";
      $category_language_query = mysqli_query($old_conn, $category_language_query);
      if ($category_language_query->num_rows > 0) {
        $category_language = mysqli_fetch_assoc($category_language_query);
        $category_language = $category_language['lang'];
        if (!empty($category_language)) {
          switch ($category_language) {
            case 'en':
              $lang_id[] = 1;
              break;
            case 'fr':
              $lang_id[] = 2;
              break;
            case 'de':
              $lang_id[] = 3;
              break;
            default:
              $lang_id[] = 3;
              break;
          }
        }
      }
    }
    $lang_ids = !empty($lang_id) ? json_encode($lang_id) : 3;

    echo '<pre>';
    print_r($lang_ids);
    echo '</pre>';
    die();


    // $city_id_query = "SELECT `ro_categories`"
    // $city_id = $row['city_id'];
    $created_by = 1;
    $deleted_by = 0;

    //lang_id


    $have_subcategory = 0;

    $insert_sql = "INSERT INTO market_categories (
      `parent_id`,
      `name`,
      `have_subcategory`,
      `lang_id`
      `city_id`,
      `created_by`,
      `deleted_by`,
      `created_at`,
      `updated_at`
      )
    VALUES (
      '" . $parent_id . "',
      '" . $category_title . "', 
      '" . $have_subcategory . "', 
      '" . $lang_id . "', 
      '" . $city_ids . "',
      '" . $created_by . "',
      '" . $deleted_by . "',
      '" . date("Y:m:d H:i:s") . "', 
      '" . date("Y:m:d H:i:s") . "' 
      )";


    if ($new_conn->query($insert_sql) === TRUE) {
      echo $category_title . ' ' . 'Added</br>';
    } else {
      //echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
    }
  }
} else {
  echo "0 results found";
}
