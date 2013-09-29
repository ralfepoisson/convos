<?php
/**
 * ConvOS
 * 
 * Call Class
 * 
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 0.0.1
 * @license GPL3
 */

class Call {
	
	public $input;
	public $alts;
	public $command;
	
	public function __construct() {
		# Set defaults for attributes
		$this->input													= "";
		$this->alts														= array();
		$this->command													= "";
	}
	
	public function add_alt($str) {
		$this->alts[]													= $str;
	}
	
}
