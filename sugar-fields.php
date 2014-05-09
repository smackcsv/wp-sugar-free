<?php
/**
 * 
 * Function to get SugarCRM fields
 */
ini_set("display_errors" , "Off");
error_reporting(0);
function wp_sugar_free_fields() {
global $plugin_url_wp_sugar;
	$config = get_option ( 'smack_wp_sugar_free_settings' );

	if (isset ( $_POST ['url'] )) {
		$config ['username'] = $_POST ['username'];
		if($_POST['password'] != "")
		{
			$config ['password'] = $_POST ['password'];
		}
		$config ['url'] = $_POST ['url'];
		$config ['appkey'] = $_POST ['appkey'];
		update_option ( 'smack_wp_sugar_free_settings', $config );
	} else {
	        $config = get_option('smack_wp_sugar_free_settings');
		$config_field = get_option ( "smack_wp_sugar_free_field_settings" );
	}
	if (isset ( $_POST ['field_posted'] )) {
		$config_field ['fieldlist'] = array ();	
		if (isset ( $_POST ['no_of_vt_fields'] )) {
			$fieldArr = array ();
			for($i = 0; $i <= $_POST ['no_of_vt_fields']; $i ++) {
				if (isset ( $_POST ["smack_wp_sugar_free_field$i"] )) {
					array_push ( $fieldArr, $_POST ["smack_wp_sugar_free_field_hidden$i"] );
				}
			}
			$config_field ['fieldlist'] = $fieldArr;
		}
		update_option ( 'smack_wp_sugar_free_field_settings', $config_field );
	}
/*
$field_settings = get_option('smack_wp_sugar_free_field_settings');
if(!is_array($field_settings))
{
*/
	if(!defined('sugarEntry') || !sugarEntry)
	{
                define('sugarEntry', TRUE);
                include_once('nusoap/nusoap.php');
	}
	$url = trim($config['url'], '/');
	$username = $config['username'];
	$password = $config['password'];
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
				}
				$j++;
			}
		}
	}
//}
$content = '';
$content .= '<div class="sugar_free_left-side-content">
<div class="sugar_free_upgradetopro" id="upgradetopro" style="display:none;">This feature is only available in Pro Version, Please <a href="http://www.smackcoders.com/wordpress-sugar-integration-automated-multi-web-forms-generator-pro.html">UPGRADE TO PRO</a></div>
<div class="sugar-free-messageBox" id="message-box" style="display:none;" ><b>Successfully Saved!</b></div>
	<form id="smack_wp_sugar_free_field_form"
		action="'.$_SERVER['REQUEST_URI'].'" method="post">';

		if (! is_array ( $config_field ['fieldlist'] )) {
			$config_field ['fieldlist'] = array ();
		}

		if (! empty ( $allowedFields )) {
                if(isset($_POST['Submit']) && $_POST['Submit'] == 'Save Field Settings'){ ?>
                        <script>
                        wpsugarfreesaveSettings();
                        </script>
                <?php } 
                        $content .= '<div style="width:20%;float:left;"><h3 class="title">Field settings</h3></div><div style="width:80%;float:right;"><p>( Please use the short code <b> [sugarcrm_webtolead]</b> in page or post )</p></div><br/><br/>
			<div style="margin-top:10px;">
                        <div style="padding:2px;"><input type="checkbox" id="skipduplicate" onclick="wpsugarfreeupgradetopro()" /> Skip Duplicates. Note: Email should be mandatory and enabled to make this work. </div>
                        <div style="padding:2px;"><input type="checkbox" id="generateshortcode" onclick="wpsugarfreeupgradetopro()" /> Generate this Shortcode for widget form. </div>
                        <div style="padding:2px;">Assign Leads to User: <select id="assignto" onclick="wpsugarfreeupgradetopro()" ><option>Administrator</option><option>Standard User</option></select></div>
                        </div><br/>

		<input type="hidden" name="posted" value="posted" />
		<label for="smack_wp_sugar_free_fields">Choose the fields you want to display in Lead Capture page.</label><br/><br/>
		<input type="button" class="button-secondary submit-add-to-menu"
			name="sync_crm_fields" value="Fetch CRM Fields"
			onclick="wpsugarfreeupgradetopro()" />
		<input type="submit" value="Save Field Settings"
			class="button-secondary submit-add-to-menu" name="Submit" />
		<input type="button" class="button-secondary submit-add-to-menu"
			name="make_mandatory" id="make_mandatory"
			value="Save Mandatory Fields" onclick="wpsugarfreeupgradetopro()" /> <input
			type="button" class="button-secondary submit-add-to-menu"
			name="save_display_name" id="save_display_name" value="Save Labels"
			onclick="wpsugarfreeupgradetopro()" /> <input type="button"
			class="sugar-free-button-create-shortcode" name="create_shortcode"
			id="create_shortcode" value="Generate Shortcode"
			onclick="wpsugarfreeupgradetopro()" /><br/><br/>
		<table class="sugar_free_tableborder">
			<tr class="smack_sugar_free_alt">
				<th style="width: 50px;"><input type="checkbox" name="selectall"
					id="selectall"
					onclick="wpsugarfreeselect_allfields(\'smack_wp_sugar_free_field_form\',\'lead\')" /></th>
				<th style="width: 200px;"><h5>Field Name</h5></th>
				<th style="width: 100px;"><h5>Show Field</h5></th>
				<th style="width: 100px;"><h5>Order</h5></th>
				<th style="width: 120px;"><h5>Mandatory Fields</h5></th>
				<th style="width: 200px;"><h5>Field Label Display</h5></th>
			</tr>
			<tbody>
				<tr valign="top">

					<td><input type="hidden" id="no_of_vt_fields"
						name="no_of_vt_fields" value="'. sizeof($allowedFields) .'">';
							
		$nooffields = count ( $allowedFields );
		$inc = 1;
		foreach ( $allowedFields as $key => $field ) {
			?>
                  <?php if($inc % 2 == 1){
                       $content .= '<tr class="smack_sugar_free_highlight">';
                        } else{
                        $content .= '<tr class="smack_sugar_free_highlight smack_sugar_free_alt">';
                        }
			if(isset($field['required']))
			{
				$typeofdata = $field['required'];
			}
			else
			{
				$typeofdata = '';
			}
			$content .= '<td class="smack-sugar-free-field-td-middleit"><input type="hidden"
						value="'.$field['fieldlabel'].'"
						id="field_label'. $key .'"> <input type="hidden"
						value="'.$typeofdata.'"
						id="field_type'.$key.'"> <input type="hidden"
						name="smack_wp_sugar_free_field_hidden'.$key.'"
						value="'.$field['fieldid'].'" />';
			if ($typeofdata == 'M') {
				$checked = 'checked="checked" disabled';
				$mandatory = 'checked="checked" disabled';
			} else {
				$checked = "";
			}
			if( $typeofdata == 'M' ){ 
				$content .= '<input type="hidden"
					value="'.$field['fieldname'] .'"
						id="smack_wp_sugar_free_field'.$key.'"
						name="smack_wp_sugar_free_field'.$key.'" /> <input
						type="checkbox" value="'.$field['fieldname'].'"'. $checked .' />';
			}else { 
				$content .= '<input type="checkbox"
							value="'.$field['fieldname'].'"
								id="smack_wp_sugar_free_field'.$key.'"
								name="smack_wp_sugar_free_field'.$key.'"'. $checked .'/>';
			}
			$content .= "</td>
					<td>{$field['fieldlabel']}";
			if( $typeofdata == 'M' ){ 
				$content .= '<span style="color: #FF4B33">&nbsp;*</span>';
			}
			$content .= '</td>';			
			$contentUrl = WP_CONTENT_URL;
			$imagepath = "{$plugin_url_wp_sugar}/images/";

			$content .= '<td class="smack-sugar-free-field-td-middleit">';
			if ($typeofdata == 'M') {
				$content .= '<img src="'.$imagepath.'tick_strict.png"
					onclick="wpsugarfreeupgradetopro()" />';
			}
			elseif(in_array ( $field['fieldid'], $config_field ['fieldlist'] )) {
			{
				$content .= '<img src="'.$imagepath.'tick.png"	onclick="wpsugarfreeupgradetopro()" />';
			}
			} else {
       				$content .= '<img src="'.$imagepath.'publish_x.png"
						onclick="wpsugarfreeupgradetopro()" />';
			}
			$content .= '</td>
				<td class="smack-sugar-free-field-td-middleit">';
			if($inc == 1){ 
				$content .= '<a class="smack_sugar_free_pointer" id="down'.$i.'" onclick="wpsugarfreemove(\'down\');"><img
							src="'.$imagepath.'downarrow.png" /></a>';
			} elseif($inc == $nooffields){ 
				$content .= '<a class="smack_sugar_free_pointer" id="up'.$i.'" onclick="wpsugarfreemove(\'up\');"><img
							src="'.$imagepath.'uparrow.png" /></a>';
			}else{ 
				$content .= '<a class="smack_sugar_free_pointer" id="down'.$i.'" onclick="wpsugarfreemove(\'down\');"><img
							src="'.$imagepath.'downarrow.png" /></a> <a
						class="smack_sugar_free_pointer" id="up'.$i.'" onclick="wpsugarfreemove(\'up\');"><img
							src="'.$imagepath.'uparrow.png" /></a>';
			} 
			$content .= '</td>
					<td class="smack-sugar-free-field-td-middleit"><input type="checkbox"
						name="check'.$i.'" id="check'.$i.'"';
					 if( $typeofdata == 'M' ){ 
						$content .= 'checked="checked" disabled';
					 } 
			$content .=' /></td>
					<td class="smack-sugar-free-field-td-middleit"
						id="field_label_display_td'.$i.'"><input type="text"
						id="field_label_display_textbox'.$i.'" class="readonly-text" onclick="wpsugarfreeupgradetopro()"
						value="'.$field['fieldlabel'].'" readonly /></td>
				</tr>';
			$inc++;
			}
			$content .= '</td>
				</tr>
			</tbody>
		</table>
		<p>Please use the short code <b> [sugarcrm_webtolead]</b> in page or post</p>
		<input type="hidden" name="field_posted"
			value="posted" />

	</form>
</div>
<div class="sugar_free_right-side-content" >'.wpsugarfree_rightContent().'                                     
</div>';
	echo $content;
	} else{
                $Content = "<div style='margin-top:20px;font-weight:bold;'>
                                Wp-sugar <a href=".admin_url()."admin.php?page=wp-sugar-free&action=wp_sugar_free_plugin_settings>settings</a> not configured.
                                </div>";
                echo $Content;
	}
}?>
