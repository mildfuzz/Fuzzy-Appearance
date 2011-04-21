<?php


add_action('admin_menu','as_settings_setup');

function as_settings_setup(){
add_menu_page("Appearance Switcher","Appearance Switcher",'manage_options',"app_switch",'display_app_switcher_settings');	

//add_settings_field('vimeo_id','Vimeo ID','display_vimeo','general');
}
function display_app_switcher_settings(){
			
			if ( $_SERVER["REQUEST_METHOD"] == "POST" ){
				
			    if($_POST['form_value'] == "theme_upload") upload_check();//run theme uploader scripts
				if($_POST['form_value'] == "theme_management") theme_management();
				if($_POST['form_value'] == "zip_upload") zip_upload();
				if($_POST['form_value'] == "debug_mode") debug_mode_switch();
			}
			
	         ?><h3 class='title'>Appearance Switcher</h3><?php
			
			theme_upload_image_zip_form();
	        theme_manage_form();
			debug_form();
}
function debug_form(){
	?>
	<form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<tr valign="top">
		<th scope="row"><h4 class="section">Debug Mode</h4></th>
		<tr>
		<td>
			<p>Debug mode will override style choice with files found in as_switcher/debug_mode</p>
			<input type="hidden" name="form_value" value="debug_mode" />
			<input id="debug_mode" name="debug_mode" type="checkbox" <?php debug_checkbox(); ?> /><br />
			
		
		
		
		</td>
		</tr>	
		</tr>
		
	
	<input type="submit" value="Debug"/>
	</form><?php
}
function theme_upload_image_zip_form(){
	global $wpdb;
	$theme_list_table = $wpdb->prefix . "app_switcher_zip_theme_list";
	$themes = $wpdb->get_results("SELECT theme_name, id from $theme_list_table", ARRAY_A);
	
	?>
	<form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<tr valign="top">
		<th scope="row"><h4 class="section">Theme Zip</h4></th>
		<tr>
		<td>
			<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
			<input type="hidden" name="form_value" value="zip_upload" />
			<label for="theme_name">Theme Name:</label> <input id="theme_name" name="theme_name" type="text" /><br />
			<label for="zip_upload">ZIP File:</label> <input id="zip_upload" name="zip_file" type="file" /><br />
			<label for="thumbnail_upload">Thumbnail:</label> <input id="thumbnail_upload" name="thumbnail_upload" type="file" /><br />
			
		
		
		</td>
		</tr>	
		</tr>
		
	
	<input type="submit" value="Upload"/>
	</form>
	
	
	<?php
}

function theme_manage_form(){
	global $wpdb;
	$theme_list_table = $wpdb->prefix . "app_switcher_zip_theme_list";
	$themes = $wpdb->get_results("SELECT * from $theme_list_table", ARRAY_A);
	$default = $wpdb->get_results("SELECT theme_name FROM $theme_list_table WHERE is_default=1", ARRAY_A);
	$default_id = $default[0]['theme_name'];
	
	
	?>
	<form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<tr valign="top">
		<th scope="row"><h4 class="section">Theme Management</h4></th>
		<tr>
		<td>
			
			<input type="hidden" name="form_value" value="theme_management" />
			<table>
			<thead>
				<td></td><td></td><td>Delete &nbsp;&nbsp;&nbsp;</td><td>Set as <br />Default</td>
			</thead>
			<?php foreach($themes as $theme):
			?>
			<tr>
				<td><?php echo str_replace('_',' ',$theme['theme_name']); ?></td>
				<td><img src="<?php echo $theme['image_path'] ; ?>" height="60" /></td>
				<td><input type="checkbox" name="delete[]" value="<?php echo $theme['id']; ?>"></td>
				<td><input type="radio" name="default[]" value="<?php echo $theme['id']; ?>" <?php if($theme['theme_name'] == $default_id) echo " checked"; ?>></td>	
			</tr>		
			<?php endforeach; ?>
			</table>
		
		
		</td>
		</tr>	
		</tr>
		
	
	<input type="submit" value="Update"/>
	</form><?php
}

/*
# Processing Functions for Theme management
*/

function theme_management(){
	if(isset($_POST['default'])) set_theme_default();
	if(isset($_POST['delete']))  delete_themes();
}

function set_theme_default(){
	global $wpdb;
	$theme_list_table = $wpdb->prefix . "app_switcher_zip_theme_list";
	
	//set all sets to false
	$wpdb->query("UPDATE $theme_list_table SET is_default=0");
	
	//set default
	$id=$_POST['default'][0];
	fb::log("UPDATE $theme_list_table SET is_default=1 WHERE id=$id");
	$wpdb->query("UPDATE $theme_list_table SET is_default=1 WHERE id=$id");
	
}

function delete_themes(){
	global $wpdb;
	$theme_list_table = $wpdb->prefix . "app_switcher_zip_theme_list";
	
	$del_sql = "DELETE FROM $theme_list_table WHERE id IN (";
	$del_files = "SELECT theme_name FROM $theme_list_table WHERE id IN (";
	
	foreach ($_POST['delete'] as $del_theme){
		$del_sql .= "'$del_theme',";
		$del_files .= "'$del_theme',";
		delete_theme_files($del_theme);
	}
	$del_sql = substr($del_sql,0,-1); //remove last comma
	$del_files = substr($del_files,0,-1); //remove last comma
	$del_sql .= ");";//SQL statment for deleting from database
	$del_files .= ");";//SQL for fetching css amd image location for deletion
	
	
	
	
	$files = $wpdb->get_results($del_files, ARRAY_A);//fetch file names for deletion
	
	$thumbnail_url = 
	fb::log($files);
	
	foreach($files as $file){
			$theme = str_replace(" ","_",$file['theme_name']);
			$thumb_dir = ABSPATH . 'wp-content/plugins/app-switcher/thumbnails/'.$theme."/";
			$theme_root = ABSPATH . 'wp-content/plugins/app-switcher/themes/';
			$theme_dir = theme_directory($theme_root,$theme, false);
			
			delete_directory($thumb_dir);
			delete_directory($theme_dir);
		
		
	}
	
	$wpdb->query($del_sql);
	
	
	
}
function delete_theme_files($theme){
	global $wpdb;
	$theme_file_table = $wpdb->prefix . "app_switcher_zip_theme_list";
	$css_file_table = $wpdb->prefix . "app_switcher_css_file_list";
	//delete css file references
	$name_array = $wpdb->get_results("SELECT theme_name FROM $theme_file_table WHERE id=$theme", ARRAY_A);
	
	$name = $name_array[0]['theme_name'];
	
	$wpdb->query("DELETE FROM $css_file_table WHERE theme_name='$name'");
	
	//delete files and database entires
	$sql="SELECT image_path FROM $theme_file_table WHERE theme_id='$theme'";
	$files = $wpdb->get_results($sql, ARRAY_A);
	foreach($files as $file){
		unlink($file['image_path']);
	}
	$wpdb->query("DELETE FROM $theme_file_table WHERE theme_id='$theme'");
	//*/
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
	fb::log($_FILES, 'files');
	$theme_root = ABSPATH . 'wp-content/plugins/app-switcher/themes/';
	
	if(!$_POST['theme_name']){
		echo "<h4 class='error'>Please Name Theme</h4>";
		$failed = true;
	}
	if(!theme_directory($theme_root,$_POST['theme_name'])){
		echo "<h4 class='warning'>Theme Name Exists</h4>";
		$failed = true;
	}
	if($_FILES['zip_file']['type'] != 'application/zip'){
		echo "<h4 class='warning'>Must be a zip file</h4>";
		$failed = true;
	}
	if($_FILES['thumbnail_upload']['type'] != 'image/png' && $_FILES['uploadedimage']['type'] != 'image/jpeg' && $_FILES['uploadedimage']['type'] != ''){
		echo "<h4 class='error'>Image must be jpg or png</h4>";
		$failed = true;
	}
	if($_FILES['thumbnail_upload']['type'] == ''){
		echo "<h4 class='error'>Must Include an Image File</h4>";
		$failed = true;
	}
	if(!$failed){
		return true;
	} else {
		return false;
	}
}
function theme_directory($root, $theme_name, $test = true){
	$theme_name = str_replace(" ","_",$theme_name);
	$theme_dir = $root.$theme_name.'/';
	
	if(is_dir($theme_dir) && $test){
		die("theme name in use");
	}
	
	return $theme_dir;
	
}

function add_theme_to_database($theme_id, $theme_dir){
	global $wpdb;
	$theme_list_table = $wpdb->prefix . "app_switcher_zip_theme_list";
	$thumb_path = transfer_thumbnail($theme_id);
	$sql = "INSERT INTO $theme_list_table (theme_name, theme_path, image_path) VALUES ('$theme_id','$theme_dir','$thumb_path')";
	$wpdb->query($sql);
}

function process_zip_upload(){
	$GLOBALS['current_theme_name'] = $_POST['theme_name'];
	
	
	
	$theme_root = ABSPATH . 'wp-content/plugins/app-switcher/themes/';
	$theme_dir = theme_directory($theme_root,$_POST['theme_name']);
	
	$theme_no_space = str_replace(" ","_",$_POST['theme_name']);
	
	
	
	add_theme_to_database($_POST['theme_name'], $theme_dir);
	
	$zip = new zipArchive();
	$x=$zip->open($theme_root);
	
	
	
	if ($zip->open($_FILES['zip_file']['tmp_name'])){
		mkdir($theme_dir);
		$random_folder = make_random_folder($theme_root);
		$zip->extractTo($random_folder);
		
		temp_folder_sort($random_folder, $theme_dir);
		
		
		
	} 
	
	
	//*/
}

function transfer_thumbnail($theme_id){
	$theme_name = str_replace(" ","_",$theme_id);
	$thumb_file = $_FILES['thumbnail_upload']['tmp_name'];
	$thumb_root = ABSPATH . 'wp-content/plugins/app-switcher/thumbnails/'.$theme_name."/";
	mkdir($thumb_root);
	$thumb_path = $thumb_root.$_FILES['thumbnail_upload']['name'];
	$thumb_url = plugins_url('/thumbnails/', __FILE__).$theme_name."/".$_FILES['thumbnail_upload']['name'];
	move_uploaded_file($thumb_file,$thumb_path);
	return $thumb_url;
}

function temp_folder_sort($temp_folder, $permanent_folder){



	
	foreach (new DirectoryIterator($temp_folder) as $fileInfo) {
	
	   
	if(!$fileInfo->isDir() && !$fileInfo->isDot()){
			$cur_file = $fileInfo->getPathname();
			$is_css = strcasecmp(substr($cur_file, strrpos($cur_file, '.') + 1),"CSS");
			
			
			if(!image_file_type($cur_file) && $is_css != 0){//remove all none CSS or image files
				unlink($cur_file); //delete non images
			} else {
				if($is_css == 0) write_css_file_to_database($fileInfo->getBasename(), $permanent_folder);
	    		$files[] = getimagesize($fileInfo->getPathname());//write file name to array
			}
		} elseif($fileInfo->isDir() && !$fileInfo->isDot()) {
			if($fileInfo->getBasename() != '__MACOSX'){ //blocks OSX's entirely useless extra folder.
			
				$sub_folder = $permanent_folder.$fileInfo->getBasename().'/';
				if(!is_dir($sub_folder)) mkdir($sub_folder); //make subdirectory is one doesn't already exist. Keeps folder heirarchy of files.
				temp_folder_sort($fileInfo->getPathname(),$sub_folder);
			}
		}
		//*/
	}
	
	//fb::log($permanent_folder,'perm folder');
	move_directory_contents($temp_folder,$permanent_folder);
	delete_directory($temp_folder);
	
	
//*/	
}

function make_random_folder($root){
	$random_folder = $root."tmp_".rand(10000,99999)."_".rand(10000,99999)."_".rand(10000,99999);
	if (is_dir($random_folder)) {
		make_random_folder($root);
		return false;
	}
	mkdir($random_folder);
	return $random_folder;
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

function write_css_file_to_database($file, $path){
	global $current_theme_name;
	global $wpdb;
	$css_list_table = $wpdb->prefix . "app_switcher_css_file_list";
	$css_location = "http://".$_SERVER['HTTP_HOST'].substr($path,strlen($_SERVER['DOCUMENT_ROOT'])).$file;
	$sql = "INSERT INTO $css_list_table (theme_name, css_path) VALUES ('$current_theme_name','$css_location')";
	
	$wpdb->query($sql);
	
	
}

function delete_directory($dirname){
   if (is_dir($dirname))
      $dir_handle = opendir($dirname);
   if (!$dir_handle)
      return false;
   while($file = readdir($dir_handle)) {
      if ($file != "." && $file != "..") {
         if (!is_dir($dirname."/".$file))
            unlink($dirname."/".$file);
         else
            delete_directory($dirname.'/'.$file);    
      }
   }
   closedir($dir_handle);
   rmdir($dirname);
}
 /*
DEBUG MODE FUNCTIONS
*/
function as_switch_debug_mode(){
	//determine is debug mode is on
	$debug_mode = get_option('as_switch_debug_mode',false);
	if(!$debug_mode) return false;
	return true;
}

function debug_mode_switch(){
	//manage toggle for debug mode.
	
	if(!as_switch_debug_mode() && $_POST['debug_mode'] == on){
		add_option('as_switch_debug_mode','on');
	} else {
		delete_option('as_switch_debug_mode');
	}
	
}

function debug_checkbox(){
	if(as_switch_debug_mode()) echo " checked='checked' ";
}
?>
