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

/**
 * Display an error message
 * @param string $message The message to be displayed
 */
function error($message) {
	# Generate HTML
	$html																= "
		<div class='error'>
			<string>ERROR</strong>
			 : 
			$message
		</div>
	";
	
	# Return HTML
	return $html;
}

/**
 * Display an information message
 * @param string $message The message to be displayed
 */
function info($message) {
	# Generate HTML
	$html																= "
		<div class='info'>
			$message
		</div>
	";
	
	# Return HTML
	return $html;
}

/**
 * Formats a decimal value as a currency in the format : 'R x, xxx.xx'
 * @param decimal $amount The decimal amount which to format.
 * @param string $prefix The prefix to the amount (eg: R, $, etc..).
 * @param string $align Alignment of the value. Default is 'right'
 * @param string $type Either 'html' or 'plain'
 * @return string
 */
function format_currency($amount, $prefix="R ", $align="right", $type="html"){
	# Format Currency in format : `R x, xxx.xx`
	return $prefix . number_format(sprintf("%01.2f", $amount), 2, '.', ',');
}

/**
 * This function takes a long string and splits up the
 * elements by splitting them up by spaces and \n characters.
 * The output is in the form of a one-dimensional array.
 *
 * @param string $long_string The string to tokenize
 * @return array
 */
function tokenize($long_string){
	# Set Variables
	$token																= 0;
	$x																	= 0;
	$long_string														= trim($long_string);
	
	# Parse String
	while (strstr($long_string, " ") || strstr($long_string, chr(10)) || strstr($long_string, chr(13))){
		if (substr($long_string, $x, 1) == " " || substr($long_string, $x, 1) == chr(13) || substr($long_string, $x, 1) == chr(10)){
			# Create the new token
			$tokens[$token] 											= substr($long_string, 0, $x);
			
			# Chop up the Long_String
			$long_string 												= trim(substr($long_string, ($x + 1), 1024));
			
			# Reset the character counter
			$token++;
			$x 															= 0;
		} else {
			$x++;
		}
	}
	
	# Get Remainders
	if (strlen($long_string) > 0){
		$tokens[$token] 												= $long_string;
	}
	
	# Return The Array of Tokens
	return $tokens;
}

/**
 * This function redirects the browser to a different URL.
 *
 * @param string $url
 */
function redirect($url) {
	print "<script>window.location.replace('$url');</script>\n";
	die();
}

/**
 * Debug Information
 */
function dd($var){
	var_dump($var);
	die();
}

function get_order_settings($order_prefix, $default_field="uid") {
	# Get Order Settings
	$order_field														= $default_field;
	$order_op															= "DESC";
	if (isset($_GET['order_field'])) {
		if (isset($_SESSION["{$order_prefix}_order_field"])) {
			if ($_SESSION["{$order_prefix}_order_field"] 				== $_GET["order_field"]) {
				if (isset($_SESSION["{$order_prefix}_order_op"])) {
					if ($_SESSION["{$order_prefix}_order_op"] 			== "DESC") {
						$order_op 										= "ASC";
					}
					else {
						$order_op 										= "DESC";
					}
				}
			}
		}
		else {
			$order_op													= "DESC";
		}
		$_SESSION["{$order_prefix}_order_field"] = $_GET['order_field'];
		$_SESSION["{$order_prefix}_order_op"] = $order_op;
	}
	if (isset($_SESSION["{$order_prefix}_order_field"])) {
		$order_field													= $_SESSION["{$order_prefix}_order_field"];
	}
	
	# Return Order Settings
	return array(	"field"	=> $order_field,
					"op"	=> $order_op
				);
}

function get_order_image($test_field, $order_settings) {
	if ($test_field 													== $order_settings['field']) {
		if ($order_settings['op'] 										== "DESC") {
			return "<img src='include/images/arrow_down.png' />";
		}
		else {
			return "<img src='include/images/arrow_up.png' />";
		}
	}
}

function generate_validation_js($field_data) {
	# Generate Javascript
	$js  																= "
		<script>
		function validate_required(field, alerttxt){
			with (field){
				if (value==null || value==''){
					alert(alerttxt);
					return false;
				}
				else {
					return true;
				}
			}
		}
		
		function validate_form(thisform) {
			proceed = 1;
			with(thisform){
				";
	$x 																	= 0;
	foreach ($field_data as $field => $errtxt){
		$js 															.= ($x)? "else " : "";
		$js 															.= "	if (validate_required($field, '$errtxt') == false) { proceed = 0; } \n";
		$x++;
	}
	$js 																.= "
			if(proceed == 1){
				thisform.submit();
			}
		}
	</script>";
	
	# Return JavaScript
	return $js;
}

function get_query_count($query) {
	# GLobal Variables
	global $_db;
	
	# Get Count
	$count_query														= substr($query, 0, strpos($query, "SELECT") + 7) . " COUNT(*) " . substr($query, strrpos($query, "FROM"));
	$num_records														= $_db->fetch_single($count_query);
	
	# Return Count
	return $num_records;
}

function paginated_listing($query, $this_page="", $prefix="") {
	# GLobal Variables
	global $_GLOBALS, $cur_page, $_db;
	
	# Local Variables
	$head 																= array();
	
	# Get Page Variables
	$page																= (isset($_GET[$prefix . 'results_page']))? $_GET[$prefix . 'results_page'] : 1;
	$p																	= (isset($_GET['p']))? $_GET['p'] : 'home';
	$this_page															= (strlen($this_page))? $this_page : "?p=" . $p;
	
	# Get Count
	$num_records														= get_query_count($query);
	$num_pages															= ceil($num_records / $_GLOBALS['max_results']);
	
	# Get Starting Record
	$starting_record													= ($page - 1) * $_GLOBALS['max_results'];
	
	# Get Body
	$data																= $_db->fetch($query . " LIMIT {$starting_record}, {$_GLOBALS['max_results']}");
	$body																= array();
	$row_num															= 0;
	foreach ($data as $item) {
		$item_arr														= get_object_vars($item);
		$body[$row_num]													= array();
		foreach ($item_arr as $key => $value) {
			$body[$row_num][]											= $value;
		}
		$row_num++;
	}
	
	# Generate Headings
	$obj_data															= get_object_vars($item);
	foreach ($obj_data as $item => $content) {
		$head[]															= $item;
	}
	
	# Generate Headings
	$headings															= "
		<tr>
	";
	foreach ($head as $item) {
		$headings														.= "
			<th>{$item}</th>
			";
	}
	$headings															.= "
		</tr>
	";
	
	# Generate Rows
	$rows																= "";
	foreach ($body as $row) {
		$rows															.= "
		<tr>
		";
		foreach ($row as $item) {
			$rows														.= "
			<td>{$item}</td>
			";
		}
		$rows															.= "
		</tr>
		";
	}
	
	# Output Page selection
	$page_select														= "";
	if ($num_records > $_GLOBALS['max_results']){
		$page_select 													.= "<script>\n";
		$page_select 													.= "	function gotoURL(me){\n";
		$page_select 													.= "		window.location.replace('$this_page&{$prefix}results_page=' + me.value);\n";
		$page_select 													.= "}\n";
		$page_select 													.= "</script>\n";
		$page_select 													.= "<div align='right' style='padding:0;margin:0;'>\n";
		$page_select 													.= "	Page : <SELECT name='results_pages' onchange='gotoURL(this);'>\n";
		for ($x = 0; $x < $num_pages; $x++){
			$selected													= ($page == ($x + 1))? " SELECTED" : "";
			$page_select 												.= "		<OPTION value='" . ($x + 1) . "'{$selected}>" . ($x + 1) . "</OPTION>\n";
		}
		$page_select 													.= "	</SELECT>\n";
		$page_select 													.= "</div>\n";
	}
	
	# Navigation Buttons
	$buttons															= "";
	if ($num_records > $_GLOBALS['max_results']){
		$previous_link													= ($page > 1)? "$this_page&$prefix" . "results_page=" . ($page - 1) : "";
		$next_link														= (($page * $_GLOBALS['max_results']) < $num_records)? "$this_page&$prefix" . "results_page=" . ($page + 1) : "";
		$buttons 														.= new_line() . nav_buttons($previous_link, $next_link);
	}
	
	# Generate HTML
	$html																= "
	{$page_select}
	
	<table class='table'>
		{$headings}
		{$rows}
	</table>
	
	{$buttons}
	";
	
	# Return HTML
	return $html;
}

/**
 * Generates the HTML of a table displaying a list of data with 
 * column headings.
 * @param array $headdings A one dimensional array with the column headings.
 * @param array $body A multidimensional array with the data to display.
 * @return string
 */
function results_table($header, $body, $prefix="", $page="", $max=0){
	# Global Variables
	global $_GLOBALS, $cur_page;
	
	# Set Current Page
	if (strlen($page) > 1){
		$this_page 														= $page;
	}
	else {
		$this_page 														= $cur_page;
	}
	
	# Set max_results
	if ($max 															== 0){
		$max_results 													= $_GLOBALS['max_results'];
	}
	else {
		$max_results 													= $max;
	}
	
	# Get Current Results Page
	if(isset($_GET[$prefix . 'results_page'])){
		$results_page 													= $_GET[$prefix . 'results_page'];
	}
	else{
		$results_page 													= 1;
	}
	
	# Calculate Number of Pages
	if ($max_results > 0){
		$num_results_pages 												= ceil(sizeof($body) / $max_results);
	}
	else {
		$num_results_pages 												= 1;
	}
	
	# Get Starting index
	if ($results_page 													== 1){
		$start_index 													= 0;
	}
	else {
		$start_index 													= ($results_page * $max_results) - $max_results + 1;
	}
	
	# Get Ending index
	if ($results_page 													== 1){
		$end_index 														= $start_index + $max_results;
	}
	else {
		$end_index 														= $start_index + $max_results - 1;
	}
	
	# --- Construct HTML ---
	$html 																= "";
	
	# Output Page selection
	if ($num_results_pages > 1){
		$html 															.= "<script>\n";
		$html 															.= "	function gotoURL(me){\n";
		$html 															.= "		window.location.replace('$this_page&" . $prefix . "results_page=' + me.value);\n";
		$html 															.= "}\n";
		$html 															.= "</script>\n";
		$html 															.= "<div align='right' style='padding:0;margin:0;'>\n";
		$html 															.= "	Page : <SELECT name='results_pages' onchange='gotoURL(this);'>\n";
		for ($x = 0; $x < $num_results_pages; $x++){
			$html 														.= "		<OPTION value='" . ($x + 1) . "'";
			if ($results_page											== ($x + 1)){
				$html 													.= " SELECTED";
			}
			$html 														.= ">" . ($x + 1) . "</OPTION>\n";
		}
		$html 															.= "	</SELECT>\n";
		$html 															.= "</div>\n";
	}
	
	# Headings
	$html 																.= "<table width='100%' cellspacing='0' cellpadding='3' border='0'>\n";
	if (sizeof($header) > 0){
		$html 															.= "	<tr>\n";
		for ($x = 0; $x < sizeof($header); $x++){
			$html 														.= "		<td>" . $header[$x] . "</td>\n";
		}
		$html 															.= "	</tr>\n";
	}
	# Body
	if (sizeof($body) > 0){
		$cur_row 														= $start_index;
		while (isset($body[$cur_row])){
			$row 														= $body[$cur_row];
			if ($cur_row < ($end_index + 1)){
				$html 													.= "	<tr>\n";
				foreach ($row as $cell){
					$html 												.= "		<td>" . $cell . "</td>\n";
				}
				$html 													.= "	<tr>\n";
			}
			$cur_row++;
		}
	}
	else {
		$html 															.= "	<tr><td>No Data To Display</td></tr>";
	}
	$html 																.= "</table>\n";
	
	# -- Navigation Buttons ---
	if (sizeof($body) > $max_results){
		# Previous Link
		if ($results_page > 1){
			$previoius_link 										= "$this_page&$prefix" . "results_page=" . ($results_page - 1);
		}
		else {
			$previoius_link 										= "";
		}
		
		# Next Link
		if (sizeof($body) > $end_index){
			$next_link 												= "$this_page&$prefix" . "results_page=" . ($results_page + 1);
		}
		else {
			$next_link 												= "";
		}
		$html 														.= new_line() . nav_buttons($previoius_link, $next_link);
	}
	
	# Return HTML
	return $html;
}

/**
 * Returns the HTML for a next button
 * @param string $link The URL the button will link to.
 * @return string
 */
function next_button($link){
	$button 															= "<a href='$link'><img src='img/next.gif' border='0' height='35' width='97'></a>";
	return $button;
}

/**
 * Returns the HTML for a previous button
 * @param string $link The URL the button will link to.
 * @return string
 */
function previous_button($link){
	$button 															= "<a href='$link'><img src='img/previous.gif' border='0' height='35' width='100'></a>";
	return $button;
}

/**
 * Generates the HTML for a pair of navigation buttons; Next and Previous.
 * @param string $link_next The URL for the Next Button
 * @param string $link_previous The URL for the Previous Button
 * @param string $align The alignment of the nav buttons. default = 'center'
 * @return string
 */
function nav_buttons($link_previous, $link_next, $align="center"){
	$html 																= "<table align='center'><tr>";
	if ($link_previous){
		$html 															.= "<td>" . previous_button($link_previous) . "</td>";
	}
	if ($link_next){
		$html 															.= "<td>" . next_button($link_next) . "</td>";
	}
	$html 																.= "</tr></table>";
	return $html;
}

/**
 * Prints out a new line / br tag
 * @param Integer $num The 
 */
function new_line($num = 1){
	for ($x = 0; $x < $num; $x++){
		print "<br />\n";
	}
}

function date_select($name="date", $default="", $min_year=1950, $max_year=2050, $only_month=0) {
	# Generate HTML
	$html 																= "<input id='$name' name='{$name}' class='date' type='text' value='{$default}' style='width: 100px'>";
	
	# Return HTML
	return $html;
}

function time_select($name="time", $default="") {
        # Get Default
        $default                										= ($default)? $default : date("H:iA");
                
        # Construct HTML
        $html                  											.= "<input type='text' class='time' id='$name' name='$name' value='$default' />";
        
        # Return HTML
        return $html;
}

function yesno($input) {
	return ($input)? "Yes" : "No";
}

function select_box($name, $items) {
	# Construct HTML
	$html  																= "<select name='$name'>\n";
	foreach ($items as $item) {
		$html 															.= "	<option value=\"{$item}\">{$item}</option>\n";
	}
	$html 																.= "</select>\n";
	
	# Return HTML
	return $html;
}

function format_string($strText){
	$len 																= strlen($strText);
	
	# --- Replace Line Breaks with <BR> Tags ---
	while (strpos($strText, chr(10)) > 0){
		$pos 															= strpos($strText, chr(10));
		$tmp 															= substr($strText, 0, ($pos));
		$tmp 															.= "<BR>" . substr($strText, ($pos + 1), 1024);
		$strText 														= $tmp;
	}
	
	# --- Replace Carriage Return with <BR> Tags ---
	while (strpos($strText, chr(13)) > 0){
		$pos 															= strpos($strText, chr(13));
		$tmp 															= substr($strText, 0, ($pos));
		$tmp 															.= "<br />" . substr($strText, ($pos + 1), 1024);
		$strText 														= $tmp;
	}
	
	# --- Return Formatted String ---
	return $strText;
}

function select_all_none($form_id) {
	# Generate HTML
	$html  																= "<div class='select_all_none'>\n";
	$html 																.= "	<a><span style='cursor:pointer;' onClick='set_all_checkboxes(\"$form_id\", 1);'>Select All</span></a>\n";
	$html 																.= "	/\n";
	$html 																.= "	<a><span style='cursor:pointer;' onClick='set_all_checkboxes(\"$form_id\", 0);'>Deselect All</span></a>\n";
	$html 																.= "</div><!-- END: Select All / None -->\n";
	
	# Return HTML
	return $html;
}

function number_select($name, $min, $max, $default=0) {
	# Generage HTML
	$html  																= "<select name='$name'>\n";
	for ($x = $min; $x < ($max + 1); $x++) {
		$selected 														= ($x == $default)? " SELECTED" : "";
		$html 															.= "	<option value='$x'{$selected}>$x</option>\n";
	}
	$html 																.= "</select>\n";
	
	# Retrun HTML
	return $html;
}

function wysiwyg_editor($name, $content, $width=500, $height=256) {
	# Construct HTML
	$html  																= "<div class='box' style='width:{$width}px;height:{$height}px;'>\n";
	$html 																.= "	<textarea id='wysiwyg' name='$name' rows='11' cols='69'>$content</textarea>\n";
	$html 																.= "</div>\n";
	
	# Return HTML
	return $html;
}

function format_date_readable($date, $time=0, $format="F Y"){
	# Break up data
	$year																= substr($date, 0, 4);
	$month																= substr($date, 5, 2);
	$day																= substr($date, 8, 2);
	$hour																= ($time)? substr($time, 0, 2) : 0;
	$minutes															= ($time)? substr($time, 3, 2) : 0;
	$seconds															= ($time)? substr($time, 6, 2) : 0;
	
	# Recompile
	$date																= (strstr($date, "-"))? date($format, mktime($hour, $minutes, $seconds, $month, $day, $year)) : $date;
	
	# Return Date
	return $date;
}

function get_attachment_icon($filename) {
	# Get File Extension
	$filename															= explode(".", $filename);
	$extension															= $filename[1];
	
	# Get Attachment Image
	switch($extension) {
		case "xls":
			$extension_img												= "icon_xls.gif";
			break;
		case "doc":
			$extension_img												= "icon_doc.gif";
			break;
		case "pdf":
			$extension_img												= "icon_pdf.gif";
			break;
		case "jpg":
			$extension_img												= "icon_img.gif";
			break;
		case "jpeg":
			$extension_img												= "icon_img.gif";
			break;
		case "gif":
			$extension_img												= "icon_img.gif";
			break;
		case "bmp":
			$extension_img												= "icon_img.gif";
			break;
		case "png":
			$extension_img												= "icon_img.gif";
			break;
		default: 
			$extension_img												= "icon_img.gif";
	}
	
	# Return Image
	return "<img src='include/images/icons/{$extension_img}' />";
}

function truncate($string, $chars) {
	if (strlen($string) > $chars) {
		$string 														= substr($string, 0, ($chars - 3)) . "...";
	}
	return $string;
}

function bullet_points($text, $ol=0) {
	# Compose HTML
	$html																= ($ol)? "<ol>\n" : "<ul>\n";
	$text																= str_replace("<br />", "\n", $text);
	$points																= explode("\n", $text);
	foreach ($points as $point){
	        if (strlen(trim($point))) {
		        $html 													.= "	<li>$point</li>\n";
		}
	}
	$html																.= ($ol)? "</ol>\n" : "</ul>\n";
	
	# Return HTML
	return $html;
}

function generate_select($name, $values, $active="") {
	# Construct HTML
	$html  																= "<select name='{$name}'>\n";
	$use_key															= (is_array($values[0]))? true : false;
	foreach ($values as $key => $value) {
		$key															= ($use_key)? $key : $value;
		$checked 														= ($key == $active)? " SELECTED" : "";
		$html 															.= "	<option value='$key'{$checked}>$value</option>\n";
	}
	$html 																.= "</select>\n";
	
	# Return HTML
	return $html;
}

function yes_no_select($name="", $active=0) {
	# Generate Values
	$values																= array(	0	=> "No",
																					1	=> "Yes");
	
	# Get HTML
	$html																= generate_select($name, $values, $active);
	
	# Return HTML
	return $html;
}

function tabbed_page($tab_data, $id="tabs1") {
	# Get Current Active Tab
	$active_tab															= 0;
	if (isset($_SESSION["tab_" . $_GET['p']][$_GET['id']])) {
		$active_tab														= $_SESSION["tab_" . $_GET['p']][$_GET['id']];
	} 
	
	# Generate Headers and Body Areas
	$tab_headers														= "";
	$tab_sections														= "";
	$js_reset															= "";
	$x																	= 0;
	foreach ($tab_data as $title => $contents) {
		$default_class													= ($x == $active_tab)? "active" : "inactive";
		$tab_headers													.= "
			<div class='tab_heading' onclick='{$id}_activate_tab(\"{$id}_$x\")'>
				{$title}
			</div>
		";
		$tab_sections													.= "
			<div class='tab_body_{$default_class}' id=\"{$id}_$x\">
				{$contents}
			</div>
		";
		$js_reset														.= "
		var cur_class = document.getElementById(\"{$id}_$x\").className;
		if (cur_class == \"tab_body_inactive\" && active_tab == \"{$id}_$x\") {
			document.getElementById(\"{$id}_$x\").className = \"tab_body_active\";
		}
		else  if (!(active_tab == \"{$id}_$x\")) {
			document.getElementById(\"{$id}_$x\").className = \"tab_body_inactive\";
		}
		";
		$x++;
	}
	
	# Generate Javascript
	$js																	= "
	function {$id}_activate_tab(active_tab) {
		ajax_get_data(\"include/content/ajax.php?action=set_page_tab&p={$_GET['p']}&id={$_GET['id']}&tab=\" + active_tab.substring(" . strlen($id . "_") . "));
		$js_reset
	}
	";
	
	# Generate HTML
	$html																= "
	<!-- Javascript -->
	<script>
		{$js}
	</script>
	
	<!-- Tab Wrapper {$id} -->
	<div class='tab_wrapper' id=\"{$id}\">
		<!-- Tab Head -->
		<div class='tab_head'>
			{$tab_headers}
		</div><!-- END: Tab Head -->
		
		<!-- Tab Body -->
		<div class='tab_body'>
			{$tab_sections}
		</div><!-- END: Tab Body -->
	</div><!-- END: Tab Wrapper {$id} -->
	";
	
	# Return HTML
	return $html;
}

function upload_form($item) {
	# Global Variables
	global $cur_page, $_db;
	
	# Generate HTML
	$html																= "
	<form enctype='multipart/form-data' action='#' method='POST'>
		<input type='hidden' name='item' value=\"{$item}\" />
		
		<!-- File -->
		Add File : <input name='new_file' type='file' /><br />
		
		<!-- Name -->
		Name : <input type='text' name='name' /><br />
		
		<input type='submit' value='Upload' />
	</form>
	";
	
	# Return HTML
	return $html;
}

function comments_page($item) {
	# Construct HTML
	$html  																= "
		<h3>Comments</h3>
		
		<!-- Comment Form -->
		<form method='POST' action='{$_SERVER['REQUEST_URI']}&subaction=save_comment'>
			<input type='hidden' name='item' value='{$item}'>
			<textarea name='comment' cols='40' rows='4'></textarea><br />
			<input type='submit' value='Save'>
		</form>
		
		<!-- Comment Listing -->
		" . get_comments($item) . "
		<!-- END: Comment Listing -->
		
		";
	
	# Return HTML
	return $html;
}

function get_comments($item) {
	# Global Variables
	global $_db;
	
	# Get Comments
	$query																= "	SELECT
																				*
																			FROM
																				`comments`
																			WHERE
																				`item` = '$item'
																			ORDER BY
																				`datetime` DESC";
	$comments															= $_db->fetch($query);
	
	# Compile HTML
	$html  																= "<br />\n";
	if (sizeof($comments)) {
		foreach ($comments as $comment) {
			$html 														.= "
				<!-- Comment -->
				<div class='comment'>
					{$comment->comment}
					<br />
					<b style='font-size:10px;color:#999999;'>
						" . user_get_name($comment->user) . "
					</b>
					<div style='font-size:10px;color:#999999;'>
						{$comment->datetime}
					</div>
				</div><!-- END: Comment -->
				";
		}
	}
	else {
		$html 															.= "No Comments<br />\n";
	}
	
	# Return HTML
	return $html;
}

function attachments_page($item) {
	# Construct HTML
	$html  																= "<h3>Attachments</h3>\n";
	$html  																.= upload_form($item);
	$html  																.= "<br /><br />\n";
	$html  																.= view_files($item);
	
	# Return HTML
	return $html;
}

function view_files($item) {
	# Global Variables
	global $_GLOBALS;
	
	# Get Listing of Files
	$files  															= get_files($item);
	
	# Construct HTML
	if (sizeof($files)){
		$html 															= "<table class='results_table'>\n";
		$html 															.= "	<tr>\n";
		$html 															.= "		<th>File</th>\n";
		$html 															.= "		<th>Date</th>\n";
		$html 															.= "		<th>User</th>\n";
		$html 															.= "		<th>&nbsp;</th>\n";
		$html 															.= "	</tr>\n";
		foreach ($files as $file) {
		        $delete_link 											= "<a href='{$_SERVER['REQUEST_URI']}&subaction=delete_file&file={$file->uid}&file_item={$item}' style='color:red;font-size:10px;'>x</a>";
				$file_name												= ($file->name)? $file->name : $file->file;
				$html 													.= "	<tr><td><a href='{$_GLOBALS['upload_url']}{$file->file}' target='_blank'>{$file_name}</a></td><td>{$file->datetime}</td><td>" . user_get_name($file->user) . "</td><td>{$delete_link}</td></tr>\n";
		}
		$html 															.= "</table>\n";
	}
	else {
		$html															= "<div class='info'>There are no files for this item.</div>\n";
	}
	
	# Return HTML
	return $html;
}

# =========================================================================
# THE END
# =========================================================================

?>
