<?php
/**
 * Project Tests
 *
 * This script makes use of PHP-Unit to test a module
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
	
	# Testing Objects
	var $testUser;
	var $textX;
	var $testReminder;
	
	# ------------------------------------------------------------------------
	# SETUP TESTING FRAMEWORK
	# ------------------------------------------------------------------------
	
	function setUp() {
		# Log Activity
		$this->write_log("Setting up testing objects.");
		
		# Create Testing Objects
		$this->testUser													= new User();
		$this->textReminder												= new Reminder();
	}
	
	function tearDown() {
		# Log Activity
		$this->write_log("Removing testing objects.");
		
		# Remove Testing Objects
		$this->testUser->delete();
		$this->textReminder->delete();
	}
	
	function write_log($message) {
		logg("UNIT TEST: {$message}");
	}
	
	# -------------------------------------------------------------------
	# USER OBJECT TESTS
	# -------------------------------------------------------------------
	
	function test_user_new() {
		# Log Activity
		$this->write_log("Testing the creation of a new user.");
		
		# Create Test User
		$this->testUser													= new User();
		
		# Set Testing Values
		$this->testUser->first_name										= "FirstName";
		$this->testUser->last_name										= "LastName";
		$this->testUser->username										= "UserName";
		$this->testUser->password										= md5("test");
		
		# Write to the Database
		$this->testUser->save();
		
		# Test Insert into the database
		$test_user														= new User($this->testUser->uid);
		$this->assertTrue($test_user->first_name == $this->testUser->first_name && $test_user->password == $this->testUser->password);
	}
	
	function test_user_clear_auth() {
		# Log Activity
		$this->write_log("Testing Clearing of user auths.");
		
		# Create Test User
		$this->testUser													= new User();
		$this->testUser->first_name										= "FirstName";
		$this->testUser->last_name										= "LastName";
		$this->testUser->username										= "UserName";
		$this->testUser->password										= md5("test");
		$this->testUser->save();
		$this->testUser->add_allowed_function($test_function);
		
		# Clear Test User Auths
		$this->testUser->clear_auths();
		
		# Get Auths
		$auths															= $this->testUser->get_auths();
		
		# Test that there are no more allowed auths
		$this->assertTrue(sizeof($auths) == 0);
	}
	
	function test_user_add_auth() {
		# Log Activity
		$this->write_log("Testing adding User auth.");
		
		# Local Variables
		$test_function													= "test_function";
		# Add Auth
		$this->testUser->add_allowed_function($test_function);
		
		# Get Auths
		$auths															= $this->testUser->get_auths();
		
		# Test that the test function was added correctly
		$this->assertTrue(in_array($test_function, $auths));
	}
	
	function test_user_form() {
		# Log Activity
		$this->write_log("Testing User Form creation.");
		
		# Get Form HTML
		$html															= $this->testUser->form();
		
		# Check that values are inserted correctly
		$this->assertTrue(strstr($html, "FirstName"));
	}
	
}
