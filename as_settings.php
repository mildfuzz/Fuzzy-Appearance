<?php

add_action('admin_menu','as_settings_setup');

function as_settings_setup(){
$log = add_menu_page("Appearance Switcher","Appearance Switcher",'manage_options',"app_switch",'display_app_switcher_settings');	
fb::log($log);
//add_settings_field('vimeo_id','Vimeo ID','display_vimeo','general');
}
function display_app_switcher_settings(){
	if ( $_SERVER["REQUEST_METHOD"] == "POST" ){
	            print "do stuff";
	    } else {
	         ?><form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>"><input type="submit" /></form><?php
	        }
	
}
	
/*function display_vimeo(){
 
 echo '<input  type="text" name="vimeo_id" id="vimeo_id" value="'.attribute_escape(get_option('vimeo_id')).'" class="regular-text code" /> size="30" style="width:85%" />';
 echo '<p><small> Enter your Vimeo ID here.</small></p>';
}*/

/*
#
#
#
*/
/*	
	function top_message_options_init(){

	    register_setting( 'top_message_options_options', 'top_message_', 'top_message_options_validate' );

	}



	// Add menu page

	function top_message_options_add_page() {

	    add_options_page('Top Message Options', 'Top Message Settings', 'manage_options', 'top_message_options', 'top_message_options_do_page');

	}



	// Draw the menu page itself

	function top_message_options_do_page() {

	    ?>

	    <div class="wrap">

	        <h2>Top Message</h2>

	        <form method="post" action="options.php">

	            <?php settings_fields('top_message_options_options'); ?>

	            <?php $options = get_option('top_message_'); ?>

	            <table class="form-table">

	                <tr valign="top"><th scope="row">Top Message Text</th>

	                    <td><input type="text" name="top_message_[text]" value="<?php echo $options['text']; ?>" /><p>Message to Appear in the drop down</p></td>

	                </tr>

					<tr valign="top"><th scope="row">Top Message URL</th>

	                    <td><input type="text" name="top_message_[url]" value="<?php echo $options['url']; ?>" /><p>Link to attach to the message</p></td>

	                </tr>
					
	            </table>

	            <p class="submit">

	            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />

	            </p>

	        </form>

	    </div>

	    <?php    

	}



	// Sanitize and validate input. Accepts an array, return a sanitized array.

	function top_message_options_validate($input) {



	    // Say our option must be safe text with no HTML tags

	    $input['vimeo_id'] =  wp_filter_nohtml_kses($input['vimeo_id']);
		$input['flickr_api_key'] =  wp_filter_nohtml_kses($input['flickr_api_key']);
		//$input['flickr_user_id'] =  wp_filter_nohtml_kses($input['flickr_user_id']);




	    return $input;

	}
	


add_action('admin_init', 'top_message_options_init' );

add_action('admin_menu', 'top_message_options_add_page');



// Init plugin options to white list our options

//*/

?>
