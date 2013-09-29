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

# System Settings
$_GLOBALS['base_dir']													= dirname(dirname(dirname(__FILE__))) . "/";
$_GLOBALS['app_dir']													= $_GLOBALS['base_dir'] . "Apps/";
$_GLOBALS['log_file']													= "/var/log/convos/" . date("Ymd") . ".log";
$app																	= 0;

# =========================================================================
# THE END
# =========================================================================

