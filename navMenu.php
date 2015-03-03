<?php
global $wp_sugar_free_menus;
$wp_sugar_free_menus = array (
		'wp_sugar_free_fields' => __ ( 'Lead Form Fields' ),
		'wp_sugar_free_widget_fields' => __ ( 'Widget Form Fields' ),
		'wp_sugar_free_capture_wp_users' => __ ( 'Sync WP Users' ),
		'wp_sugar_free_plugin_settings' => __ ( 'Settings' ),
		'wp_sugar_free_listShortcodes' => __ ( 'List Shortcodes' )
);

function wpsugarfree_topnavmenu() {

	global $wp_sugar_free_menus;
	$class = "";
	$top_nav_menu = '<div class="update-message" style="text-align:center;">Please migrate to our new plugin <a href="https://wordpress.org/plugins/wp-leads-builder-any-crm/" target="blank">Leads Builder For Any CRM</a> for advanced features.</div>';
        $top_nav_menu.= "<div class='nav-pills-div'>";
        $top_nav_menu.= '<ul class="nav nav-pills">';
        $top_nav_menu.= '       <ul class="nav nav-tabs">';
        if(is_array ( $wp_sugar_free_menus )){
                foreach( $wp_sugar_free_menus as $links => $text ) {
                        if (! isset ( $_REQUEST ['action'] ) && ($links == "wp_sugar_free_plugin_settings")) {
                                $class = 'active';
                        }
                        elseif (isset( $_REQUEST['action'] ) && ($_REQUEST ['action'] == $links)) {
                                $class = "active";
                        }
                        $top_nav_menu.= '<li class = "'.$class.'"> <a href="?page=wp-sugar-free&action='.$links.'" class = "saio_nav_smartbot">'.$text.'</a> </li>';
                        $class="";
                }
        }
        $top_nav_menu.='        </ul>
                        </ul>';
	$top_nav_menu.='</div>';
        return $top_nav_menu;
}

function getActionWpSugarFree()
{
        if(isset($_REQUEST['action']))
        {
                $action = $_REQUEST['action'];
        }
        else
        {
                $action = 'wp_sugar_free_plugin_settings';
        }
        return $action;
}

function wpsugarfree_displaySettings()
{
        echo "<h3>Please save the settings first</h3>";
}

?>
