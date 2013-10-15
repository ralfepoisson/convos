<?php
/**
 * ConvOS
 * 
 * App Class
 * 
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 0.0.1
 * @license GPL3
 */

class App {
	
	public $dir;
	public $manifest;
	
	public function __construct($dir) {
		# Log Activity
		logg("App: Adding Application ({$dir})");
		
		# Set Attributes
		$this->dir														= $dir;
		
		# Load Manifest
		$this->load_manifest();
	}
	
	private function load_manifest() {
		# Ensure that the file exists
		$file															= $this->dir . "Manifest.xml";
		if (file_exists($file)) {
			$this->manifest												= new Manifest($file);
		}
		else {
			throw new Exception("App: Missing Manifest file for '{$this->dir}'.");
		}
	}
	
}
