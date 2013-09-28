<?php
/**
 * Project
 * 
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 2.0
 * @package Project
 */

# ==========================================================================================
# Application CLASS
# ==========================================================================================

class Application {
	
	# --------------------------------------------------------------------------------------
	# ATTRIBUTES
	# --------------------------------------------------------------------------------------
	
	public $config;
	public $page;
	public $template;
	public $user;
	public $p;
	
	# --------------------------------------------------------------------------------------
	# METHODS
	# --------------------------------------------------------------------------------------
	
	/**
	 * Constructor
	 * 
	 * Initialise default values for Model attributes 
	 */
	function __construct() {
		# Global Variables
		global $app, $_GLOBALS;
		
		# Ensure Singleton
		if (is_object($app)) {
			return $app;
		}
		
		# Include Models
		$this->include_models();
		$this->include_helpers();
		
		# Check Installation
		check_installation();
		
		# Connect to Database
		$this->db_connect();
		
		# Initialise Variables
		$this->config												= array();
		$this->template												= new Template();
		$this->user													= new User(get_user_uid());
		
		# Load Configuration
		foreach($_GLOBALS as $key => $value) {
			$this->config[$key]										= $value;
		}
		
		# Sanitize all User Input
		$this->sanitize();
		
		# Authenticate User
		if($this->conf("requires_login")) {
			$this->authenticate();
		}
	}
	
	public function db_connect() {
		# Global Variables
		global $_db;
		
		# Database Engine
		$_db 														= new db_engine(	config::get('mysql_host'),
																						config::get('mysql_user'),
																						config::get('mysql_pass'),
																						config::get('mysql_db'),
																						config::get('mysql_debug')
																					);
		$_db->db_connect();
	}
	
	public function include_models() {
		$dir														= dirname(dirname(dirname(__FILE__))) . "/models/";
		$d															= opendir($dir);
		while ($entry												= readdir($d)) {
			if (strstr($entry, ".class.php")) {
				include_once($dir . $entry);
			}
		}
	}
	
	public function include_helpers() {
		$dir														= dirname(dirname(dirname(__FILE__))) . "/helpers/";
		$d															= opendir($dir);
		while ($entry												= readdir($d)) {
			if (strstr($entry, ".inc.php")) {
				include_once($dir . $entry);
			}
		}
	}
	
	public function Factory() {
		# Global Variables
		global $app;
		
		# Ensure Singleton
		if (is_object($app)) {
			return $app;
		}
		else {
			return new Application();
		}
	}
	
	public function conf($var) {
		if (isset($this->config[$var])) {
			return $this->config[$var];
		}
		else {
			return false;
		}
	}
	
	public function draw_page() {
		# Get Page
		$this->page														= $this->get_page();
		
		# Get Action
		$action															= $this->get_action();
		
		# Log Activity
		logg("Constructing Page: Page = `{$this->p}`. Action = `{$action}`.");
		
		# Draw the Top section of the template
		$this->template->draw_top();
		
		# Run the page's action
		$this->page->$action();
		
		# Draw the Bottom section of the template
		$this->template->draw_bottom();
	}
	
	public function get_page() {
		# Get the 'p' GET variable and sanitize it
		$p																= (isset($_GET['p']))? preg_replace('@[^a-zA-Z0-9_]@', '', $_GET['p']) : $this->conf('default_page');
		$this->p														= $p;
		
		# Determine the file to include from the content directory
		$dir															= dirname(dirname(dirname(__FILE__))) ."/content/";
		$p																= $dir . $p . ".php";
		$p																= (file_exists($p))? $p : $dir . "error.php";
		
		# Include the content file and create a page object
		include_once($p);
		$page															= new Page();
		
		# Return Page
		return $page;
	}
	
	public function get_action() {
		# Get the 'p' GET variable and sanitize it
		$action															= (isset($_GET['action']))? preg_replace('@[^a-zA-Z0-9_]@', '', $_GET['action']) : $this->conf('default_action');
		
		# Return the Action
		return $action;
	}
	
	public function sanitize() {
		foreach ($_POST as $key => $value) {
			$_POST[$key]												= htmlentities($value);
		}
		foreach ($_GET as $key => $value) {
			$_POST[$key]												= htmlentities($value);
		}
		foreach ($_REQUEST as $key => $value) {
			$_POST[$key]												= htmlentities($value);
		}
	}
	
	public function authenticate() {
		if (!$this->user->uid) {
			redirect("login.php");
			die();
		}
	}
	
}

# ==========================================================================================
# THE END
# ==========================================================================================
