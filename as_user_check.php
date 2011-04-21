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

function as_create_theme_selection_list(){
	ob_start();
	
	global $wpdb;
	$theme_list_table = $wpdb->prefix . "app_switcher_zip_theme_list";
	$results = $wpdb->get_results("SELECT * FROM $theme_list_table;", ARRAY_A);
	//fb::loglog($results);
	if (count($results) > 0) :
	?><ul id="theme_selection"><?php
	foreach($results as $result){
		$theme_name = str_replace('_',' ',$result['theme_name']);
		?>
		<li class="theme <?php echo $result['theme_name'].($result['theme_name'] == $_COOKIE['as_theme_selection'] ? ' active':'') ;?>">
		<a href="<?php echo addURLQuery("theme-selection=".$result['theme_name']); ?>" id="<?php echo $result['theme_name']; ?>" class="theme">
		<img src="<?php echo $result['image_path']; ?>" alt="<?php echo $result['theme_name']; ?>" />
		<div><?php echo $theme_name; ?></div></a>
		</li>
		 <?php
	}
	?></ul><?php
	endif;
}
wp_register_sidebar_widget('as_theme_selector','AS Theme Selector','as_create_theme_selection_list');




//PROCESSING

function as_log_choice_database(){
	//process get variables into chosen themes, and redirect page
	
	global $wpdb;
	$ip = $_SERVER['REMOTE_ADDR'];
	$user_list_table = $wpdb->prefix . "app_switcher_ip_list";
	$results = $wpdb->get_results("SELECT theme FROM $user_list_table WHERE ip='$ip'", ARRAY_A);
	
	if(count($results)<1){
		//fb::loglog('INSERT');
		$wpdb->insert( $user_list_table, array( "ip" => "$ip", "theme" => $_GET['theme-selection'] ));
	} else {
		//fb::loglog('UPDATE');
		$wpdb->update( $user_list_table, array("theme" => $_GET['theme-selection'] ),  array("ip" => $ip));
	}
	
	
	$url = rebuildURL('theme-selection');
	
	header("Location: $url");
}

function as_theme_choice_processing(){
	
	//Runs debug CSS instead of default.
	$debug_css = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__))."/debug_mode/layout.css";
	if(as_switch_debug_mode() && is_user_logged_in()){
		?> <link rel="stylesheet" href="<?php echo $debug_css; ?>
		" type="text/css" media="screen" /><?php
		if(isset($_GET['theme-selection'])){
		 	as_log_choice_database();
		} else {
			return false;
		}
	}
	
	$ip_result = as_check_ip();
	if(as_fetch_css($_COOKIE['as_theme_selection']) != NULL || isset($_GET['theme-selection'])) {
	
	
	
	if(!$ip_result && !isset($_GET['theme-selection']) && !isset($_COOKIE['as_theme_selection'])){
		
		 return false;
	
	} 	elseif (isset($_GET['theme-selection'])) {
		
		 setcookie('as_theme_selection',$_GET['theme-selection'], time()+86400,'/');
		 as_log_choice_database();
		 return true;
	
	}	elseif(isset($_COOKIE['as_theme_selection'])) {
		 
	 	 
		echo as_fetch_css($_COOKIE['as_theme_selection']); 
		return true;
	
	}  elseif($ip_result) {
		
		echo as_fetch_css($_COOKIE['as_theme_selection']); 
		return true;
	}

	
		
	}
	else {
		
		global $wpdb;
		$theme_list = $wpdb->prefix . "app_switcher_zip_theme_list";
		//handle default
		$default = $wpdb->get_results("SELECT theme_name FROM $theme_list WHERE is_default=1", ARRAY_A);
		$default_id = $default[0]['theme_name'];
		echo as_fetch_css($default_id); 
		
	}//end NULL check
	
}

if(!is_page_template('holding.php')){
	
	add_action('wp_head','as_theme_choice_processing');
}
?>