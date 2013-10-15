<?php
/**
 * ConvOS
 * 
 * Message Out Class
 *
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 0.0.1
 * @license GPL3
 */

class MessageOut extends AbstractMessage {
	
	public function __construct($raw_xml="") {
		$this->xml													= $this->encode($raw_xml);
		$this->message												= $this->xml->message;
	}
	
	/**
	 * encode()
	 * This function takes the string returned from the application and
	 * converts it into an XML object to send to the client.
	 * @param $output String: The output from the app
	 * @return XML
	 */
	private function encode($output) {
		# Create XML Object
		$xml															= new XML();
		
		# Extract Control Structures
		$controls														= new XMLelement("controls");
		while (strstr($output, "[")) {
			# Get Directive
			$pos														= strpos($output, "[");
			$pos2														= strpos($output, "]");
			$directive													= substr($output, $pos + 1, ($pos2 - $pos - 2));
			$output														= substr($output, 0, $pos - 1) . substr($output, $pos2 + 1);
			
			# Process Directive
			$this->process_directive($directive);
			
			# Add to control structures
			$directive_element											= new XMLelement("directive", $directive);
			$controls->add($directive_element);
		}
		$xml->document->add($controls);
		
		# Add The remaining output to the message component
		$message														= new XMLelement("message", $output);
		$xml->document->add($message);
		
		# Log Activity
		logg("Controller: Responding to client. [{$output}]");
		
		# Return XML
		$this->xml														= simplexml_load_string($xml->generate());
	}
	
}
