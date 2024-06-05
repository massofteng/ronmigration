<?php
include("newdb_conn.php");
include("olddb_conn.php");

$sql = "SELECT * FROM ro_newsletter WHERE 1=1 AND extrablatt='N'";
$result = mysqli_query($old_conn, $sql);

ini_set('max_execution_time', '0');
if ($result->num_rows > 0) {
	while ($row = mysqli_fetch_assoc($result)) {
		$user_id = $row['creator_id'];
		$profile_query = "SELECT  profile_id FROM ro_user_profiles WHERE user_id='$user_id'";
		$profile_query_ex = mysqli_query($old_conn, $profile_query);
		$profile_result = mysqli_fetch_assoc($profile_query_ex);

		$profile_id = 0;
		if ($profile_result) {
			$profile_id = $profile_result['profile_id'];
		}

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
			$newsletter_status = 0;
		} elseif ('complete' == $newsletter_status) {
			$newsletter_status = 2;
		}

		$slug =  str_ireplace(" ", "-", strtolower($newsletter_title));

		$newsletter_id = $row['newsletter_id'];
		$city_query = "SELECT  city_id FROM ro_newsletter_sent WHERE newsletter_id='$newsletter_id'";
		$city_query_ex = mysqli_query($old_conn, $city_query);
		$city_result = mysqli_fetch_assoc($city_query_ex);

		$city_id = 0;
		if ($city_result) {
			if ($city_result['city_id'] == 'zuerich') {
				$city_id = 2;
			} else if ($city_result['city_id'] == 'zurich_en') {
				$city_id = 1;
			} else if ($city_result['city_id'] == 'lausanne' || $city_result['city_id'] == 'geneve') {
				$city_id = 3;
			} else if ($city_result['city_id'] == 'basel') {
				$city_id = 4;
			} else if ($city_result['city_id'] == 'bern') {
				$city_id = 5;
			} else if ($city_result['city_id'] == 'luzern') {
				$city_id = 6;
			} else if ($city_result['city_id'] == 'st_gallen') {
				$city_id = 7;
			} else if ($city_result['city_id'] == 'winterthur') {
				$city_id = 8;
			} else if ($city_result['city_id'] == 'family') {
				$city_id = 9;
			} else {
				$city_id = 0;
			}
		}

		if ($city_id != 0) {

			$lang_query = "SELECT  lang_id FROM cities WHERE id='$city_id'";
			$lang_query_ex = mysqli_query($new_conn, $lang_query);
			$lang_result = mysqli_fetch_assoc($lang_query_ex);
			$lang_id = $lang_result['lang_id'];

			$insert_sql = "INSERT INTO cms_newsletters (
				`title`, 
				`slug`,
				`lead_text`,
				`publish_date`,
				`creator_profile_id`,
				`city`,
				`lang`,
				`status`,
				`created_by`,
				`created_at`,
				`updated_at`
			)
			VALUES (
				'" . $newsletter_title . "',
				'" . $slug . "',
				'',
				'" . $publish_date . "', 
				'" . $profile_id . "', 
				'" . $city_id . "', 
				'" . $lang_id . "', 
				'" . $newsletter_status . "',
				'" . $created_by . "',
				'" . $created_at . "',
				'" . $created_at . "'
			)";

			if ($new_conn->query($insert_sql) === TRUE) {
				echo $newsletter_title . ' ' . 'Added</br>';
			} else {
				//echo "Error: " . $insert_sql . "<br>" . $new_conn->error;
			}
		}
	}
} else {
	echo "0 results found";
}
