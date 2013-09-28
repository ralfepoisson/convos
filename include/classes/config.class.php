<?php
/**
 * Configuration Class (config.class.php)
 * 
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 2.0
 * @package Project
 */

class config {
	
	public $config;
	public $filename;
	
	public function __construct($filename="") {
		# Global Variables
		global $_GLOBALS;
		
		# Set Filename
		$this->filename													= ($filename)? $filename : $_GLOBALS['config_file'];
		
		# Read in Configuration
		$this->refresh($this->filename);
	}
	
	public function refresh() {
		# Reset Config
		$this->config													= $this->read($this->filename);
	}
	
	public function read($filename="") {
		# Global Variables
		global $_GLOBALS;
		
		# Local Variables
		$config															= array();
		$file_name														= ($filename)? $filename : $_GLOBALS['config_file'];
		
		# Open File for Reading
		$f																= fopen($file_name, 'r');
		
		# Parse File
		while ($line													= fgets($f, 1024)) {
			# Remove Trailing spaces and new line character
			$line														= trim($line);
			
			# Remove Comments
			if (strstr($line, "#")) {
				$line													= substr($line, 0, strpos($line, "#"));
			}
			
			# Check if Key-Value Pair
			if (strstr($line, "=")) {
				$line_data												= explode("=", $line);
				$var													= trim($line_data[0]);
				$val													= trim($line_data[1]);
				
				# Add to Config
				$config[$var]											= $val;
			}
		}
		
		# Close File
		fclose($f);
		
		# Return Config
		return $config;
	}
	
	public function get($var) {
		# Get Config
		$config															= config::read();
		
		# Get Var
		if (isset($config[$var])) {
			return $config[$var];
		}
		else {
			return false;
		}
	}
	
	public function update($var, $val, $filename="") {
		# Global Variables
		global $_GLOBALS;
		
		# Local Variables
		$content														= "";
		$found															= false;
		
		# Get Filename
		$filename														= (strlen($filename))? $filename : $_GLOBALS['config_file'];
		
		# Open File for Reading
		$f																= fopen($filename, 'r');
		
		# Read File
		while ($line													= fgets($f, 1024)) {
			if (strstr($line, $var) && strstr($line, "=")) {
				$content												.= $var . " = " . $val . "\n";
				$found													= true;
			}
			else {
				$content												.= $line;
			}
		}
		
		# Close File
		fclose($f);
		
		# If the variable was not found, append it to the file
		if (!$found) {
			$content													.= "\n" . $var . " = " . $val . "\n";
		}
		
		# Write new content to file
		$f																= fopen($filename, 'w');
		fputs($f, $content);
		fclose($f);
	}
	
}
