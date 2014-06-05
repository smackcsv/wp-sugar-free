<?php 
/*
*Plugin Name: WP Sugar free
*Plugin URI: http://www.smackcoders.com
*Description: Easy Lead capture Sugar Webforms and Contacts synchronization
*Version: 1.1.1
*Author: smackcoders.com
*Author URI: http://www.smackcoders.com
*
* Copyright (C) 2013 Smackcoders (www.smackcoders.com)
*
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*
* @link http://www.smackcoders.com/blog/category/free-wordpress-plugins
***********************************************************************************************
*/

ini_set("display_errors" , "Off");
error_reporting(0);

global $plugin_url_wp_sugar ;
$plugin_url_wp_sugar = plugins_url( '' , __FILE__ );
global $plugin_dir_wp_sugar;
$plugin_dir_wp_sugar = plugin_dir_path( __FILE__ );

if(!defined('sugarEntry') || !sugarEntry)
{
	define('sugarEntry', TRUE);
	require_once('nusoap/nusoap.php');
}
$fieldNames = array(
		'url' => __('URL'),
		'username' => __('User Name'),
		'password' => __('Password'),
		'appkey' => __('Unique Key'),
		'wp_sugar_free_smack_user_capture' => __('Capture User'),
	);	


require_once 'sugar-fields.php';
require_once 'widget-fields.php';
require_once 'smack-sugar-shortcodes.php';
require_once 'settings.php';
require_once 'navMenu.php';
require_once 'pro-features.php';

add_action ( 'admin_enqueue_scripts', 'LoadWpSugarFreeScript' );
add_action ( "admin_menu", "wpsugarfree" );
add_action( 'user_register', 'wp_sugar_free_capture_registering_users' );

register_deactivation_hook( __FILE__, 'wpsugarfree_deactivate' );

// Admin menu settings
function wpsugarfree() {
	global $plugin_url_wp_sugar;
	add_menu_page('WPSugarFree Settings', 'WP Sugar Free', 'manage_options', 'wp-sugar-free', 'wpsugarfree_settings', "{$plugin_url_wp_sugar}/images/icon.png");
}

function LoadWpSugarFreeScript() {
	global $plugin_url_wp_sugar;
	wp_enqueue_script("wp-sugar-free-script", "{$plugin_url_wp_sugar}/js/smack-sugar-scripts.js", array("jquery"));
	wp_enqueue_style("wp-sugar-free-css", "{$plugin_url_wp_sugar}/css/smack-sugar-style.css");
}

function wpsugarfree_deactivate()
{
	delete_option( 'smack_wp_sugar_free_settings' );
	delete_option( 'smack_wp_sugar_free_field_settings' );
	delete_option( 'smack_wp_sugar_widget_free_field_settings' );
}

function wpsugarfree_settings()
{
        echo wpsugarfree_topContent();
        $action = getActionWpSugarFree(); 
        ?>
        <div id="main-page">
                <?php echo wpsugarfree_topnavmenu(); ?>
                <div>
                        <?php $action(); ?>
                </div>
        </div>
        <?php
}

function wpsugarfree_rightContent(){
	global $plugin_url_wp_sugar;
	$rightContent = '<div class="wpsugarfree-plugindetail-box" id="wpsugarfree-pluginDetails"><h3>Plugin Details</h3>
		<div class="wpsugarfree-box-inside wpsugarfree-plugin-details">
		<table>	<tbody>
		<tr><td><b>Plugin Name</b></td><td>WP Sugar Free</td></tr>
		<tr><td><b>Version</b></td><td>1.0.1 <a style="text-decoration:none" href="http://www.smackcoders.com/free-wordpress-sugar-integration-advanced-web-form-generator-plugin.html" target="_blank">( Update Now )</a></td></tr>
		</tbody></table>
		<div class="company-detials" id="company-detials">
		<div class="wpsugarfree-rateus"><img width="70px" height="40px" style="margin-top:10px;" src="'.$plugin_url_wp_sugar.'/images/SubscribeViaEmail.gif"><a style="margin-left:15px;margin-top:-10px;" class="dash-action" target="_blank" href="http://www.smackcoders.com/free-wordpress-sugar-integration-advanced-web-form-generator-plugin.html">Rate Us</a></div>
		<div class="sugar-free-sociallinks">
		<label>Social Links :</label>
		<span><a target="_blank" href="https://plus.google.com/106094602431590125432"><img src="'.$plugin_url_wp_sugar.'/images/googleplus.png"></a></span>
		<span><a target="_blank" href="https://www.facebook.com/smackcoders"><img src="'.$plugin_url_wp_sugar.'/images/facebook.png"></a></span>
		<span><a target="_blank" href="https://twitter.com/smackcoders"><img src="'.$plugin_url_wp_sugar.'/images/twitter.png"></a></span>
		<span><a target="_blank" href="http://www.linkedin.com/company/smackcoders"><img src="'.$plugin_url_wp_sugar.'/images/linkedin.png"></a></span>
		</div>
		<div class="sugar-free-poweredby" id="poweredby"><a target="_blank" href="http://www.smackcoders.com/"><img src="http://www.smackcoders.com/wp-content/uploads/2012/09/Smack_poweredby_200.png"></a></div>
		</div>
		</div><!-- end inside div -->
		</div>';
		return $rightContent;
}

function wpsugarfree_topContent()
{ //wpsugarfree_topContent
	$header_content = '<div style="background-color: #FFFFE0;border-color: #E6DB55;border-radius: 3px 3px 3px 3px;border-style: solid;border-width: 1px;margin: 5px 15px 2px; margin-top:15px;padding: 5px;text-align:center"> Please check out <a href="http://www.smackcoders.com/blog/category/free-wordpress-plugins" target="_blank">www.smackcoders.com</a> for the latest news and details of other great plugins and tools. </div><br/>';
	return $header_content;
}

function testAccess()
{
	require_once("test-access.php");
} 

add_action('wp_ajax_testAccess', 'testAccess');


function wp_sugar_free_capture_registering_users($user_id)
{
	$siteurl=site_url();
	$config = get_option('smack_wp_sugar_free_settings');
	if($config['wp_sugar_free_smack_user_capture'] =='on')
	{

		$plugin_dir = dirname(__FILE__).'/';
		#changing the current location  from wp-admin 
		chdir($plugin_dir."/");

		$url = trim($config['url'], '/');
		$username = $config['username'];
		$password = $config['password'];
		if(!defined('sugarEntry') || !sugarEntry)
		{
			define('sugarEntry', TRUE);
			include_once('nusoap/nusoap.php');
		}

//		$url = "http://localhost/SugarCE";
		$client = new nusoapclient($url.'/soap.php?wsdl',true);

		$user_auth = array(
			'user_auth' => array(
			'user_name' => $username,
			'password' => $password,
			'version' => '0.1'
		),
		'application_name' => 'wp-sugar-free');
		$login = $client->call('login',$user_auth);
		$session_id = $login['id'];


		$result_lastnames = array();
		$result_emails = array();

		if(!$login) echo 'Login Failed';
		else {
			$get_entries_count_parameters = array(
			     //Session id
			     'session' => $session_id,
			     //The name of the module from which to retrieve records
			     'module_name' => 'Contacts',
			     //The SQL WHERE clause without the word "where".
			     'query' => "contacts.id in (SELECT eabr.bean_id FROM email_addr_bean_rel eabr JOIN email_addresses ea ON (ea.id = eabr.email_address_id) WHERE eabr.deleted=0)",
			     //If deleted records should be included in results.
		//           'deleted' => false
			);

			$result = $client->call('get_entry_list', $get_entries_count_parameters);

			$entry_list = $result['entry_list'];

			foreach($entry_list as $entry)
			{
				foreach($entry['name_value_list'] as $field)
				{
					if($field['name'] == 'last_name')
					{
						$result_lastnames[] = $field['value'];
					}
					if($field['name'] == 'email1')
					{
						$result_emails[] = $field['value'];
					}
					if($field['name'] == 'email2')
					{
						$result_emails2[] = $field['value'];
					}
				}
			}

		}

		$user_data = get_userdata( $user_id );
		$user_email = $user_data->data->user_email;

		$user_lastname = get_user_meta( $user_id, 'last_name', 'true' );
		$user_firstname = get_user_meta( $user_id, 'first_name', 'true' );
		if(empty($user_lastname))
		{
			$user_lastname = $user_data->data->display_name;
		}
		$post = Array(
				Array('name' => 'first_name', 'value' => $user_firstname),
				Array('name' => 'last_name', 'value' => $user_lastname),
				Array('name' => 'email1', 'value' => $user_email),
				Array('name' => 'assigned_user_id' , 'value' => '1'),
			);

		foreach($post as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string,'&');

		$response = $client->call('set_entry', array($session_id, 'Contacts', $post));
		$contact_id = $response['id'];

		if($contact_id) {
			$content= "successful";
		}
		else{
			$content= "failed";
		}
	}
}

?>
