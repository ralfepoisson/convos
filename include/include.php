<?php
/**
 * ConvOS
 * 
 * Include configuration and all function libraries.
 * 
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 0.0.1
 * @license GPL3
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

# Include all Models
$dir																	= dirname(__FILE__) . "/models/";
$d																		= opendir($dir);
while ($entry															= readdir($d)) {
	if (strstr($entry, ".class.php")) {
		include_once($dir . $entry);
	}
}

# =========================================================================
# THE END
# =========================================================================
