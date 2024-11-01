<?php

//just separating the code out for my own sanity
//these are the functions that actually manipulate the database

class WPicasaActions {

	function setDirectory($dir=false){
		$base = ABSPATH.'/wp-content';
		$direxists = false;
		//first check if directory exists
		if($dir){
			if(is_dir($base.'/'.$dir)){
				$direxists = true;
			}else{
				if(mkdir($base.'/'.$dir)){
					$direxists = true;
				}
			}
			if($direxists){
				add_option('wpicasa_folder',$dir);
				return true;
			}
		}
		return false;
	}
	
	function installCheck(){
		//for now all we need to check is to make sure we have some default settins
		$folder = get_settings('wpicasa_folder');
		if($folder){
			return true;
		}else{
			return false;
		}
	
	}
	
	function albumQueue(){
		//gets unpublished albums
		$unpublished = WPicasaActions::unpublishedFolders();
		$albums = array();
		$errors = array();
		foreach($unpublished as $folder){
			$a = new WPicasaXML($folder);
			if(!$a->error){
				$albums[$folder] = $a;
			}else{
				$errors[$folder] = $a;
			}
		}
		foreach($albums as $album){
			$list .= "
			<li class=\"album\"><h3>$album->albumname</h3>
			<div><a href=\"?page=$_GET[page]&amp;mode=publishalbum&amp;folder=$album->albumfolder\" title=\"publish $album->albumname\">Publish</a></div></li>
			";
		}
		$output = ($list) ? "<ul class=\"wpicasaqueue\">\n".$list."\n</ul>\n" : "<p>There are no albums in the queue.";
		foreach($errors as $e){
			$elist .= "\t<li>$e->albumName <small>$e->errormessage</small></li>\n";
		}
		if($elist) $output .= "<ul class=\"wpicasaerrorqueue\">\n$elist\n</ul>";
		echo $output;
	}
	
	function unpublishedFolders(){
		$folderlist = WPicasaActions::allFolders();
		$published = WPicasaActions::publishedFolders();
		$unpublished = array();
		foreach($folderlist as $folder){
			if(!in_array($folder, $published)){
				$unpublished[] = $folder;
			}
		}
		return $unpublished;
	}
	
	function publishedFolders(){
		global $wpdb;
		$folders = $wpdb->get_col("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = 'wpicasa_album';");
		if(!$folders) $folders = array();
		return $folders;
	}
	
	function allFolders(){
		$wpicasapath = ABSPATH.'wp-content/'.get_settings('wpicasa_folder');
		$dirs = WPicasaActions::listFiles($wpicasapath);
		return $dirs;
	}
	
	function listFiles($path=false, $type='dir'){
		$files = array();
		if(!$path) $path = ABSPATH.'wp-content';
		$dh = opendir($path);
		while(false !== ($filename = readdir($dh))){
			if(is_dir($path.'/'.$filename) && $type=='dir' && $filename != '.' && $filename != '..'){
				$files[] = $filename;
			}elseif(@getimagesize($path.'/'.$filename) && $type=='photo'){
				$files[] = $filename;
			}
		}
		
		return $files;
	}
}

?>