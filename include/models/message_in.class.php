<?php
/**
 * ConvOS
 * 
 * Message In Class
 *
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 0.0.1
 * @license GPL3
 */

class MessageIn extends AbstractMessage {
	
	public function __construct($raw_xml) {
		$this->xml														= simplexml_load_string($raw_xml);
		$this->message													= trim((string)$this->xml->message);
		$this->conversation_id											= (isset($this->xml->conversation_id))? trim((int)$this->xml->conversation_id) : 0;
	}
	
}
