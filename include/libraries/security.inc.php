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

function security_handle_event($result) {
	# Global Variables
	global $_db, $cur_page, $_GLOBALS;
	
	# Generate Email
	$trace 					= print_r(debug_backtrace(), 1);
	$error_message			= "<b>SECURITY ALERT on {$_SERVER['REQUEST_URI']}.</b>: " . print_r($result, 1) . "<br /><br /><b>Stack Trace</b><br /><br />$trace";
	
	# Logg Activity
	logg("SECURITY WARNING: " . print_r($result, 1) . "\n" . $trace);
	
	# Send Email
	$to						= $_GLOBALS['security_email'];
	$subject				= $_GLOBALS['security_subject'];
	$message				= $error_message;
	mail($to, $subject, $message, "From: <{$_GLOBALS['title']}> {$_GLOBALS['from_email']} \n\r");
	
	# Output Generic Message
	print "
	<html>
		<head>
			<title>{$_GLOBALS['title']}</title>
		</head>
		<body>
			<h2>Security Alert</h2>
			
			<p>
				Please note, an attempted security breach has been detected and administrators have been notified.
			</p>
			
			<a href='./'>Click Here to Return to the Main Page.</a>
		</body>
	</html>
	";
	
	# Stop Processing
	die();
}

function setup_ids() {
	# Include the PHP-IDS Library
	require_once 'phpids/lib/IDS/Init.php';
	
	# Compile User Input Data to Process
	$request							= array(	'REQUEST'		=> $_REQUEST,
													'GET'			=> $_GET,
													'POST'			=> $_POST,
													'COOKIE'		=> $_COOKIE
												);
	
	# Initialize the IDS
	$init								= IDS_Init::init('/Config/Config.ini.php');
	$ids								= new IDS_Monitor($request, $init);
	
	# Run
	$result								= $ids->run();
	if (!$result->isEmpty()) {
		security_handle_event($result);
	}
}

# =========================================================================
# SETUP PHP-IDS
# =========================================================================

#setup_ids();

# =========================================================================
# THE END
# =========================================================================

?>