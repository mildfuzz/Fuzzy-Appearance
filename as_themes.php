<?php





//Fetch URL of CSS file based on theme name
function as_fetch_css($theme){
	global $wpdb;
	$theme_list_table = $wpdb->prefix . "app_switcher_theme_list";
	$css = $wpdb->get_results("SELECT css_location FROM $theme_list_table WHERE theme_name='$theme';", ARRAY_A);
	$location = $css[0]['css_location'];
	
	return $location;
}



?>