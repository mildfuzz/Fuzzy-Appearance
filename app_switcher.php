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
	$zip_image_connect_table = $wpdb->prefix . "app_switcher_zip_image_list";
	if($wpdb->get_var("SHOW TABLES LIKE '$ip_list_table'") != $ip_list_table &&  $wpdb->get_var("SHOW TABLES LIKE '$theme_list_table'") != $theme_list_table &&  $wpdb->get_var("SHOW TABLES LIKE '$zip_image_connect_table'") != $zip_image_connect_table) {
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
			  image_location text NOT NULL,
			  UNIQUE KEY id (id)
			);";
		$sql3 = "CREATE TABLE " . $zip_image_connect_table . " (
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  theme_id mediumint(9) NOT NULL,
			  image_path text NOT NULL,
			  UNIQUE KEY id (id)
			);";
		

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		dbDelta($sql2);
		dbDelta($sql3);
		add_option("app_switcher_db_version",$app_switcher_db_version);
	}
}
function app_switcher_uninstall () {
   global $wpdb;

   	$tables[] = $wpdb->prefix . "app_switcher_ip_list";
	$tables[] = $wpdb->prefix . "app_switcher_theme_list";
	$tables[] = $wpdb->prefix . "app_switcher_zip_image_list";
	
	foreach($tables as $table){
		$wpdb->query("DROP TABLE $table;");
	}
	
	
	delete_option("app_switcher_db_version");
	
}
register_activation_hook(__FILE__,'app_switcher_install');
register_deactivation_hook(__FILE__,'app_switcher_uninstall');


session_start();
ob_start();
//utilities
include 'as_utilities.php';
// Themes Functions
include 'as_themes.php';

// User Functions
include 'as_user_check.php';

//Settings Page;
include 'as_settings.php';


?>