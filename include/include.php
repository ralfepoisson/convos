<?php
/**
 * Project
 * 
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 2.0
 * @package Project
 */

# =========================================================================
# INCLUDE REQUIRED SCRIPTS
# =========================================================================

# Configuration
include_once("config/config.php");

# Include all Function Libraries
$dir																	= dirname(__FILE__) . "/libraries/";
$d																		= opendir($dir);
while ($entry															= readdir($d)) {
	if (strstr($entry, ".inc.php")) {
		include_once($dir . $entry);
	}
}


# Include all classes
$dir																	= dirname(__FILE__) . "/classes/";
$d																		= opendir($dir);
while ($entry															= readdir($d)) {
	if (strstr($entry, ".class.php")) {
		include_once($dir . $entry);
	}
}

# =========================================================================
# THE END
# =========================================================================

