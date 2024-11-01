<?php
/*
Plugin Name: WPicasa
Plugin URI: http://beaucollins.com/laboratory/wpicasa/
Description: WP + Picasa = WPicasa
Version: 0.5
Author: Beau Collins 
Author URI: http://beaucollins.com/
*/

//Our class files, nice and clean

include_once('classes/wpicasaxml.php');
include_once('classes/wpicasaadmin.php');
include_once('classes/wpicasaactions.php');
include_once('classes/wpicasa.php');

//all of our API hooks are here in one spot

add_action('admin_menu',array('WPicasaAdmin','menu'));
add_action('init',array('WPicasaAdmin','router'));
add_action('admin_head',array('WPicasaAdmin','stylesheet'));


?>