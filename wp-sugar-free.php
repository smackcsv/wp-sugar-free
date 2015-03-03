<?php 
/*
*Plugin Name: WP Sugar free
*Plugin URI: http://www.smackcoders.com
*Description: Easy Lead capture Sugar Webforms and Contacts synchronization
*Version: 1.2
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

$config = get_option( 'smack_wp_sugar_free_settings' );
if($config['wp_sugar_free_smack_debug'] != 'on') {
	ini_set("display_errors" , "Off");
	error_reporting(0);
}

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
add_action ( 'user_register', 'wp_sugar_free_capture_registering_users' );
add_action ( 'after_plugin_row_wp-sugar-free/wp-sugar-free.php', 'plugin_row' );

register_deactivation_hook( __FILE__, 'wpsugarfree_deactivate' );

// Admin menu settings
function wpsugarfree() {
	global $plugin_url_wp_sugar;
	add_menu_page('WPSugarFree Settings', 'WP Sugar Free', 'manage_options', 'wp-sugar-free', 'wpsugarfree_settings', "{$plugin_url_wp_sugar}/images/icon.png");
}

// Move Pages above Media
function smacksugarfree_change_menu_order( $menu_order ) {
   return array(
       'index.php',
       'edit.php',
       'edit.php?post_type=page',
       'upload.php',
       'wp-sugar-free',
   );
}
add_filter( 'custom_menu_order', '__return_true' );
add_filter( 'menu_order', 'smacksugarfree_change_menu_order' );

/*
 * Function to get the plugin row
 * @$plugin_name as string
 */
function plugin_row($plugin_name){
	echo '</tr><tr class="plugin-update-tr"><td colspan="3" class="plugin-update"><div class="update-message"> Please migrate to our new plugin <a href="https://wordpress.org/plugins/wp-leads-builder-any-crm/" target="blank" >Leads Builder For Any CRM</a> for advanced features.</div></td>';
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

	$get_activate_plugin_list = get_option('active_plugins');
        if(!in_array('wp-leads-builder-for-any-crm/index.php', $get_activate_plugin_list)) { ?>
                <div align=center style="padding-top:220px;">
                        <form name="upgrade_to_latest" method="post">
                                <label style="font-size:2em;" id="info">Upgrade to Lead Builder CRM</label>
                                <input type="submit" class="" name="upgrade" id="upgrade" value="Click Here"/>
                        </form>
                </div>
                <?php if (isset($_POST['upgrade'])) { ?>
                <script>
                        document.getElementById('info').style.display = 'none';
                        document.getElementById('upgrade').style.display = 'none';
                </script>
                <div align=center style="padding-top:0px;">
                        <?php echo migrate_leadbuildfree(); ?>
                </div>
                <?php
                }
        }
}

function migrate_leadbuildfree() {

        require_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
        $plugin['source'] = 'https://downloads.wordpress.org/plugin/wp-leads-builder-any-crm.1.1.zip';
        $source = ( 'upload' == $type ) ? $this->default_path . $plugin['source'] : $plugin['source'];
        /** Create a new instance of Plugin_Upgrader */
        $upgrader = new Plugin_Upgrader( $skin = new Plugin_Installer_Skin( compact( 'type', 'title', 'url', 'nonce', 'plugin', 'api' ) ) );
        /** Perform the action and install the plugin from the $source urldecode() */
        $upgrader->install( $source );
        /** Flush plugins cache so we can make sure that the installed plugins list is always up to date */
        wp_cache_flush();
        $plugin_activate = $upgrader->plugin_info(); // Grab the plugin info from the Plugin_Upgrader method
        $activate = activate_plugin( $plugin_activate ); // Activate the plugin
	if ( !is_wp_error( $activate ) )
                deactivate_plugins('wp-sugar-free/wp-sugar-free.php');//Deactivate sugar plugin
        $this->populate_file_path(); // Re-populate the file path now that the plugin has been installed and activated
        if ( is_wp_error( $activate ) ) {
                echo '<div id="message" class="error"><p>' . $activate->get_error_message() . '</p></div>';
                echo '<p><a href="' . add_query_arg( 'page', $this->menu, admin_url( $this->parent_url_slug ) ) . '" title="' . esc_attr( $this->strings['return'] ) . '" target="_parent">' . __( 'Return to Required Plugins Installer', $this->domain ) . '</a></p>';
                return true; // End it here if there is an error with automatic activation
        }
        else {
                echo '<p>' . $this->strings['plugin_activated'] . '</p>';
        }

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
