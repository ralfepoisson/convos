<?php
/**
 * Project
 * 
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 1.0
 * @package Project
 */

# =========================================================================
# FUNCTIONS
# =========================================================================

function template_top() {
	# Global Variables
	global $cur_page, $_db, $_GLOBALS;
	
	# Set Error Message
	$error_message														= "";
	if (isset($_SESSION['error_message'])) {
		$error_message													= error($_SESSION['error_message']);
		unset($_SESSION['error_message']);
	}
	
	# Set Info Message
	$info_message														= "";
	if (isset($_SESSION['info_message'])) {
		$info_message													= info($_SESSION['info_message']);
		unset($_SESSION['info_message']);
	}
	
	# Generate HTML
	$html  																= "<!DOCTYPE html>
	<html>
		<head>
			<!-- Title -->
			<title>{$_GLOBALS['title']}</title>
			
			<!-- CSS -->
			<link href='include/css/master.css' rel='stylesheet' type='text/css' />
			
			<!-- Special JS -->
			<script lang='javascript' src='include/scripts/jquery.js'></script>
			<script lang='javascript' src='include/scripts/include.js'></script>
		</head>
		<body>
		
		<!-- Javascript -->
		<script lang='javascript'>get_submenu(\"" . get_current_page_parent() . "\")</script>		

		<!-- Page Wrapper -->
		<div class='page_wrapper'>
			
			<!-- Header -->
			<div class='page_header'>
				<!-- Logo -->
				<div class='logo'>
					<a href='./'>
						<img src='include/images/logo.png' border='0' />
					</a>
				</div><!-- END: Logo -->
				
				<!-- Main Menu -->
				" . generate_xml_menu() . "
										
			</div><!-- END: Header -->
			
			<!-- Content -->
			<div class='content'>
				{$error_message}
				{$info_message}
	";
	
	# Return HTML
	return $html;
}

function template_bottom() {
	# Global Variables
	global $cur_page, $_db;
	
	# Generate HTML
	$html = "
			</div><!-- END: Content -->
			
			<!-- Footer -->
			<div class='page_footer'>
				<img src='include/images/logo_small.png' alt='Imply I.T.'/>
				<br />
				<br />
				Copyright &copy; Imply I.T. " . date("Y") . "
			</div><!-- END: Footer -->
			
		</div><!-- END: Page Wrapper -->
		
	</body>
	</html>";
	
	# Return HTML
	return $html;
}

function menu() {
	# Generate Menu Items
	$menu_items						= "";
	// Home
	$menu_items						.= (has_authority("home"))? "
			<!-- Menu Item -->
				<span class='menu_item'><a href='?p=home'>Home</a></span>
			<!-- END: Menu Item -->
	" : "";
	// Admin
	$menu_items						.= (has_authority("admin_menu"))? "
			<!-- Menu Item -->
				<span class='link' class='menu_item' onclick='get_submenu(\"admin\")'>
					Admin
				</span>
			<!-- END: Menu Item -->
	" : "";
		
	# Generate HTML
	$html = "
	<!-- Nav Menu -->
	<div class='main_menu'>

		<!-- User Details -->
		<div class='user_details'>
			Logged in as: <strong>" . user_get_name(get_user_uid()) . "</strong>
			<a href='?p=logout'>Logout</a>
		</div>
		<!-- END: User Details -->

		<!-- Nav  -->
		<div class='main_menu_items'>
			{$menu_items}
		</div>	
	
	</div>
	<!-- END: Main Menu -->

	<!-- Sub Menu -->
		<div class='submenu' id='submenu'>
			<!-- Sub Menu -->
		</div>
	<!-- END: Sub Menu -->

	
	";
	
	# Return HTML
	return $html;
}

function submenu($parent) {
	# Global Variables
	global $_GLOBALS;
	
	# Get Submenu
	$submenu				= $_GLOBALS['menu'][$parent];
	
	# Generate Submenu Items
	$submenu_items			= "";
	if ($submenu) {
		foreach ($submenu as $text => $details) {
			$submenu_items	.= (has_authority($details['auth']))? "
			<!-- Submenu Item -->
			<div class='submenu_item'>
				&#62&nbsp<a href='?p={$details['page']}'>{$text}</a>
			</div><!-- END: Submenu Item -->
			" : "";
		}
	}
	
	# Generate HTML
	$html					= "
		<!-- Submenu Items -->
		<div class='submenu_items'>
			{$submenu_items}
		</div><!-- END: Submenu Items -->
	";
	
	# Return HTML
	return $html;
}

function get_current_page_parent() {
	# Global Variables
	global $cur_page, $_db, $_GLOBALS;
	
	# Get page
	$page					= (isset($_GET['p'])) ? $_GET['p'] : $_GLOBALS['default_page'];
	
	# Get Parent
	foreach ($_GLOBALS['menu'] as $parent => $submenu) {
		foreach ($submenu as $text => $page_data) {
			if ($page_data['page'] == $page) {
				return $parent;
			}
		}
	}
	
	# Return Default Value
	return "home";
}

function button($text, $link, $align="right", $action="href", $tag="a", $tag_class="link") {
	# Generate HTML
	//$float							= ($align == "right")? " float: $align;" : "";
	$html 							= "
	<!-- Button -->
	<!-- <div style='height: 50px;{$float}'> -->
		<{$tag} class='{$tag_class}' {$action}='{$link}'>
			<div class='main_button'>
				{$text}
			</div>
		</{$tag}>
	<!-- </div> -->
	<!-- END: Button -->
	";
	# Return HTML
	return $html;
}

function generate_xml_menu() {
	# Global Variables
	global $_db, $_GLOBALS;
	
	# Get Menu XML
	$xml_file															= dirname(dirname(__FILE__)) . "/config/menu.xml";
	$xml																= simplexml_load_file($xml_file);
	
	# Generate Menu Items
	$items																= "";
	foreach ($xml as $item) {
		$items															.= xml_menu_item_html($item);
	}
	
	# Generate HTML
	$html																= "
		<!-- Main Menu -->
		<ul id='menu'>
			{$items}
		</ul>
	";
	
	# Return HTML
	return $html;
}

function xml_menu_item_html($item) {
	# Get Children HTML
	$children															= "";
	if (isset($item->item)) {
		if (isset($item->item->item)) {
			foreach ($item->item as $child) {
				$children												.= xml_menu_item_html($child);
			}
		}
		else {
			$children													.= xml_menu_item_html($item->item);
		}
	}
	$children															= (strlen($children))? "<ul>{$children}</ul>" : "";
	
	# Generate HTML
	$html																= "
		<li>
			<a href='{$item['link']}'>{$item['label']}</a>
			{$children}
		</li>
	";
	
	# Return HTML
	return $html;
}

# =========================================================================
# THE END
# =========================================================================

?>