<?php



/*function as_register_themes(){
	//list of css files, with theme names
	$themes = array(
		"theme_1" => "css/theme-1.css",
		"theme_2" => "css/theme-2.css",
		"theme_3" => "css/theme-3.css",
		"theme_4" => "css/theme-4.css",
		"theme_5" => "css/theme-5.css",
		"theme_6" => "css/theme-6.css"
		
			
	);
		// Fetch current defined themes from database
		global $wpdb;
		$theme_list_table = $wpdb->prefix . "app_switcher_theme_list";
		$db_themes = $wpdb->get_results("SELECT * FROM $theme_list_table;", ARRAY_A);
		
		//remove unused themes
		$remove = 0;
		$del_sql = "DELETE FROM $theme_list_table WHERE theme_name IN (";
		foreach ($db_themes as $defined){
			if(!in_array($defined['css_location'],$themes)){
				$add_to_list = $defined['theme_name'];
				$delete_list .= "'$add_to_list', ";
				$remove ++;
			}
		}
		$delete_list = substr($delete_list, 0, -2);//chop last two chars off 
		$del_sql = $del_sql.$delete_list.");";
		if($remove>0){
			$wpdb->query($del_sql);
		}
		
		//remove array items already in database
		foreach ($themes as $theme_name => $css_location){
			foreach ($db_themes as $defined){
				if (in_array($theme_name, $defined)) unset($themes[$theme_name]);
			}
		}
		
		//add new themes to database
		if (count($themes)>0){
			$sql = "INSERT INTO $theme_list_table(theme_name, css_location) VALUES ";
			foreach ($themes as $theme_name => $css_location){

				$sql .= "('$theme_name','$css_location'),";
			}
			$sql = substr($sql, 0, -1);//chop last comma off
			$sql .= ';';		
			$wpdb->query($sql);
		} 
		
}*/

//Fetch USL of CSS file based on theme name
function as_fetch_css($theme){
	global $wpdb;
	$theme_list_table = $wpdb->prefix . "app_switcher_theme_list";
	$css = $wpdb->get_results("SELECT css_location FROM $theme_list_table WHERE theme_name='$theme';", ARRAY_A);
	$css = $css[0]['css_location'];
	$location = plugins_url($css, __FILE__);
	return $location;
}

add_action('wp_head','as_register_themes');
add_action('admin_head','as_register_themes');

?>