<?php

add_action('admin_menu','as_settings_setup');

function as_settings_setup(){
add_menu_page("Appearance Switcher","Appearance Switcher",'manage_options',"app_switch",'display_app_switcher_settings');	

//add_settings_field('vimeo_id','Vimeo ID','display_vimeo','general');
}
function display_app_switcher_settings(){
			
			if ( $_SERVER["REQUEST_METHOD"] == "POST" ){
			    if($_POST['form_value']) upload_check();//run theme uploader scripts
			}
			
	         ?><h3 class='title'>Appearance Switcher</h3><?php
			theme_upload_form();
	        
	
}

function theme_upload_form(){
	?>
	<form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<tr valign="top">
		<th scope="row"><h4 class="section">Theme Upload</h4></th>
		<tr>
		<td>
			<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
			<input type="hidden" name="form_value" value="theme_upload" />
			<label for="theme_name">Theme Name:</label> <input id="css_upload" name="theme_name" type="text" /><br />
			<label for="css_upload">Choose a file to upload:</label> <input id="css_upload" name="uploadedfile" type="file" /><br />
		
		
		
		</td>
		</tr>	
		</tr>
		
	
	<input type="submit" value="Upload"/>
	</form><?php
}

function upload_check(){
	fb::log($_POST);
	fb::log($_FILES);
	if($_POST['theme_name'] == ""){
		echo "<h4 class='error'>Must include Theme Name</h4>";
		$fail = true;
	}
	if($_FILES['uploadedfile']['type'] != 'text/css' && $_FILES['uploadedfile']['type'] != "") {
		echo "<h4 class='error'>File Not CSS</h4>";
		$fail = true;
	}
	if($_FILES['uploadedfile']['type'] == "") {
		echo "<h4 class='error'>Must Include a CSS file</h4>";
		$fail = true;
	}
	if($_FILES['uploadedfile']['size'] > $_POST['MAX_FILE_SIZE']) {
		echo "<h4 class='error> File Not CSS</h4";
		$fail = true;
	}
	if($_FILES['uploadedfile']['error'] != 0) {
		echo "<h4 class='error>Upload Error</h4";
		$fail = true;
	}
	if(!$fail){
		upload_process();		
	} 
	
}
function upload_process(){
	fb::log(plugins_url('/css', __FILE__));
	fb::log($_FILES['uploadedfile']['tmp_name']);
	$file_name = str_replace(' ','_',$_POST['theme_name']).'.css';
	$dir = ABSPATH . 'wp-content/plugins/app-switcher/css/';
	$file = $_FILES['uploadedfile']['tmp_name'];
	
	$file_move = move_uploaded_file($file,$dir.$file_name);
	
	if($file_move){
		echo "<h3 class='updated'>File Upload Successful</h4>";
	} else {
		echo "<h3 class='error'>File Upload Failed</h4>";
	}
}
?>
