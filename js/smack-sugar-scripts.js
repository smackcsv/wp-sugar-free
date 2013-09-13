function smack_validate_Fields() {
	var no_of_fields = jQuery('#no_of_vt_fields').val();
	for ( var i = 0; i < no_of_fields; i++) {
		if (jQuery('#field_type' + i).val() == 'M'
				&& !jQuery('#smack_wp_sugar_free_field' + i).is(':checked')) {
			alert(jQuery('#field_label' + i).val() + ' is mandatory');
			try {
				jQuery('#smack_wp_sugar_free_field' + i).focus();
			} catch (e) {
			}
			return false;
		}
	}
	return true;
}

function captureAlreadyRegisteredUsersWpSugarFree() {
	document.getElementById('please-upgrade').style.fontSize = "14px";
	document.getElementById('please-upgrade').style.fontFamily = "Sans Serif";
	document.getElementById('please-upgrade').style.color = "red";
	document.getElementById('please-upgrade').innerHTML = "Please Upgrade to WP-Sugar-Pro for Sync feature";
}

function testCredentials(siteurl) {
/*	var data = "";
	data += "url=" + jQuery("#url").val();
	data += "&username=" + jQuery("#username").val();
	data += "&password=" + jQuery("#password").val();
	data += "&check=testaccess";
*/
	jQuery.ajax({
		type: 'POST',
		url: ajaxurl,
		data: {
		    'action'   : 'testAccess',
		    'url'      : jQuery("#url").val(),
		    'username' : jQuery("#username").val(),
		    'password' : jQuery("#password").val(),
		    'check'    : "testaccess",
		},
		success:function(data) {
			if (data.indexOf("Success") != -1) {
				document.getElementById('smack-database-test-results').style.fontWeight = "bold";
				document.getElementById('smack-database-test-results').style.color = "green";
				document.getElementById('smack-database-test-results').innerHTML = "Connection successful";
			} else {
				document.getElementById('smack-database-test-results').style.fontWeight = "bold";
				document.getElementById('smack-database-test-results').style.color = "red";
				document.getElementById('smack-database-test-results').innerHTML = "Credentials are wrong";
			}
		},
		error: function(errorThrown){
		    console.log(errorThrown);
		}
	});
}

function wpsugarfreeupgradetopro() {
	window.setTimeout("wpsugarfreeshowmessage()", 100);
	window.setTimeout("wpsugarfreehidemessage()", 5000);
}

function wpsugarfreemove(val) {
	window.setTimeout("wpsugarfreeshowmessage()", 100);
	window.setTimeout("wpsugarfreehidemessage()", 5000);
}

function wpsugarfreeshowmessage() {
	document.getElementById('upgradetopro').style.display = "";
}

function wpsugarfreehidemessage() {
	document.getElementById('upgradetopro').style.display = "none";
	document.getElementById('skipduplicate').checked = false;
	document.getElementById('generateshortcode').checked = false;
}

function wpsugarfreeselect_allfields(formid, module) {
	var i;
	var data = "";
	var form = document.getElementById(formid);
	var chkall = form.elements['selectall'];
	var chkBx_count = form.elements['no_of_vt_fields'].value;
	if (chkall.checked == true) {
		for (i = 0; i < chkBx_count; i++) {
			if (document.getElementById('smack_wp_sugar_free_field' + i).disabled == false)
				document.getElementById('smack_wp_sugar_free_field' + i).checked = true;
		}
	} else {
		for (i = 0; i < chkBx_count; i++) {
			if (document.getElementById('smack_wp_sugar_free_field' + i).disabled == false)
				document.getElementById('smack_wp_sugar_free_field' + i).checked = false;
		}
	}
}

function wpsugarfreesaveSettings(){
        window.setTimeout("wpsugarfreeshowSuccessMessage()", 100);
        window.setTimeout("wpsugarfreehideSuccessMessage()", 10000);
}

function wpsugarfreeshowSuccessMessage(){
        document.getElementById('message-box').style.display = '';
}

function wpsugarfreehideSuccessMessage(){
        document.getElementById('message-box').style.display = 'none';
}

