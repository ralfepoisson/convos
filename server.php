<?php
/**
 * ConvOS
 * 
 * Server Script: This is the script that runs the ConvOS daemon which
 * listens for incomming connections to initiate conversations.
 * 
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 0.0.1
 * @license GPL3
 */

# =====================================================================
# SCRIPT SETTING
# =====================================================================

# Include Required Scripts
require_once(dirname(__FILE__) . "/include/include.php");

# =====================================================================
# MAIN PROGRAM
# =====================================================================

# Create Platform Object
global $platform;
$platform																= Platform::Factory();

# Initialize Platform
$platform->init();

# Run main program loop
$platform->run();

# =====================================================================
# THE END
# =====================================================================
