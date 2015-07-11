<?php
defined('ABSPATH') && defined('WPINC') || die;

require_once PM_PATH.'/admin-page-class/admin-page-class.php';

/**
* configure your fields
*/
global $fields;
$fields = array(
	array(
		"type" => "text",
		"name" => __("Button text", 'pixter-me'),
		"id" => "button_text",
		"desc" => __("The text which will appear on the button which is displayed while hovering an image", 'pixter-me'),
		"default" => __("Print Image", 'pixter-me')
		),
	array(
		"type" => "color",
		"name" => __("Button background color", 'pixter-me'),
		"id" => "button_bg_color",
		"desc" => __("Bacground color of the print button.", 'pixter-me'),
		"default" => "#3333CC"
		),
	array(
		"type" => "color",
		"name" => __("Button text color", 'pixter-me'),
		"id" => "button_text_color",
		"desc" => __("Text color of the print button.", 'pixter-me'),
		"default" => "#FFFFEE"
		),
	array(
		"type" => "radio",
		"name" => __("Button position", 'pixter-me'),
		"id" => "button_position",
		"options" => array('top-left' => 'Top Left', 'top-right' => 'Top Right', 'bottom-left' => 'Bottom Left', 'bottom-right' => 'Bottom Right'),
		"desc" => __("The position of the button relatively to the image.", 'pixter-me'),
		"default" => "top-left"
		),
	array(
		"type" => "text",
		"name" => __("CSS Selector", 'pixter-me'),
		"id" => "selector",
		"desc" => __("Selector for containers including relevant images. Comma separates multiple selectors.", 'pixter-me'),
		"default" => "#primary, .entry-content"
		),
	);

$pixter_me_user = get_option('pixter_me_user');
if (empty($pixter_me_user))
{
	add_action( 'wp_ajax_register_pixter', 'register_pixter' );

	function register_pixter()
	{
		if (empty($_POST['email']))
			wp_die('MissingData');

		$apiUrl = "https://publishers.pixter-media.com/index/wpgetapikey/?email=".$_POST['email'];

		$ch = curl_init();

		curl_setopt($ch , CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt($ch , CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_URL, $apiUrl);
		//execute post
		$result = trim(curl_exec($ch));
		if (empty($result))
			$result = '{"success":false,"message":"'.curl_error($ch).'"}';
		else
		{
			$values = json_decode($result);
			$api_key = $values->api_key;
			if (!empty($api_key))
				update_option( 'pixter_me_user', $api_key, true );

			unset($values->api_key);
			$result = json_encode($values);
		}

		curl_close($ch);
		wp_die($result); // this is required to terminate immediately and return a proper response
	}

	function pixter_me_admin_page_register()
	{
		$siteurl = get_bloginfo('siteurl');
		$admin_email = get_option('admin_email');
		$name = get_option('blogname');
		$password = wp_generate_password( 12, false );

		$logo = plugins_url( 'logo.png', __FILE__ );

		echo <<<RegisterFirst
<style>
.wp-admin #wpfooter { position: static; }

#admin_page_class { display: none; }
#register-pm
{
	padding: 20px;
	width: calc(100% - 50px);
	position: relative;
}
#registration-form
{
	font-size: 1.2em;
}
#registration-form > div
{
	margin: 20px 40px 20px 0;
	display: inline-block;
	position: relative;
}
#registration-form > div button
{
	padding: 7px 15px;
	border-radius: 7px;
	font-weight: bold;
	color: #0073aa;
	background-color: #FFF;
	border-color: #99A;
}
/*
#registration-form > div > label:first-child
{
	display: inline-block;
	width: 90px;
}
*/
input#tnc
{
	display: inline-block!important;
}
div.iphone-style[rel=tnc]
{
    display: none;
}
.toplevel_page_pixter_me_plugin .wp-menu-image img
{
	width: 18px;
}
.err
{
	position: absolute;
	display: none;
	top: 29px;
	left: 20px;
	padding: 5px 12px;
	background-color: #FFF;
	border: 1px solid #D00;
	color: #D00;
	border-radius: 7px;
	box-shadow: 1px 1px 3px #666;
	font-size: 0.9em;
}
</style>
<div style='margin: 20px 10px 0'>
	<a href='http://www.pixter-media.com' target='_blank'><img src='$logo'></a>
</div>
<div class="wrap" id="register-pm">
	<h2>Pixter.me</h2>
	<h3>Register $siteurl to <a href="http://www.pixter-media.com" target="_blank">Pixter.me</a></h3>
	<div id='registration-form'>
			<div>
				<label for="email">Email:</label>
				<input type="email" name="email" id="email" value="$admin_email">
				<div class="err">Must use a valid email</div>
			</div>
			<div>
				<input id="tnc" type="checkbox" name="tnc">
				<label for="tnc">I agree to the
					<a href="https://publishers.pixter-media.com//publisher/docs/terms_of_service.html" target="_blank">Terms of Service</a>
				</label>
				<div class="err">You must agree to terms</div>
			</div>
			<div>
				<button id="register-pixter-me">Register my website and create account</button>
			</div>
	</div>
</div>
RegisterFirst;

	}
	add_action('admin_page_class_before_page', 'pixter_me_admin_page_register');
}
else
{
	function pixter_me_admin_before_page()
	{
		$logo = plugins_url( 'logo.png', __FILE__ );
		echo "
			<style> .wp-admin #wpfooter { position: static; }	</style>
			<div style='margin: 20px 10px 0'>
				<a href='http://www.pixter-media.com' target='_blank'><img src='$logo'></a>
			</div>";
	}
	add_action('admin_page_class_before_page', 'pixter_me_admin_before_page');
}

/**
* configure your options page
*/
$config = array(
    'menu'=> array('top' => 'Pixter.me settings'),
    'page_title' => 'Pixter.me',
    'capability' => 'install_plugins',
    'option_group' => 'pixter_me_options',
    'id' => 'pixter_me_plugin',
    'fields' => $fields,
	'icon_url' => plugins_url( 'admin-icon.png', __FILE__ ),
    'position' => 82,
);
$options_panel = new BF_Admin_Page_Class($config);
