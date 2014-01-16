<?php
function wp_sugar_free_plugin_settings() {
	$siteurl = site_url ();
	$config = get_option ( 'smack_wp_sugar_free_settings' );
	$config_field = get_option ( "smack_wp_sugar_free_field_settings" );

	$content = '<div style="width:95%">
			<div style="float:left">';
	
	if (! isset ( $config_field ['fieldlist'] )) {
		$content .= '<form class="sugar_free_left-side-content" id="smack_vtlc_form"
						method="post">';
	} else {
		$content .= '<form class="sugar_free_left-side-content" id="smack_vtlc_form" 
						action="' . $_SERVER ['REQUEST_URI'] . '" method="post">';
	}
                if(isset($_POST['Submit']) && $_POST['Submit'] == 'Save Settings'){ ?>
                        <script>
                        wpsugarfreesaveSettings();
                        </script>
                <?php }	
	$content .= '<input type="hidden" name="page_options" value="smack_vtlc_settings" />
					<input type="hidden" name="smack_vtlc_hidden" value="1" />
					<h2>SugarCRM Contact Form Settings</h2>
					<br />
					<div class="sugar-free-messageBox" id="message-box" style="display:none;" ><b>Settings Successfully Saved!</b></div>
					<h3>SugarCRM settings</h3>
					<div id="dbfields">
						<table>
							<tr>
								<td class="smack_sugar_free_settings_td_label"><label>SugarCrm URL</label></td>
								<td><input class="smack_sugar_free_settings_input_text" type="text" id="url"
									name="url" value="' . $config ['url'] . '" /></td>
							</tr>
							<tr>
								<td class="smack_sugar_free_settings_td_label"><label>User Name</label>
								</td>
								<td><input class="smack_sugar_free_settings_input_text" type="text" id="username"
									name="username" value="' . $config ['username'] . '" /></td>
							</tr>
							<tr>
								<td class="smack_sugar_free_settings_td_label"><label>Password</label>
								</td>
								<td><input class="smack_sugar_free_settings_input_text" type="password" id="password" onblur="enableTestSugarCredentials();" autocomplete="off"  
									name="password" /><br /></td>
							</tr>
						</table>
					</div>
					<table>
						<tr>
							<td class="smack_sugar_free_settings_td_label"><input type="button"
								class="button" value="Test connection" id="Test-Credentials"
								onclick="testCredentials(\'' . $siteurl . '\');" disabled=disabled /></td>
							<td id="smack-database-test-results"></td>
						</tr>
					
					</table>
					<div id=sugarsettings>
						<br />
						<h3>Capturing WordPress users</h3>
						<table>
							<tr>
								<td><br /> <label>
										<div style="float: left">Do you need to capture the registering
											users</div>
										<div style="float: right; padding-left: 5px;">:</div>
								</label></td>
								<td><br /> <input type="checkbox"
									class="smack-sugar-settings-user-capture"
									name="wp_sugar_free_smack_user_capture" id="wp_sugar_free_smack_user_capture"';
	
	if (isset($config ['wp_sugar_free_smack_user_capture']) && ($config ['wp_sugar_free_smack_user_capture'] == 'on')) {
		$content .= "checked";
	}
	$content .= '>
					</td>
					</tr>
					<!--<tr>
						<td>
							<div style="float: left">Sync WP members to SugarCRM contacts</div>
							<div style="float: right; padding-left: 5px;">:</div>
						</td>
						<td><input type="button" value="Sync"
							class="button-secondary submit-add-to-menu"
							onclick="captureAlreadyRegisteredUsersWpSugarFree();" />
							<div id="please-upgrade" style="position: absolute; z-index: 100;"></div>
						</td>
					</tr>-->
					
					</table>
					
					</div>
					<input type="hidden" name="posted" value="Posted">
					<p class="submit">
						<input name="Submit" type="submit" value="Save Settings" class="button-primary" />
					</p>
					<div id="vt_fields_container"></div>
					</form></div>

<div style="float:right;">
<!--
<p><h3>How To Configure WP-Suagr-Free in wordpress?</h3></p>
<iframe width="560" height="315" src="//www.youtube.com/embed/lX0evNGL5tc?list=PL2k3Ck1bFtbR7d8nRq-oc5iMDBm2ITWuX" frameborder="0" allowfullscreen></iframe>-->
</div>
</div>';
	echo $content;
//	echo rightSideContent ();
}

if (sizeof ( $_POST ) && isset ( $_POST ["smack_vtlc_hidden"] )) {
	$config = get_option( 'smack_wp_sugar_free_settings' );
	if(!is_array($config))
	{
		$config=Array();
	}
	foreach ( $fieldNames as $field => $value ) {
		if(isset($_POST[$field])){
			if($field != "password")
			{
				$config [$field] = $_POST [$field];
			}
			else
			{
				if($_POST['password'] != '')
				{
					$config [$field] = $_POST [$field];
				}
			}
		}
	}
	
	update_option ( 'smack_wp_sugar_free_settings', $config );
}

?>
