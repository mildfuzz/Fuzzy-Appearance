<?php
/*
Plugin Name: CSS Switcher by IP
Plugin URI: http://mildfuzz.com
Description: A plugin that allows a CSS files be loaded based on user IP, and a selecter mechanism
Version: 0.1
Author: John Farrow
Author URI: http://mildfuzz.com
License: GPL2
*/


/*  Copyright 2011  John Farrow  (email : john@mildfuzz.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
//Install and Uninstall
global $app_switcher_db_version;
$app_switcher_db_version = "0.1";


function app_switcher_install () {
   global $wpdb;

   	$ip_list_table = $wpdb->prefix . "app_switcher_ip_list";
	$theme_list_table = $wpdb->prefix . "app_switcher_theme_list";
	if($wpdb->get_var("SHOW TABLES LIKE '$ip_list_table'") != $ip_list_table &&  $wpdb->get_var("SHOW TABLES LIKE '$theme_list_table'") != $theme_list_table) {
		$sql = "CREATE TABLE " . $ip_list_table . " (
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  ip text NOT NULL,
			  theme text NOT NULL,
			  UNIQUE KEY id (id)
			);";
		$sql2 = "CREATE TABLE " . $theme_list_table . " (
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  theme_name text NOT NULL,
			  css_location text NOT NULL,
			  UNIQUE KEY id (id)
			);";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		dbDelta($sql2);
		add_option("app_switcher_db_version",$app_switcher_db_version);
	}
}
function app_switcher_uninstall () {
   global $wpdb;

   	$ip_list_table = $wpdb->prefix . "app_switcher_ip_list";
	$theme_list_table = $wpdb->prefix . "app_switcher_theme_list";
	
	$sql = "DROP TABLE $ip_list_table;";
	$sql2 = "DROP TABLE $theme_list_table;";

	$wpdb->query($sql);
	$wpdb->query($sql2);
	
	delete_option("app_switcher_db_version");
	
}
register_activation_hook(__FILE__,'app_switcher_install');
register_deactivation_hook(__FILE__,'app_switcher_uninstall');


// Themes Functions
include 'as_themes.php';

// User Functions
include 'as_user_check.php';

session_start();
//
function check_theme_selection(){
	if(isset($_SESSION['theme'])) { 
		$theme = $_SESSION['theme'];
	} elseif(isset($_GET['theme'])) {
		$theme = $_GET['theme'];
	}
	
	
	if(!isset($theme) /* AND $theme not appear in theme list*/){
		return false;
	} else {
		return $theme;
	}
	
}

function choose_theme($theme){
	global $wpdb;
	$table_name = $wpdb->prefix . "app_switcher";
	
	//if $theme does not appear in theme list return false
	
	//fetch IP
	
	//add IP with theme selection to database
	
	//switch theme
}

?>