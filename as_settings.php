<?php

add_action('admin_menu','as_settings_setup');

function as_settings_setup(){
add_menu_page("Appearance Switcher","Appearance Switcher",'manage_options',"app_switch",'display_app_switcher_settings');	

//add_settings_field('vimeo_id','Vimeo ID','display_vimeo','general');
}
function display_app_switcher_settings(){
			
			if ( $_SERVER["REQUEST_METHOD"] == "POST" ){
			    if($_POST['form_value'] == "theme_upload") upload_check();//run theme uploader scripts
				if($_POST['form_value'] == "theme_management") delete_themes();
				if($_POST['form_value'] == "zip_upload") zip_upload();
			}
			
	         ?><h3 class='title'>Appearance Switcher</h3><?php
			theme_upload_form();
			theme_upload_image_zip_form();
	        theme_manage_form();
	
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
			<label for="css_upload">CSS File:</label> <input id="css_upload" name="uploadedfile" type="file" /><br />
			<label for="img_upload">Image File:</label> <input id="img_upload" name="uploadedimage" type="file" /><br />
		
		
		
		</td>
		</tr>	
		</tr>
		
	
	<input type="submit" value="Upload"/>
	</form><?php
}
function theme_upload_image_zip_form(){
	global $wpdb;
	$theme_list_table = $wpdb->prefix . "app_switcher_theme_list";
	$themes = $wpdb->get_results("SELECT theme_name, id from $theme_list_table", ARRAY_A);
	
	?>
	<form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<tr valign="top">
		<th scope="row"><h4 class="section">Image Zip</h4></th>
		<tr>
		<td>
			<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
			<input type="hidden" name="form_value" value="zip_upload" />
			<table>
			<?php foreach($themes as $theme):?>
			<tr>
				<td><?php echo str_replace('_',' ',$theme['theme_name']); ?></td>
				<td><input type="radio" name="theme[]" value="<?php echo $theme['id']; ?>"></td>	
			</tr>		
			<?php endforeach; ?>
			</table><br />
			<label for="zip_upload">ZIP File:</label> <input id="zip_upload" name="zip_file" type="file" /><br />
			
		
		
		</td>
		</tr>	
		</tr>
		
	
	<input type="submit" value="Upload"/>
	</form><?php
}

function theme_manage_form(){
	global $wpdb;
	$theme_list_table = $wpdb->prefix . "app_switcher_theme_list";
	$themes = $wpdb->get_results("SELECT * from $theme_list_table", ARRAY_A);
	
	
	?>
	<form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<tr valign="top">
		<th scope="row"><h4 class="section">Theme Management</h4></th>
		<tr>
		<td>
			
			<input type="hidden" name="form_value" value="theme_management" />
			<table>
			<?php foreach($themes as $theme):?>
			<tr>
				<td><?php echo str_replace('_',' ',$theme['theme_name']); ?></td>
				<td><img src="<?php echo $theme['image_location'] ; ?>" height="60" /></td>
				<td><input type="checkbox" name="delete[]" value="<?php echo $theme['id']; ?>"></td>	
			</tr>		
			<?php endforeach; ?>
			</table>
		
		
		</td>
		</tr>	
		</tr>
		
	
	<input type="submit" value="Delete"/>
	</form><?php
}

/*
# Processing Functions for Theme Uploading
*/
function upload_check(){
	if(!check_theme_name($_POST['theme_name'])){
		echo "<h4 class='error'>Theme Name already exists</h4>";
		$fail = true;
	}
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
	if($_FILES['uploadedimage']['type'] != 'image/png' && $_FILES['uploadedimage']['type'] != 'image/jpeg' && $_FILES['uploadedimage']['type'] != ''){
		echo "<h4 class='error'>Image must be jpg or png</h4>";
		$fail = true;
	}
	if($_FILES['uploadedimage']['type'] == ''){
		echo "<h4 class='error'>Must Include an Image File</h4>";
		$fail = true;
	}
	if(!$fail){
		upload_process();		
	} 
	
}
function upload_process(){
	$file_name = str_replace(' ','_',$_POST['theme_name']).'.css';
	$image_name = $_FILES['uploadedimage']['name'];
	$dir = ABSPATH . 'wp-content/plugins/app-switcher/css/';
	$image_dir = ABSPATH . 'wp-content/plugins/app-switcher/images/';
	$file = $_FILES['uploadedfile']['tmp_name'];
	$image = $_FILES['uploadedimage']['tmp_name'];
	
	$file_move = move_uploaded_file($file,$dir.$file_name);
	
	if($file_move){
		$image_move = move_uploaded_file($image,$image_dir.$image_name);
		
		if($image_move){
			echo "<h3 class='update'>Upload Successful</h4>";
			add_to_theme_database();//add to database
		} else {
			unlink($dir.$file_name);
			echo "<h3 class='error'>Failed: Image Upload Failed</h4>";
		}
	} else {
		echo "<h3 class='error'>File Upload Failed</h4>";
	}
}
function add_to_theme_database(){
	global $wpdb;
	$theme_list_table = $wpdb->prefix . "app_switcher_theme_list";
	$theme_name = str_replace(' ','_',$_POST['theme_name']);
	$css_location = plugins_url("css/".$theme_name.".css", __FILE__);
	$image_location = plugins_url("images/".$_FILES['uploadedimage']['name'], __FILE__);
	$sql = "INSERT INTO $theme_list_table(theme_name, css_location, image_location) VALUES ('$theme_name','$css_location','$image_location')";
	$wpdb->query($sql);
}
function check_theme_name($theme){
	global $wpdb;
	$theme_list_table = $wpdb->prefix . "app_switcher_theme_list";
	$theme = str_replace(' ','_',$theme);
	$check =$wpdb->get_results("SELECT * FROM $theme_list_table WHERE theme_name='$theme';", ARRAY_A);
	if(!isset($check[0])){
		return true;
	} else {
		return false;
	}
}
/*
# Processing Functions for Theme Deleting
*/
function delete_themes(){
	global $wpdb;
	$theme_list_table = $wpdb->prefix . "app_switcher_theme_list";
	$del_sql = "DELETE FROM $theme_list_table WHERE id IN (";
	$del_files = "SELECT css_location, image_location FROM $theme_list_table WHERE id IN (";
	foreach ($_POST['delete'] as $del_theme){
		$del_sql .= "'$del_theme',";
		$del_files .= "'$del_theme',";
	}
	$del_sql = substr($del_sql,0,-1); //remove last comma
	$del_files = substr($del_files,0,-1); //remove last comma
	$del_sql .= ");";//SQL statment for deleting from database
	$del_files .= ");";//SQL for fetching css amd image location for deletion
	
	
	$files = $wpdb->get_results($del_files, ARRAY_A);//fetch file names for deletion
	$passed = true;
	foreach($files as $file){
		foreach($file as $component){
			if($passed != false ){//will stop deleting after first fail - Could be improved.
				$component = url_to_abs($component); //concerts URL path to filesystem for deletion
				$passed = unlink($component);
			} 
		}
		
	}
	if ($passed) {//if all deletes successful, delete from database.
		$wpdb->query($del_sql);
	} else {
		echo "<h2 class='warning'>DELETE FAILED</h2>";
	}//*/
	
	
}
function url_to_abs($string){
	$string = str_replace(plugins_url('',__FILE__),ABSPATH.'/wp-content/plugins/app-switcher',$string);
	return $string;
}
/*
# Processing Functions for zip uploading
*/
function zip_upload(){
	if (validate_zip_upload()){
		process_zip_upload();
	}
}
function validate_zip_upload(){
	
	if(!isset($_POST['theme'])){
		echo "<h4 class='error'>Must choose a theme for upload</h4>";
		$failed = true;
	}
	if($_FILES['zip_file']['type'] != 'application/zip'){
		echo "<h4 class='error'>Must be a zip file</h4>";
		$failed = true;
	}
	if(!$failed){
		return true;
	} else {
		return false;
	}
}
function process_zip_upload(){
	global $wpdb;
	$dir = ABSPATH . 'wp-content/plugins/app-switcher/css/images/';
	$zip = new zipArchive();
	$random_folder = $dir."tmp_".rand(10000,99999);
	$x=$zip->open($dir);
	
	if ($zip->open($_FILES['zip_file']['tmp_name'])){
		
		mkdir($random_folder);
		$zip->extractTo($random_folder);
		
		temp_folder_sort($random_folder, $dir);
		
		
		
	} 
	
	
}

function temp_folder_sort($temp_folder, $permanent_folder){
	
	foreach (new DirectoryIterator($temp_folder) as $fileInfo) {
	    if(!$fileInfo->isDir() && !$fileInfo->isDot()){
			$cur_file = $fileInfo->getPathname();
			if(!image_file_type($cur_file)) {
				unlink($cur_file); //delete non images
			} else {
	    		$files[] = getimagesize($fileInfo->getPathname());//write file name to array
			}
		} elseif($fileInfo->isDir() && !$fileInfo->isDot()) {
			if($fileInfo->getBasename() != '__MACOSX'){//prevents keeping OSX's entirely useless extra folder.
				$sub_folder = $permanent_folder.$fileInfo->getBasename().'/';
				if(!is_dir($sub_folder)) mkdir($sub_folder); //make subdirectory is one doesn't already exist. Keeps folder heirarchy of files.
			
				temp_folder_sort($fileInfo->getPathname(),$sub_folder);
			}
		}
	}
	
	
	move_directory_contents($temp_folder,$permanent_folder);
	delete_directory($temp_folder);
	
	
	
}

function image_file_type($file){
	$image = getimagesize($file);
	if(!$image) return false;//return false if not an image
	return $image['mime'];
}

function move_directory_contents($directory,$destination){
	
	foreach (new DirectoryIterator($directory) as $file) {
		
		$cur_file = $file->getPathname();
		$new_file = $destination.$file->getFilename();
		if(!$file->isDir() && !$file->isDot()){
			copy($cur_file,$new_file);
		} 
		
	}
}

function delete_directory($path){
	foreach (new DirectoryIterator($path) as $file) {
		$cur_file = $file->getPathname();
		if(!$file->isDir() && !$file->isDot()){
			unlink($cur_file);
		} elseif (!$file->isDot()){
			rmdir($cur_file);
		}
	}
	rmdir($path);
}

?>
