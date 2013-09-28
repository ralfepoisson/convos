<?php
/**
 * Project Tests
 *
 * This script makes use of PHP-Unit to test ta module
 * 
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 2.0
 * @copyright Copyright (C) Ralfe Poisson 2012
 */

# ========================================================================
# SCRIPT SETTINGS
# ========================================================================

# Include System Framework
require_once(dirname(dirname(__FILE__)) . "/include.php");

# ========================================================================
# CREATE TESTS
# ========================================================================

class ProjectTest extends PHPUnit_Framework_TestCase{
	
	# ------------------------------------------------------------------------
	# ATTRIBUTES
	# ------------------------------------------------------------------------
	
	
	
	# ------------------------------------------------------------------------
	# CREATE TESTS
	# ------------------------------------------------------------------------
	
	function setUp() {
		# Log Activity
		$this->write_log("Setting up testing objects.");
		
		# Create Testing Objects
		
	}
	
	function tearDown() {
		# Log Activity
		$this->write_log("Removing testing objects.");
		
		# Remove Testing Objects
		
	}
	
	function write_log($message) {
		logg("UNIT TEST: {$message}");
	}
	
	# ------------------------------------------------------------------------
	# CREATE TESTS
	# ------------------------------------------------------------------------
	
	
	

}
