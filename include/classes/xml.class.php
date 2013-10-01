<?php
/**
 * ConvOS
 * 
 * XML Class
 * 
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 0.0.1
 * @license GPL3
 */

# =========================================================================
# XML CLASS
# =========================================================================

class XML {
	# ---------------------------------------------------------------------
	# ATTRIBUTES
	# ---------------------------------------------------------------------
	
	var $document;
	
	# ---------------------------------------------------------------------
	# FUNCTIONS
	# ---------------------------------------------------------------------
	
	/**
	 * Constructor for initializing the Validator Object.
	 */
	function __construct() {
		$this->document													= new XMLelement("DOCUMENT", "", array("version" => "3.0", "reply_tp" => "0"));
	}
	
	function generate() {
		# Generate XML
		$data															= "<?xml version=\"1.0\" standalone=\"yes\" ?>\n";
		$data															.= $this->document->generate();
		
		# Return XML
		return $data;
	}
	
	function object_to_element($name, $object) {
		$element														= new XMLelement($name);
		$data															= get_object_vars($object);
		foreach ($data as $key => $val) {
			$sub_element												= new XMLelement($key, $val);
			$element->add($sub_element);
		}
		return $element;
	}
	
}

class XMLelement {
	# ---------------------------------------------------------------------
	# ATTRIBUTES
	# ---------------------------------------------------------------------
	
	var $name;
	var $attributes;
	var $children;
	var $content;
	
	# ---------------------------------------------------------------------
	# FUNCTIONS
	# ---------------------------------------------------------------------
	
	/**
	 * Constructor for initializing the Validator Object.
	 */
	function __construct($name="ITEM", $content="", $attributes=0) {
		$this->name														= $name;
		$this->attributes												= ($attributes)? $attributes : array();
		$this->content													= $content;
		$this->children													= array();
	}
	
	function add($element) {
		$this->children[]												= $element;
	}
	
	function generate($level=0) {
		# Calculate Indent
		$indent															= "";
		for ($x = 0; $x < $level; $x++) {
			$indent														.= "	";
		}
		
		# Generate XML Opening Element
		$xml															= $indent . "<{$this->name}";
		foreach ($this->attributes as $key => $value) {
			$xml														.= " {$key}=\"{$value}\"";
		}
		$xml															.= ">";
		
		# Add Content
		$xml															.= $this->content;
		
		if (sizeof($this->children)) {
			$xml														.= "\n";
			
			# Generate XML Children
			foreach ($this->children as $element) {
				$xml													.= $element->generate($level + 1);
			}
			
			# Indent
			$xml														.= $indent;
		}
		# Generate XML Closing Element
		$xml															.= "</{$this->name}>\n";
		
		# Return XML
		return $xml;
	}
	
}

# =========================================================================
# THE END
# =========================================================================
