<?php
/*
Plugin Name: Pixter Me
Plugin URI: http://www.pixter-media.com/wordpress
Description: Enable printing of images on accessories directly from your website.
This plugin adds a button on top of images in your website. The button appears on hover only.
Author: Pixter Media
Author URI: http://www.pixter-media.com
Text Domain: pixter-me
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Version: 1.2

Copyright 2015 Pixter Media
*/

defined('ABSPATH') && defined('WPINC') || die;

define('PM_PATH', dirname(__FILE__));

require_once PM_PATH.'/admin.php';

function plugins_loaded_pixter_me()
{
	// embed the javascript file that makes the AJAX request
	wp_enqueue_script( 'pixter-me-script', plugin_dir_url( __FILE__ ) . 'script.js', array( 'jquery' ) );
	wp_localize_script( 'pixter-me-script', 'pixterAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

	load_plugin_textdomain( 'pixter-me', false, dirname( plugin_basename( __FILE__ )).'/languages' );
}
add_action( 'plugins_loaded', 'plugins_loaded_pixter_me', 999999);

function pixter_me_activate()
{
	global $fields;
	$default = array();
	foreach ($fields as $conf)
		if (!empty($conf['default']) && !empty($conf['id']))
			$default[$conf['id']] = $conf['default'];

	update_option('pixter_me_options',$default);
}
register_activation_hook( __FILE__, 'pixter_me_activate' );

function pixter_me_init()
{
	// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
//	wp_enqueue_script( 'pixter-me-global', 'http://ddd.rrr.com/x.js', array(), '0.1', true );
}
add_action( 'init', 'pixter_me_init');

function pixter_me_inline_script()
{
	$apiKey = get_option('pixter_me_user');	
	if (!empty($apiKey) && !wp_script_is( 'pixter_me_inline', 'done' ) )
	{
		$options = (object)get_option('pixter_me_options');
		$button_text = $options->button_text;
		$button_bg_color = $options->button_bg_color;
		$button_text_color = $options->button_text_color;
		/*
		$button_position = $options->button_position;
		switch ($button_position)
		{
			case 'top-left':		$cssPos = "left: 5px; top: 5px;";	break;
			case 'top-right':		$cssPos = "left: auto!important; right: 5px; top: 5px;";	break;
			case 'bottom-left':		$cssPos = "left: 5px; top: auto!important; bottom: 5px;";	break;
			case 'bottom-right':	$cssPos = "left: auto!important; right: 5px; top: auto!important; bottom: 5px;";	break;
		}		
		
		*/
		$selector = $options->selector;

		echo <<<InlineScript

<script>
function onInitComplete()
{
	pLoader.initOnDemand({
		"selectors":"$selector", "minHeight":150, "minWidth":150, "position":"top-left",
		"text":"$button_text", "textColor":"$button_text_color", "buttonColor":"$button_bg_color"
	});	
}
</script>
<script src="https://pixter-loader-assets.s3.amazonaws.com/Loader/loader.js"
		onload="pLoader.initiate('$apiKey',null,true,true,onInitComplete)"></script>
InlineScript;
		global $wp_scripts;
		$wp_scripts->done[] = 'pixter_me_inline';
	}	
	
}
add_action( 'wp_footer', 'pixter_me_inline_script', 99999 );
