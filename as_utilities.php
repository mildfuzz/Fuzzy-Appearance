<?php
//get current page url
if (!function_exists('curPageURL')){
	function curPageURL() {
		$pageURL = 'http';
 		if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 		$pageURL .= "://";
 		if ($_SERVER["SERVER_PORT"] != "80") {
  			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 		} else {
  			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 		}
 		return $pageURL;
	}
}

if (!function_exists('removeURLQuery')){
	//REMOVES SPECIFIED QUERY FROM THE $_GET GLOBAL, can accept arrays
	function removeURLQuery($query){
		if(is_array($query)){
			foreach ($query as $v){
				removeURLQuery($v);
			}
		}
		$get = $_GET;
		if(array_key_exists($query, $get)) {
			unset($get[$query]);
		} 
		return $get;
	}
}

if (!function_exists('constructQuery')){
	//BUILDS URL EXPENSION FROM AN ARRAY OF KEY/VALUES
	function constructQuery($query){
		if(!is_array($query)) die ("constructQuery requires an array as its argument.");
		
		$url_ext = "?";
		foreach($query as $k => $v){
			$url_ext .=$k."=".$v."&";
		}
		$url_ext = substr($url_ext, 0, -1);//chop last ampersand off
		
		return $url_ext;
	
	}
}
if (!function_exists('rebuildURL')){
	/* 	Rebuilds URL after removing a variable from the get list
	# 	Dependands
	#		constructQuery
	#		removeURLQuery
	#		curPageURL
	*/
		
	function rebuildURL($remove){
		$curl = preg_replace('/\?.*/', '', curPageURL()); //get current URL and remove the query string
		$query = constructQuery(removeURLQuery($remove));
		return $curl.$query;
		
		
	}
}
?>