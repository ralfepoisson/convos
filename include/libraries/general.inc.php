<?php
/**
 * Project
 * 
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 2.0
 * @package Project
 */

# =========================================================================
# FUNCTIONS
# =========================================================================

function logg($message, $type="NORMAL"){
	# Global variables
	global $_GLOBALS;
	
	# Get Log File
	$log_file															= (isset($_GLOBALS['log_file']))? $_GLOBALS['log_file'] : "/var/log/project.log";
	
	# Open File For Writing
	$f																	= fopen($log_file, 'a');
	
	# Write Log Message
	fputs($f, date("Y-m-d H:i:s") . " [" . get_user_username() . "] " . $type . " " . $message . "\n");
	
	# Close File
	fclose($f);
}

function get_user_username() {
	return (isset($_SESSION['user_username']))? $_SESSION['user_username'] : "System";
}

function is_mobile_browser() {
	$mobile_browser = '0';
	
	# Handle Common Mobile Browsers
	if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
		$mobile_browser++;
	}
	
	# Handle Mobile Browser Profiles
	if((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
		$mobile_browser++;
	}    
	
	# Handle Custom Mobile Browsers
	$mobile_ua 															= strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
	$mobile_agents 														= array('w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
																				'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
																				'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
																				'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
																				'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
																				'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
																				'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
																				'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
																				'wapr','webc','winw','winw','xda','xda-');
	if(in_array($mobile_ua,$mobile_agents)) {
		$mobile_browser++;
	}
	
	# Handle Opera Mini Browser
	if (strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini')>0) {
		$mobile_browser++;
	}
	
	# Handle Microsoft Browsers
	if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows')>0) {
		$mobile_browser 												= 0;
	}
	
	# Return Result
	return ($mobile_browser > 0)? true : false;
}

function upload($item, $file, $name) {
	# Global Variables
	global $_GLOBALS, $_db;
	
	# Check file exists
	if (strlen($file['tmp_name'])){
		# Security Checks
		if (!(
			strstr($file['name'], ".exe") ||
			strstr($file['name'], ".php") ||
			strstr($file['name'], ".pl") ||
			strstr($file['name'], ".py") || 
			strstr($file['name'], ".sh") ||
			strstr($file['name'], ".html")
		)) {
			# Upload File
			$destination 												= $item . str_replace(" ", "_", $file["name"]);
			if (!(copy($file["tmp_name"], $_GLOBALS['upload_dir'] . $destination))){
				error("<b>Upload Error : </b>Could not upload file. {$file['tmp_name']} -> {$_GLOBALS['upload_dir']}{$destination}.");
				logg ("File upload Error : Could not upload file. {$file['tmp_name']} -> {$_GLOBALS['upload_dir']}{$destination}.");
			}
	
			# Add to database
			$_db->insert(
				"files",
				array(
					"item"												=> $item,
					"file"												=> $destination,
					"datetime"											=> date("Y-m-d H:i:s"),
					"name"												=> $name,
					"user"												=> get_user_uid()
				)
			);
			
			# Log Activity
			logg ("File has been Uploaded : " . $destination . " : By " . user_get_name(get_user_uid()));
		}
		else {
			# Log Security Alert
			logg ("SECURITY ALERT : " . user_get_email(get_user_uid()) . " is trying to upload an illegal file ( {$file['name']} ).");
		}
	}
}

function get_files($item) {
	# Global Variables
	global $_GLOBALS, $_db;
	
	# Get List of Files for Item
	$query																= "	SELECT
																				*
																			FROM
																				`files`
																			WHERE
																				`item` = \"{$item}\"
																				AND `active` = 1";
	$files																= $_db->fetch($query);
	
	# Return File List
	return $files;
}

function get_next_day($date) {
	# Split Variables
	$year																= substr($date, 0, 4);
	$month																= substr($date, 5, 2);
	$day																= substr($date, 8, 2);
	
	# Return Next Day
	return date("Y-m-d", mktime(0, 0, 0, $month, $day + 1, $year));
}

function get_previous_day($date) {
	# Split Variables
	$year																= substr($date, 0, 4);
	$month																= substr($date, 5, 2);
	$day																= substr($date, 8, 2);
	
	# Return Next Day
	return date("Y-m-d", mktime(0, 0, 0, $month, $day + 1, $year));
}

function get_unix_timestamp($date) {
	# Split Variables
	$year																= substr($date, 0, 4);
	$month																= substr($date, 5, 2);
	$day																= substr($date, 8, 2);
	
	# Return Next Day
	return date("U", mktime(0, 0, 0, $month, $day + 1, $year));
}

function working_days($start_date, $work_days) {
	# Local Variables
	$x																	= 0;
	$end_date															= $start_date;
	
	# Get End Date
	while ($x < $work_days) {
		$end_date														= get_next_day($end_date);
		$x																= (is_public_holiday($end_date))? $x : $x + 1;
	}
	
	# Return End Date
	return $end_date;
}

function rand_str($length = 32, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890') {
    # Length of character list
    $chars_length 														= (strlen($chars) - 1);

    # Start our string
    $string 															= $chars{rand(0, $chars_length)};
   
    # Generate random string
    for ($i = 1; $i < $length; $i = strlen($string)) {
        # Grab a random character from our list
        $r 																= $chars{rand(0, $chars_length)};
       
        # Make sure the same two characters don't appear next to each other
        if ($r != $string{$i - 1}) $string .=  $r;
    }
   
    # Return the string
    return $string;
}

function on_error($num, $str, $file, $line) {
	# Global Variables
	global $_GLOBALS;
	
	# Display Standard Error Message
	print error("Oops! We encountered a problem. An email has been sent to the administrators informing them of the issue. Click <a href='./'>Here</a> to continue.");
	
	# Compile Error Message
	$error_message 														= "Encountered error $num in $file, line $line: $str";
	
	# Send Email
	mail($_GLOBALS['dev_email'], "{$error_message}\n\nStack Trace: \n\n" . print_r(debug_backtrace(), 1));
	
	# Log the Error
	logg($error_message, "DEBUG");
}

function get_next_workday($this_date) {
	$day																= date("N", mktime(0, 0, 0, substr($this_date, 5, 2), substr($this_date, 8, 2), substr($this_date, 0, 4)));
	if ($day > 5) {
		$days															= ($day == 6)? 2 : 1;
		$this_date														= date("Y-m-d", mktime(0, 0, 0, substr($this_date, 5, 2), substr($this_date, 8, 2) + $days, substr($this_date, 0, 4)));
	}
	return $this_date;
}

function format_db_time($time) {
	# Split up time
	$hour																= substr($time, 0, 2);
	$minutes															= substr($time, 3, 2);
	$period																= trim(substr($time, 5));
	
	# Update Hour
	$hour																=  (strtoupper($period) == "PM" && !($hour == 12))? $hour + 12 : $hour;
	
	# Return Formatted Time
	return $hour . ":" . $minutes . ":00";
}

function remove_array_element($array, $str) {
	foreach ($array as $key => $value) {
		if ($array[$key] == "$str") {
			unset($array[$key]);
		}
	}
	return $array;
}

function is_checked($val) {
	return ($val)? " CHECKED" : "";
}

function html_email($to, $subject, $text, $html, $from) {
	# Log Activity
	logg ("Email: Sending email to $to with subject `$subject`.");
	
	# Include Required Libraries
	include_once('Mail.php');
	include_once('Mail/mime.php');
	
	# Local Variables
	$crlf																= "\n";
	
	# Construct Headers
	$hdrs																= array(
																					'From'    => $from,
																					'Subject' => $subject
																				);
	
	# Create Mail Object
	$mime 																= new Mail_mime($crlf);
	
	# Generate Message Body
	$mime->setTXTBody($text);
	$mime->setHTMLBody($html);
	$body 																= $mime->get();
	
	# Set Headers
	$hdrs 																= $mime->headers($hdrs);
	
	# Send Email
	$mail																=& Mail::factory('mail');
	$mail->send($to, $hdrs, $body);
	//$mail->send("ralfepoisson@gmail.com", $hdrs, $body); // For Testing Purposes
	logg ("Email: `$subject` has been sent to $to successfully.");
}

function set_error($message) {
	$_SESSION['error_message']											= $message;	
}

function set_info($message) {
	$_SESSION['info_message']											= $message;
}

function get_web_page($url) {
	logg("CURL: > {$url}");
	$ch																	= curl_init(); 
	url_setopt($ch, CURLOPT_URL, $url); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	$output																= curl_exec($ch); 
	curl_close($ch);
	logg("CURL: < {$output}");
	return $output;
}

function now() {
	return date("Y-m-d H:i:s");
}

function generate_select_values($table, $id_field, $name_field, $where="") {
	# Global Variables
	global $_db;
	
	# Get Data
	$where_clauses														= (strlen(trim($where)))? filter_clauses($where) : "";
	$query																= "	SELECT
																				`{$id_field}` as 'id',
																				`{$name_field}` as 'name'
																			FROM
																				`{$table}`
																			WHERE
																				`active` = 1
																				AND `org_id` = '" . get_org() . "'
																				{$where_clauses}
																			";
	$data																= $_db->fetch($query);
	
	# Generate Values
	$values																= array();
	foreach ($data as $item) {
		$values[$item->id]												= $item->name;
	}
	
	# Return Values
	return $values;
}

function format_plaintext($str) {
	# Generate HTML from String
	$html																= str_replace("\n", "<br>", $str);
	$html																= str_replace("\t", "&#09;", $html);
	$html																= str_replace(" ", "&nbsp;", $html);
	
	# Return HTML
	return $html;
}

# =========================================================================
# THE END
# =========================================================================
