<?php
/**
 * ConvOS
 * 
 * Manifest Class
 * 
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 0.0.1
 * @license GPL3
 */

class Manifest {
	
	public $file;
	
	public $name;
	public $description;
	public $author_name;
	public $author_email;
	public $version;
	
	public $hooks;
	public $assides;
	public $starters;
	
	public function __construct($file) {
		# Set Attributes
		$this->file														= $file;
		$this->name														= "";
		$this->description												= "";
		$this->author_name												= "";
		$this->author_email												= "";
		$this->version													= 0;
		$this->hooks													= array();
		$this->assides													= array();
		$this->starters													= array();
		
		# Log Activity
		logg(" - Adding Manifest '{$file}'.");
		
		# Load the file
		$this->load();
	}
	
	public function load() {
		# Load Manifest File into XML Object
		$xml															= simplexml_load_file($this->file);
		
		# Reset Calls
		$this->hooks													= array();
		$this->assides													= array();
		$this->starters													= array();
		
		# Extract Meta Data
		$this->name														= $this->clean_attr($xml->app->meta->name);
		$this->description												= $this->clean_attr($xml->app->meta->description);
		$this->version													= $this->clean_attr($xml->app->meta->version);
		$this->author_name												= $this->clean_attr($xml->app->meta->author->name);
		$this->author_email												= $this->clean_attr($xml->app->meta->author->email);
		
		# Extract Hooks
		$i																= 0;
		foreach ($xml->app->hooks->call as $hook) {
			$this->hooks[$i]											= new Call();
			$this->hooks[$i]->input										= $this->clean_attr($hook->input);
			$this->hooks[$i]->command									= $this->clean_attr($hook->command);
			logg("  > Added Hook '{$this->hooks[$i]->input}'.");
			if (isset($hook->alts)) {
				foreach ($hook->alts->alt as $alt) {
					$this->hooks[$i]->alts[]							= $this->clean_attr($alt);
				}
			}
			$i++;
		}
		
		# Extract Assides
		$i																= 0;
		foreach ($xml->app->assides->call as $asside) {
			$this->assides[$i]											= new Call();
			$this->assides[$i]->input									= $this->clean_attr($asside->input);
			$this->assides[$i]->command									= $this->clean_attr($asside->command);
			logg("  > Added Asside '{$this->assides[$i]->input}'.");
			if (isset($asside->alts)) {
				foreach ($asside->alts->alt as $alt) {
					$this->assides[$i]->alts[]							= $this->clean_attr($alt);
				}
			}
			$i++;
		}
		
		# Extract Conversation Starters
		$i																= 0;
		foreach ($xml->app->starters->call as $starter) {
			$this->starters[$i]											= new Call();
			$this->starters[$i]->input									= $this->clean_attr($starter->input);
			$this->starters[$i]->command								= $this->clean_attr($starter->command);
			logg("  > Added Conversation Starter '{$this->starters[$i]->input}'.");
			if (isset($starter->alts)) {
				foreach ($starter->alts->alt as $alt) {
					$this->starters[$i]->alts[]							= $this->clean_attr($alt);
				}
			}
			$i++;
		}
	}
	
	private function clean_attr($val) {
		return trim((string)$val);
	}
	
}
