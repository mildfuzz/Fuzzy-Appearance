<?php
//check IP against database and sets cookie.
function as_check_ip(){
	if (isset($_COOKIE['as_theme_selection'])) return false;//if cookie set, ignore rest of function. Expires each day. Improves performance.
	global $wpdb;
	$ip = $_SERVER['REMOTE_ADDR'];
	
	$user_list_table = $wpdb->prefix . "app_switcher_ip_list";
	$results = $wpdb->get_results("SELECT theme FROM $user_list_table WHERE ip='$ip'", ARRAY_A);
	if(isset($results[0])){
		setcookie('as_theme_selection',$results[0]['theme'], time()+86400,'/');
		return $results[0]['theme'];
	} else {
		return false;
	}
}


//create theme selection list, to be called within the theme.

//SORT OUT REPEATING GET VARIABLES

//PROCESS CHOICES


// LOOK INTO  wp_register_sidebar_widget
function as_create_theme_selection_list(){
	ob_start();
	
	global $wpdb;
	$theme_list_table = $wpdb->prefix . "app_switcher_theme_list";
	$results = $wpdb->get_results("SELECT * FROM $theme_list_table;", ARRAY_A);
	?><ul id="theme_selection"><?php
	foreach($results as $result){
		$theme_name = str_replace('_',' ',$result['theme_name']);
		?><a href="<?php echo addURLQuery("theme-selection=".$result['theme_name']); ?>" id="<?php echo $result['theme_name']; ?>" class="theme"><?php echo $theme_name; ?></a> <?php
	}
	?></ul><?php
	//fb::log($results);
}
wp_register_sidebar_widget('as_theme_selector','AS Theme Selector','as_create_theme_selection_list');
function as_log_choice_database(){
	//process get variables into chosen themes, and redirect page
	
	global $wpdb;
	$ip = $_SERVER['REMOTE_ADDR'];
	$user_list_table = $wpdb->prefix . "app_switcher_ip_list";
	$results = $wpdb->get_results("SELECT theme FROM $user_list_table WHERE ip='$ip'", ARRAY_A);
	
	if(count($results)<1){
		fb::log('INSERT');
		$wpdb->insert( $user_list_table, array( "ip" => "$ip", "theme" => $_GET['theme-selection'] ));
	} else {
		fb::log('UPDATE');
		$wpdb->update( $user_list_table, array("theme" => $_GET['theme-selection'] ),  array("ip" => $ip));
	}
	
	
	$url = rebuildURL('theme-selection');
	header("Location: $url");
}

function as_theme_choice_processing(){
	//if(!isset($_SESSION['curl']) return null;
	$ip_result = as_check_ip();
	
	
	
	if(!$ip_result && !isset($_GET['theme-selection']) && !isset($_COOKIE['as_theme_selection'])){
		 fb::log('DEFAULT THEME');
		 return false;
	
	} 	elseif (isset($_GET['theme-selection'])) {
		 setcookie('as_theme_selection',$_GET['theme-selection'], time()+86400,'/');
		 as_log_choice_database();
		 fb::log($_GET['theme-selection'],'get result->');
		 return true;
	
	}	elseif(isset($_COOKIE['as_theme_selection'])) {
		 fb::log($_COOKIE['as_theme_selection'],'cookie result->');
		 return true;
	
	}  elseif($ip_result) {
		fb::log($ip_result,'database result->');
		return true;
	}
	
	
}

add_action('wp_head','as_theme_choice_processing');

?>