<?php

class WPicasaAdmin {

	function menu(){
		//options
		if(WPicasaActions::installCheck()){
			add_options_page('WPicasa Options','WPicasa',9,'wpicasa.php',array('WPicasaAdmin','options'));
			//template
			add_submenu_page('themes.php','WPicasa Presentation and Templates','WPicasa',9,'wpicasa.php',array('WPicasaAdmin','presentation'));
			//post
		
			//manage
			add_management_page('Add Album','Albums',5,'wpicasa.php',array('WPicasaAdmin','manage'));
		}else{
			add_options_page('WPicasa Setup','WPicasa',9,'wpicasa.php',array('WPicasaAdmin','install'));
		}
	
	}
	
	function router(){//detects submitted form values and fires appropriate functions
	
		if($_POST['action']=='wpicasa_install'){
			if($_POST['wpicasa_folder']!='!false' && $_POST['wpicasa_folder'] != '!makenew'){
				$dir = $_POST['wpicasa_folder'];
			}elseif($_POST['wpicasa_folder'] == '!makenew'){
				$dir = $_POST['wpicasa_folder_new'];
			}
			if(WPicasaActions::setDirectory($dir)){
				header('Location: options-general.php?page='.$_GET['page'].'&success=true');
			}else{
				header('Location: options-general.php?page='.$_GET['page'].'&failure=true');
			}
		}
	
	}
	
	function options(){//the options page
	?>
	<div class="wrap">
		<h2>WPicasa Options Panel</h2>
	</div>
	<?php
	}

	
	function install(){
		$dirs = WPicasaActions::listFiles();
		if($_GET['failure']):
		?>
		<div class="updated"> <p><strong>Error</strong> - unable to create directory.</p> </div>
		<?php elseif($_GET['success']):?>
		<div class="updated"><p><strong></strong> - directory set.</p></div>
		<?php endif; ?>
	<div class="wrap">
		<h2>WPicasa Install</h2>
		
		<div id="wpicasainstallform">
		<p>
			In order to get WPicasa going, you need to choose a folder for WPicasa to monitor.
		</p>
		<form id="wpicasasetup" method="post" action="options-general.php?page=wpicasa.php">
			<input type="hidden" name="action" value="wpicasa_install" />
			<p><label for="wpicasa_folder">Folder:</label>
			<select tabindex="1" id="wpicasa_folder" name="wpicasa_folder">
				<option value="!false"></option>
				<option value="!makenew">-- Create New Folder --</option>
				<?php if($dirs):foreach($dirs as $d):?>
				<option value="<?php echo $d;?>"><?php echo $d;?></option>
				<?php endforeach;endif;?>
			</select></p>
			<p><label for="wpicasa_folder_new">New Folder:</label>
			<input tabindex="2" type="text" id="wpicasa_folder_new" name="wpicasa_folder_new" size="25" /></p>
			
			<p class="submit">
				<input class="submit" type="submit" value="Install" name="wpicasainstallsubmit" />
			</p>
			
		</form>
		</div>
	</div>
	<?php
	}
	
	function presentation(){
	?>
	<div class="wrap">
	
	</div>
	<?php	
	}
	
	function manage(){
	?>
	<div class="wrap">
	<?php if($_GET['mode']=='publishalbum' && $_GET['folder']): $type=(!$_GET['type']) ? 'hosted' : 'external'; $album = new WPicasaXML($_GET['folder'], $type);?>
		<h2>Publish <em><?php $album->albumName()?></em></h2>
		<p><?php $album->albumCaption()?></p>
	<?php elseif(!$_GET['mode']):?>
		<h2>Album Queue</h2>
		<?php WPicasaActions::albumQueue() ?>
		<h2>Published Albums</h2>
		<p>These albums have already been published.</p>
	<?php endif;?>
	</div>
	<?php
	}
	
	function stylesheet(){
		if($_GET['page']=='wpicasa.php'){
		?>
		<link rel="stylesheet" type="text/css" href="<?php echo get_settings('siteurl')?>/wp-content/plugins/wpicasa/styles/style.css" />
		<?php
		}
	}
	
	
}

?>