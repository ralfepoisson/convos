<?php
/**
 * Project
 * 
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 2.0
 * @package Project
 */

# ==========================================================================================
# Test Manager CLASS
# ==========================================================================================

class TestManager {
	
	# --------------------------------------------------------------------------------------
	# ATTRIBUTES
	# --------------------------------------------------------------------------------------
	
	public $test_files;
	
	# --------------------------------------------------------------------------------------
	# METHODS
	# --------------------------------------------------------------------------------------
	
	/**
	 * Constructor
	 * 
	 * Initialise default values for Model attributes 
	 */
	public function __construct() {
		$this->test_files												= array();
	}
	
	public function get_tests() {
		# Reset Test Files Array
		$this->test_files												= array();
		
		# Get Directory Listing
		$d																= opendir(dirname(dirname(dirname(__FILE__))) . "/tests/");
		while ($entry													= readdir($d)) {
			if (substr($entry, 0, 4) == "test") {
				$this->test_files[]										= $entry;
			}
		}
	}
	
	public function run_test($test_file) {
		# Run PHPUnit Command
		$command														= "phpunit {$test_file}";
		$output															= exec($command);
		return $output;
	}
	
	public function test() {
		# Get Test Files
		$this->get_tests();
		
		# Run Each Test
		foreach ($this->test_files as $test_file) {
			$output														= run_test($test_file);
			print $output;
		}
	}
	
}

# ==========================================================================================
# THE END
# ==========================================================================================
