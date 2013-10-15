<?php
/**
 * ConvOS
 * 
 * Conversation Class
 *
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 0.0.1
 * @license GPL3
 */

class Conversation {
	
	public $messages;
	public $conversation_id;
	public $log_file;
	
	public function __construct($conversation_id=0) {
		# Global Variables
		global $_GLOBALS;
		
		# Set Attributes
		$this->messages													= array();
		$this->conversation_id											= ($conversation_id)? $conversation_id : $this->generate_id();
		$this->log_file													= $_GLOBALS['conversation_log_dir'] . $this->conversation_id . ".log";
	}
	
	public function add_message($message) {
		# Add to Conversation
		$this->messages[]												= $message;
		
		# Write to Log File
		$this->logg($message->toString());
	}
	
	public function logg($text) {
		# Open File for writing
		$f																= fopen($this->log_file, 'a');
		
		# Generate Message
		$message														= date("Y-m-d H:i:s") . " " $text . "\n";
		
		# Append to File
		fputs($f, $message);
		
		# Close File
		fclose($f);
	}
	
	public function generate_id() {
		return date("U");
	}
	
}
