<?php
/**
 * Template Web Application
 * 
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 2.0
 * @package WebApp
 */

# ==========================================================================================
# Template CLASS
# ==========================================================================================

class Template {
	
	# --------------------------------------------------------------------------------------
	# ATTRIBUTES
	# --------------------------------------------------------------------------------------
	
	private $html;
	public $file;
	public $top;
	public $bottom;
	
	# --------------------------------------------------------------------------------------
	# METHODS
	# --------------------------------------------------------------------------------------
	
	/**
	 * Constructor
	 * 
	 * Initialise default values for Model attributes 
	 */
	public function __construct($file="", $variables="") {
		# Initialise Variables
		$this->file													= ($file)? $file : dirname(dirname(dirname(dirname(__FILE__)))) . "/frontend/html/index.html";
		$this->variables											= $variables;
	}
	
	public function toString() {
		return $this->render($this->get_html(), $this->variables);
	}
	
	public function get_html() {
		# Get HTML
		$this->html													= file_get_contents($this->file);
		return $this->html;
	}
	
	public function draw_top() {
		$this->split();
		print $this->render($this->top);
	}
	
	public function draw_bottom() {
		$this->split();
		print $this->render($this->bottom);
	}
	
	public function draw() {
		$this->get_html();
		print $this->render($this->html);
	}
	
	private function split($marker="CONTENT") {
		# Get HTML
		if (!strlen($this->html)) {
			$this->get_html();
		}
		
		# Get the Position of the Content
		$marker														= "{{" . $marker . "}}";
		$pos														= strpos($this->html, $marker);
		
		# Get the Top half
		$this->top													= substr($this->html, 0, $pos);
		
		# Get the Bottom half
		$this->bottom												= substr($this->html, $pos + strlen($marker));
	}
	
	public function render($html, $variables=0) {
		# Cycle through Template Variables
		while (strstr($html, "{{")) {
			# Get Next Template Variable
			$pos														= strpos($html, "{{");
			$pos2														= strpos($html, "}}", $pos) - $pos;
			$var														= substr($html, $pos + 2, $pos2 - 2);
			
			# Check for Repeating Block
			if (substr($var, 0, 6) == "BEGIN:") {
				$repeating_var											= substr($var, 6);
				
				# Find End of Repeating Block
				$pos_end												= strpos($html, "{{END:{$repeating_var}}}", $pos);
				$pos_end2												= $pos_end + strlen("{{END:{$repeating_var}}}");
				
				# Find Content to Replace
				$repeating_content										= substr($html, ($pos2 + $pos + 2), $pos_end - ($pos2 + $pos + 2));
				
				# Generate Repeating HTML
				$repeating_html											= "";
				if (sizeof($variables[$repeating_var])) {
					foreach ($variables[$repeating_var] as $arr) {
						$repeating_html									.= $this->render($repeating_content, $arr);
					}
				}
				
				# Inject Repeating HTML
				$html													= substr($html, 0, $pos) . $repeating_html . substr($html, $pos_end2);
			}
			# Auth Block
			else if (substr($var, 0, 5) == "AUTH:") {
				$auth_var												= substr($var, 5);
				
				# Find End of Auth Block
				$pos_end												= strpos($html, "{{END:AUTH}}", $pos);
				$pos_end2												= $pos_end + 12;
				
				# Get Content
				$content												= substr($html, ($pos2 + $pos + 2), $pos_end - ($pos2 + $pos + 2));
				
				# Generate Replacement HTML
				$replacement											= (has_authority($auth_var))? $content : "";
				
				# Replace HTML
				$html													= substr($html, 0, $pos) . $replacement . substr($html, $pos_end2);
			}
			# Variable
			else {
				# Get Substitution Value
				$val													= $this->render_special($var);
				if (!$val) {
					$val												= $this->render_variable($var, $variables);
				}
			
				# Inject Substitution Value
				$html													= substr($html, 0, $pos) . $val . substr($html, $pos + strlen($var) + 4);
			}
		}
		
		# Return Rendered HTML
		return $html;
	}
	
	public function render_variable($variable, $variables) {
		if (is_array($variables)) {
			foreach ($variables as $key => $value) {
				if ($key == $variable) {
					return $value;
				}
			}
		}
		return "";
	}
	
	public function render_special($variable) {
		if ($variable == "MENU") {
			return $this->render_menu();
		}
		else if ($variable == "CSS") {
			return $this->css_includes();
		}
		else if ($variable == "JS") {
			return $this->js_includes();
		}
		else if ($variable == "USERNAME") {
			return user_get_name(get_user_uid());
		}
		else {
			return "";
		}
	}
	
	private function css_includes() {
		$dir														= dirname(dirname(dirname(dirname(__FILE__)))) . "/frontend/css/";
		$d															= opendir($dir);
		$includes													= "";
		
		while ($entry												= readdir($d)) {
			if (strstr($entry, ".css")) {
				$includes											.= "		<link rel='stylesheet' type='text/css' href='css/{$entry}'>\n";
			}
		}
		
		return $includes;
	}
	
	private function js_includes() {
		$dir														= dirname(dirname(dirname(dirname(__FILE__)))) . "/frontend/js/";
		$d															= opendir($dir);
		$includes													= "";
		
		while ($entry												= readdir($d)) {
			if (strstr($entry, ".js")) {
				$includes											.= "		<script src='js/{$entry}'></script>\n";
			}
		}
		
		return $includes;
	}
	
	private function render_menu() {
		# Global Variables
		global $_db, $_GLOBALS;
	
		# Get Menu XML
		$xml_file														= dirname(dirname(__FILE__)) . "/config/menu.xml";
		$xml															= simplexml_load_file($xml_file);
	
		# Generate Menu Items
		$items															= "";
		foreach ($xml as $item) {
			$items														.= $this->xml_menu_item_html($item);
		}
	
		# Generate HTML
		$html															= "
			<!-- Main Menu -->
			<ul id='menu'>
				{$items}
			</ul>
		";
	
		# Return HTML
		return $html;
	}
	
	private function xml_menu_item_html($item) {
		# Get Children HTML
		$children														= "";
		if (isset($item->item)) {
			if (isset($item->item->item)) {
				foreach ($item->item as $child) {
					$children											.= xml_menu_item_html($child);
				}
			}
			else {
				$children												.= xml_menu_item_html($item->item);
			}
		}
		$children														= (strlen($children))? "<ul>{$children}</ul>" : "";
		
		# Generate HTML
		$html															= "
			<li>
				<a href='{$item['link']}'>{$item['label']}</a>
				{$children}
			</li>
		";
	
		# Return HTML
		return $html;
	}

}

# ==========================================================================================
# THE END
# ==========================================================================================
