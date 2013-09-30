<?php
/**
 * Project
 * 
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 1.0
 * @package Project
 */

# =========================================================================
# CONFIGURATION
# =========================================================================

# Server Settings
$_GLOBALS['server_address']												= "127.0.0.1";
$_GLOBALS['server_port']												= 3333;

# System Settings
$_GLOBALS['base_dir']													= dirname(dirname(dirname(__FILE__))) . "/";
$_GLOBALS['app_dir']													= $_GLOBALS['base_dir'] . "Apps/";
$_GLOBALS['log_file']													= "/var/log/convos/" . date("Ymd") . ".log";
$app																	= 0;

# =========================================================================
# THE END
# =========================================================================

