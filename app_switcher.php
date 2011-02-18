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

session_start();

function check_theme_selection(){
	if(isset($_SESSION['theme'])) { 
		$theme = $_SESSION['theme'])
	} elseif(isset($_GET['theme'])) {
		$theme = $_GET['theme']);
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

  


function app_switcher_install () {
   global $wpdb;

   $table_name = $wpdb->prefix . "app_switcher";
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $table_name . " (
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  ip text NOT NULL,
			  theme text NOT NULL,
			  UNIQUE KEY id (id)
			);";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		
}
register_activation_hook(__FILE__,'app_switcher_install');


?>