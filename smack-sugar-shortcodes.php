<?php
ini_set("display_errors" , "Off");
error_reporting(0);

add_filter('widget_text', 'do_shortcode');

add_shortcode('sugarcrm_webtolead','wp_sugar_free_normal_form');

add_shortcode('sugarcrm_webtolead_WG','wp_sugar_free_widget_form');

function wp_sugar_free_normal_form($atts)
{

$config = get_option("smack_wp_sugar_free_settings");
$config_field = get_option("smack_wp_sugar_free_field_settings");

$RequiredField = Array();
$config_widget_field = get_option("smack_wp_sugar_widget_free_field_settings");
if(!empty($config['url']) && !empty($config['username'])){
	if( !empty($config_field['fieldlist']) && is_array($config_field['fieldlist']) ){
		$field_list = implode(',', $config_field['fieldlist']);
	}

        $url = trim($config['url'], '/');
        $username = $config['username'];
        $password = $config['password'];
        if(!defined('sugarEntry') || !sugarEntry)
        {
                define('sugarEntry', TRUE);
                include_once('nusoap/nusoap.php');
        }
        $client = new nusoapclient($url.'/soap.php?wsdl',true);
        $user_auth = array(
                'user_auth' => array(
                'user_name' => $username,
                'password' => md5($password),
                'version' => '0.1'
        ),
        'application_name' => 'wp-sugar-free');
        $login = $client->call('login',$user_auth);
        $session_id = $login['id'];

        $recordInfo = $client->call('get_module_fields', array('session' => $session_id, 'module_name' => 'Leads'));

        if(isset($recordInfo['error']['number']) && is_array($recordInfo['error']) )
        {
                die("Please check the user name or password");
        }

        if(isset($recordInfo))
        {
                $j=0;
                $module = $recordInfo['module_name'];
                $AcceptedFields = Array( 'text' => 'text' , 'bool' => 'boolean', 'enum' => 'picklist' , 'varchar' => 'string' , 'url' => 'url' , 'phone' => 'phone' , 'multienum' => 'multipicklist' , 'radioenum' => 'radioenum', 'currency' => 'currency' );

                for($i=0;$i<count($recordInfo['module_fields']);$i++)
                {
                        if(array_key_exists($recordInfo['module_fields'][$i]['type'], $AcceptedFields)){
                                $allowedFields[$j]['fieldname'] = $recordInfo['module_fields'][$i]['name'];
                                $allowedFields[$j]['fieldlabel'] = trim($recordInfo['module_fields'][$i]['label'], ':');
                                $allowedFields[$j]['fieldid'] = $recordInfo['module_fields'][$i]['name'];
                                if($recordInfo['module_fields'][$i]['required'] == 1)
                                {
                                        $allowedFields[$j]['required'] = 'M';
					$RequiredField[] = $allowedFields[$j]['fieldname'];
                                }
                                $j++;
                        }
                }
        }
	$selectedFields = $allowedFields;
}
//$action=trim($config['url'], "/").'/modules/Webforms/post.php';
$content = "<form name='wpsugarfree_contactform' method='post'>";
//$content = "<form id='contactform' name='contactform' method='post' action='".$action."'>";
$content.= "<table>";
// Success message Added by Fredrick Marks
if(isset($_REQUEST['page_contactform']) && $_REQUEST['page_contactform'])
{
        extract($_POST);
	$required_count = 0;
        foreach($_POST as $key => $value)
        {
                if(($key != 'moduleName') && ($key != 'page_contactform') && ($key != '') && ($key != 'submit'))
                {
//                      $module_fields[$key] = $value;
                        $module_fields[] = Array( 'name' => $key, 'value' => $value);
			if(($value != '') && in_array($key, $RequiredField))
			{
				$required_count++;
			}
                }
        }
	$module_fields[] = array('name' => 'assigned_user_id' , 'value' => '1');
	if($required_count == count($RequiredField))
	{
		$response = $client->call('set_entry', array($session_id, $module, $module_fields));
		$responseid = $response['id'];
		if(($responseid != '') && ($response['error']['number'] == 0 ))
		{
			$content.= "<tr><td colspan='2' style='text-align:center;color:green;font-size: 1.2em;font-weight: bold;'>Thank you for submitting</td></tr>";
		}
		else
		{
			$content.= "<tr><td colspan='2' style='text-align:center;color:red;font-size: 1.2em;font-weight: bold;'>Submitting Failed</td></tr>";
		}
	}
	else
	{
		$content.= "<tr><td colspan='2' style='text-align:center;color:red;font-size: 1.2em;font-weight: bold;'>Submitting Failed</td></tr>";
	}
}// Fredrick Marks Code ends here
	if( is_array( $config_field['fieldlist'] ) )

	foreach ($selectedFields as $field) {
	    if(in_array($field['fieldname'], $config_field['fieldlist']))
	    {
		$content1="<p>";
		$content1.="<tr>";
		$content1.="<td>";
		$content1.="<label for='".$field['fieldname']."'>".$field['fieldlabel']."</label>";
		if(isset($field['required']))
		{
			$typeofdata = $field['required'];
		}
		else
		{
			$typeofdata = "";
		}
		if( $typeofdata == 'M' ){
		$content1.="<span  style='color:red;'>*</span>";
		}
		$content1.="</td><td>";
		$content1.="<input type='hidden' value='".$typeofdata."' id='".$field['fieldname']."_type'>";
		$content1.="<input type='text' size='30' value='' name='".$field['fieldname']."' id='".$field['fieldname']."'></p>";
		$content1.="</td></tr>";

		$content.=$content1;
	    }
	}
	$content.="<tr><td></td><td>";
	$content.="<p>";
	$content.="<input type='submit' value='Submit' id='submit' name='submit'></p><span style='font-size:11px;float:right;'>Generated by <a target='_blank' href='http://www.smackcoders.com/wordpress-sugar-integration-automated-multi-web-forms-generator-pro.html'>WP-Sugar</a></td></tr></table>";
        $content.="<input type='hidden' value='contactform' name='page_contactform'>";
	$content.="<input type='hidden' value='Leads' name='moduleName' />
</form>";
//return $content;

return $content;
}

function wp_sugar_free_widget_form($atts)
{
$RequiredField = Array();

$config = get_option("smack_wp_sugar_free_settings");
$config_field = get_option("smack_wp_sugar_free_field_settings");

$config_widget_field = get_option("smack_wp_sugar_widget_free_field_settings");

if(!empty($config['url']) && !empty($config['username'])){
	if( !empty($config_field['fieldlist']) && is_array($config_field['fieldlist']) ){
		$field_list = implode(',', $config_field['fieldlist']);
	}

        $url = trim($config['url'], '/');
        $username = $config['username'];
        $password = $config['password'];
        if(!defined('sugarEntry') || !sugarEntry)
        {
                define('sugarEntry', TRUE);
                include_once('nusoap/nusoap.php');
        }
        $client = new nusoapclient($url.'/soap.php?wsdl',true);
        $user_auth = array(
                'user_auth' => array(
                'user_name' => $username,
                'password' => md5($password),
                'version' => '0.1'
        ),
        'application_name' => 'wp-sugar-free');
        $login = $client->call('login',$user_auth);
        $session_id = $login['id'];

        $recordInfo = $client->call('get_module_fields', array('session' => $session_id, 'module_name' => 'Leads'));

        if(isset($recordInfo['error']['number']) && is_array($recordInfo['error']) )
        {
                die("Please check the user name or password");
        }

        if(isset($recordInfo))
        {
                $j=0;
                $module = $recordInfo['module_name'];
                $AcceptedFields = Array( 'text' => 'text' , 'bool' => 'boolean', 'enum' => 'picklist' , 'varchar' => 'string' , 'url' => 'url' , 'phone' => 'phone' , 'multienum' => 'multipicklist' , 'radioenum' => 'radioenum', 'currency' => 'currency' );

                for($i=0;$i<count($recordInfo['module_fields']);$i++)
                {
                        if(array_key_exists($recordInfo['module_fields'][$i]['type'], $AcceptedFields)){
                                $allowedFields[$j]['fieldname'] = $recordInfo['module_fields'][$i]['name'];
                                $allowedFields[$j]['fieldlabel'] = trim($recordInfo['module_fields'][$i]['label'], ':');
                                $allowedFields[$j]['fieldid'] = $recordInfo['module_fields'][$i]['name'];
                                if($recordInfo['module_fields'][$i]['required'] == 1)
                                {
                                        $allowedFields[$j]['required'] = 'M';
					$RequiredField[] = $allowedFields[$j]['fieldname'];
                                }
                                $j++;
                        }
                }
        }
	$selectedFields = $allowedFields;
}
$content="";
$content.= "<form id='wpsugarfree_widget_contactform' name='wpsugarfree_widget_contactform' method='post'><table>";
// Success message for widget area -- Code added by Fredrick Marks
if(isset($_REQUEST['widget_contactform']) && $_REQUEST['widget_contactform'])
{
        extract($_POST);
	$required_count = 0;
        foreach($_POST as $key => $value)
        {
                if(($key != 'moduleName') && ($key != 'page_contactform') && ($key != '') && ($key != 'submit'))
                {
//                      $module_fields[$key] = $value;
                        $module_fields[] = Array( 'name' => $key, 'value' => $value);
                        if(($value != '') && in_array($key, $RequiredField))
                        {
                                $required_count++;
                        }
                }
        }
	$module_fields[] = array('name' => 'assigned_user_id' , 'value' => '1');

        if($required_count == count($RequiredField))
        {

		$response = $client->call('set_entry', array($session_id, $module, $module_fields));
		$responseid = $response['id'];

		if(($responseid != '') && ($response['error']['number'] == 0 ))
		{
			$content.= "<tr><td colspan='2' style='text-align:center;color:green;font-size: 1.2em;font-weight: bold;'>Thank you for submitting</td></tr>";
		}
		else
		{
			$content.= "<tr><td colspan='2' style='text-align:center;color:red;font-size: 1.2em;font-weight: bold;'>Submitting Failed</td></tr>";
		}
	}
	else
	{
		$content.= "<tr><td colspan='2' style='text-align:center;color:red;font-size: 1.2em;font-weight: bold;'>Submitting Failed</td></tr>";
	}
} // Fredrick Marks Code ends here
	if( is_array( $config_widget_field['widgetfieldlist'] ) ) foreach ($selectedFields as $field) {
            if(in_array($field['fieldname'], $config_widget_field['widgetfieldlist']))
            {
		$content1="<p >";
		$content1.="<tr>";
		$content1.="<td>";
		$content1.="<label for='".$field['fieldname']."'>".$field['fieldlabel']."</label>";
		if(isset($field['required']))
		{
			$typeofdata = $field['required'];
		}
		else
		{
			$typeofdata = "";
		}
		if( $typeofdata == 'M' ){
		$content1.="<span style='color:red;'>*</span>";
		}
		$content1.="</td><td>";
		$content1.="<input type='hidden' value='".$typeofdata."' id='".$field['fieldname']."_type'>";
		$content1.="<input type='text' class='wp-sugar-free-widget-area-text' size='20' value='' name='".$field['fieldname']."' id='".$field['fieldname']."'></p>";
		$content1.="</td></tr>";
		$content.=$content1;
	    }
	}
	$content.="<tr><td></td><td>";
	$content.="<p>";
	$content.="<input type='submit' class='wp-sugar-free-widget-area-submit' value='Submit' id='submit' name='submit'></p>Generated by <a target='_blank' href='http://www.smackcoders.com/wordpress-sugar-integration-automated-multi-web-forms-generator-pro.html'>WP-Sugar</a></td></tr>";
	$content.="</table>";
	$content.="<input type='hidden' value='contactform' name='widget_contactform'>";
	$content.="</form>";

//echo $content;
return $content;

}
?>
