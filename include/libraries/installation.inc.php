<?php
/**
 * SMS Related Functions
 * 
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 1.1
 * @package DW Bullion
 */

//-----------------------------------------------------------------------
// FUNCTIONS
//-----------------------------------------------------------------------

function check_installation() {
	# Local Variables
	$error_message														= "";
	
	# Test Connection
	$error_message														= install_test_db_connect();
	$error_message														.= install_test_db_query();
	
	# If there are database problems, display error
	if (strlen($error_message)) {
		logg("Installation: Database errors. Running installation process. ($error_message)");
		run_installation($error_message);
	}
	else {
		return true;
	}
}

function install_test_db_connect() {
	# Local Variables
	$error_message														= "";
	
	# Get Database Connection Details
	$mysql_host															= config::get("mysql_host");
	$mysql_user															= config::get("mysql_user");
	$mysql_pass															= config::get("mysql_pass");
	$mysql_db															= config::get("mysql_db");
	
	# Test Connection
	if ($link = mysql_connect($mysql_host, $mysql_user, $mysql_pass)) {
		# Test Database Selection
		if (mysql_select_db($mysql_db, $link)) {
			$mysql_database												= true;
		}
		else {
			$error_message												.= "Could not access the database. ";
		}
	}
	else {
		$error_message													.= "Could not connect to the database. ";
	}
	
	# Return Result
	return $error_message;
}

function install_test_db_query() {
	# Local Variables
	$error_message														= "";
	
	# Get Database Connection Details
	$mysql_host															= config::get("mysql_host");
	$mysql_user															= config::get("mysql_user");
	$mysql_pass															= config::get("mysql_pass");
	$mysql_db															= config::get("mysql_db");
	
	# Test Connection
	logg("Installation: Testing DB Query: {$mysql_host}, {$mysql_user}, {$mysql_pass}, {$mysql_db}.");
	$link 																= mysql_connect($mysql_host, $mysql_user, $mysql_pass);
	mysql_select_db($mysql_db, $link);
	mysql_query("SELECT COUNT(*) FROM `users`");
	if (mysql_error()) {
		$error_message													= "Could not query database. " . mysql_error();
	}
	
	# Return Result
	return $error_message;
}

function run_installation($error) {
	redirect("install.php?error=" . urlencode($error));
}

//-----------------------------------------------------------------------
// THE END
//-----------------------------------------------------------------------

