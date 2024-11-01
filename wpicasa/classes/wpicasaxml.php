<?php

/*
WPicasaXML Eats XML for breakfast
 we like the domxml implementation
*/

class WPicasaXML {
	var $xml;
	var $physicalpath;	
	var $type;
	var $errormessage;
	var $error = false;
	var $inphotos = false;
	var $opentag = false;
	var $albumname;
	
	function WPicasaXML($albumpath, $type='hosted'){ //the construct
		$this->type = $type;
		$this->setPath($albumpath);
		$this->readXML();
	}

	function setPath($albumpath){
		if($this->type == 'hosted'){
			$this->albumfolder = $albumpath;
			$this->physicalpath = ABSPATH.'wp-content/'.get_settings('wpicasa_folder').'/'.$albumpath;
		}else{
			//$this->physicalpath = rawurlencode($albumpath);//we need to make sure we strip any urls
			//need to work on this so that it validates the the url better and even adds http:// if necessary as well as strip any trailing slash
			$pieces = explode('/',$albumpath);
			foreach($pieces as $p){
				if($p) $np .= rawurlencode($p).'/';
			}
			$this->physicalpath = $np;
		}
	}

	function albumName($e=true){
		if(!$this->error){
			$title = $this->albumname;
		}else{
			$title = 'Error reading album';
		}
		if($e) echo $title;
		return $title;
	}
	
	function albumCaption($e=true){
		if(!$this->error){
			$c = $this->albumcaption;
		}else{
			$c = $this->errormessage;
		}
		if($e) echo $c;
		return $c;
	}

	function readXML(){//open the xml file and read her in
		$file = $this->physicalpath.'/index.xml';
		if (!($fp = @fopen($file, "r"))) {
			$this->error = true;
			$this->errormessage = "Could not open XML file: $file";
		}
		
		if(!$this->error){
			$this->initParser();
			while($data = fread($fp, 4096)){
				if(!xml_parse($this->xml, $data, feof($fp))){
					$this->error = true;
					$this->errormessage = "XML error: ".xml_error_string(xml_get_error_code($this->xml))." at line ".xml_get_current_line_number($this->xml);
				}
			}
			$this->killParser();
		}
		
	}
	
	function startTag($p, $n, $attrs){
		if($n=='photos'){
			$this->inphotos = true;
		}
		$this->opentag = $n;
	}
	
	function endTag($p, $n){
		if($n=='photos'){
			$this->inphotos = false;
		}
		$this->opentag = false;
	}
	
	function charData($p, $d){
		//echo $this->opentag.": ".$d."<br/>";
		if($this->opentag == 'name' && !$this->inphotos) $this->albumname = $d;
	}
	
	function initParser(){//create parser and set options
		$this->xml = xml_parser_create();
		xml_parser_set_option($this->xml,XML_OPTION_SKIP_WHITE, true);
		xml_parser_set_option($this->xml,XML_OPTION_CASE_FOLDING, false);
		xml_set_object($this->xml, $this);
		xml_set_element_handler($this->xml,'startTag','endTag');
		xml_set_character_data_handler($this->xml,'charData');
	}
	
	function killParser(){//free the parser resource
		xml_parser_free($this->xml);
	}
}

?>