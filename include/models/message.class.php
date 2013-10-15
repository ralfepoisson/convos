<?php
/**
 * ConvOS
 * 
 * Message Class
 *
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 0.0.1
 * @license GPL3
 */

abstract class AbstractMessage {
	
	public $xml;
	public $type;
	public $message;
	public $conversation_id;
	
	public static function Factory($xml_raw, $type="In") {
		# Create Appropriate Message Type
		if ($type == "In") {
			$obj														= new MessageIn($xml_raw);
		}
		else {
			$obj														= new MessageOut($xml_raw);
		}
		
		# Set Type
		$obj->type														= $type;
		
		# Return Object
		return $obj;
	}
	
	public function toString() {
		return $this->type . "> " . $this->message;
	}
	
	public function toXML() {
		return $this->xml->asXML();
	}
	
}
