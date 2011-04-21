<?php





//Fetch URL of CSS file based on theme name
function as_fetch_css($theme){
	global $wpdb;
	$theme_list_table = $wpdb->prefix . "app_switcher_css_file_list";
	
	$css = $wpdb->get_results("SELECT css_path FROM $theme_list_table WHERE theme_name='$theme';", ARRAY_A);
	
	foreach($css as $css_file){
		$path = $css_file['css_path'];
		$echo .= "<link rel='stylesheet' href='$path' type='text/css' media='screen' />";
	}
	
	
	return $echo;
}



?>