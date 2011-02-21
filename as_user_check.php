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
	}
}
add_action('wp_head','as_check_ip');

?>