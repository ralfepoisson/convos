<?php
/**
 * ConvOS
 * 
 * Platform Class
 * 
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 0.0.1
 * @license GPL3
 */

class Platform {
	
	/*	-------------------------------------------------------
		Attributes
	*/
	
	public $apps;
	public $context;
	
	public function __construct() {
		# Set Attributes
		$this->apps														= array();
		$this->context													= 0;
	}
	
	/*	-------------------------------------------------------
		Public Methods
	*/
	
	public function Factory() {
		# Global Variables
		global $app;
		
		# Ensure Singleton
		if (is_object($app)) {
			return $app;
		}
		else {
			return new Platform();
		}
	}
	
	public function init() {
		# Log Activity
		logg("Platform: Initiating...");
		
		# Load Mainifests
		$this->load_apps();
		
		# Log Activity
		logg("Platform: Ready.");
	}
	
	public function run() {
		
	}
	
	/*	-------------------------------------------------------
		Private Methods
	*/
	
	private function load_apps() {
		# Global Variables
		global $_GLOBALS;
		
		# Log Activity
		logg("Platform: Loading Apps ...");
		
		# Clear Apps
		$this->apps														= array();
		
		# Load Apps from Dir
		$dir															= $_GLOBALS['app_dir'];
		$d																= opendir($dir);
		while ($entry													= readdir($d)) {
			logg($entry);
			if (file_exists($dir . $entry . "/Manifest.xml")) {
				$tmp													= new App($dir . $entry . "/");
				$this->apps[]											= $tmp;
				if ($entry == "General") {
					$this->context										= $tmp;
				}
			}
		}
	}
	
}
