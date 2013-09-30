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
	public $service;
	public $controller;
	
	public function __construct() {
		# Set Attributes
		$this->apps														= array();
		$this->context													= 0;
		$this->controller												= new Controller();
		$this->service													= new Service(	Platform::get_config("server_address"), 
																						Platform::get_config("server_port")
																					);
	}
	
	/*	-------------------------------------------------------
		Public Methods
	*/
	
	/**
	 * Factory():
	 * This function is used to implement a Singleton pattern for the Platform.
	 * If some client code needs to make use of the Platform, it can access the
	 * Singleton object through the following code:
	 *       $platform = Platform::Factory();
	 */
	public static function Factory() {
		# Global Variables
		global $platform;
		
		# Ensure Singleton
		if (is_object($platform)) {
			return $platform;
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
		# Create Socket Connection
		$this->service->init_socket();
		
		# Loop
		try {
			while (true) {
				# Get Input
				$input													= $this->service->get_input();
			
				# Get Response
				$response												= $this->controller->get_output($input);
			
				# Return Response
				$this->service->write($response);
			}
		}
		catch (Exception $e) {
			logg("Platform: Caught Exception: " . $e->getMessage());
		}
		
		# Close Socket
		socket_close($this->socket);
	}
	
	public static function get_config($var) {
		# Global Variables
		global $_GLOBALS;
		
		# Return Configuration Variable
		return ($_GLOBALS[$var])? $_GLOBALS[$var] : false;
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
