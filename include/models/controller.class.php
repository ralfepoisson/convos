<?php
/**
 * ConvOS
 * 
 * Controller Class
 *
 * This class is responsible for deciding how to handle the incoming requests
 * from the clients, and then generating the appropriate responses.
 * 
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 0.0.1
 * @license GPL3
 */

class Controller {
	
	public $input;
	
	public function get_output($input) {
		# Interpret Input
		$this->input													= $this->decode($input);
		
		# First Try Internal Commands
		$output															= $this->route_internals();
		
		# Next try Application call routing
		if ($output === false) {
			$output														= $this->route_apps();
		}
		
		# Encode Output
		$output															= $this->encode($output)->generate() . "\n";
		
		# Return Output
		return $output;
	}
	
	private function route_internals() {
		if ($this->input->message == "Goodbye") {
			# Get Platform
			$platform														= Platform::Factory();
			
			# Close Socket Connection
			$platform->service->close_socket();
		}
		return false;
	}
	
	/**
	 * route_apps()
	 * This is the main intelligence behind deciding how to respond to a given input. The
	 * function first checks to see if the user is asking for a quick piece of information
	 * without leaving the current application context. This is known as an 'Asside'. If
	 * this is not the case, the function then routes the input to the current conversation
	 * happening within the current app context. If no conversation is currently occurring,
	 * then the function checks to see if the input matches a conversation starter, which will
	 * change the application context and set a current conversation or it will check to see
	 * if the input matches a hook, which will just change the application context.
	 */
	private function route_apps() {
		# Get Message Component
		$message														= trim((string)$this->input->message);
		
		# Get Platform
		$platform														= Platform::Factory();
		
		# First Check Assides
		foreach ($platform->apps as $app) {
			foreach ($app->manifest->assides as $call) {
				$result													= $this->test_call($call, $message);
				if (!($result === false)) {
					logg("Controller: Routing to App '{$app->manifest->name}' through Asside: '{$call->input}'");
					return $result;
				}
			}
		}
		
		# If currently in a conversation, pass input to the conversation
		if (!($platform->conversation == 0)) {
			# Log Activity
			logg("Controller: Routing to current Conversation.");
			
			# Run Command
			return $this->call_command($platform->conversation->command);
		}
		else {
			# Loop through Hooks
			foreach ($platform->apps as $app) {
				foreach ($app->manifest->hooks as $call) {
					$result												= $this->test_call($call, $message);
					if (!($result === false)) {
						# Update Current Application Context
						$platform->context								= $app;
					
						# Log Activity
						logg("Controller: Routing to App '{$app->manifest->name}' through Hook: '{$call->input}'");
					
						# Return Result
						return $result;
					}
				}
			}
			
			# Loop through Conversation Starters
			foreach ($platform->apps as $app) {
				foreach ($app->manifest->starters as $call) {
					$result												= $this->test_call($call, $message);
					if (!($result === false)) {
						# Update Current Application Context
						$platform->context								= $app;
						$platform->conversation							= $call;
					
						# Log Activity
						logg("Controller: Routing to App '{$app->manifest->name}' through Conversation Starter: '{$call->input}'");
					
						# Return Result
						return $result;
					}
				}
			}
	
			# If Nothing Has been understood, Retrun false
			return false;
		}
	}
	
	private function test_call($call, $message) {
		//TODO: Implement REGEX
		# Test Call's Main Input Phrase
		if ($call->input == $message) {
			return $this->call_command($call->command);
		}
		
		# Test Call's Alternative Input Phrases
		if (sizeof($call->alts->alt)) {
			foreach ($call->alts->alt as $alt) {
				logg("Testing '{$message}' against {$alt}.");
				if ($alt == $message) {
					return $this->call_command($call->command);
				}
			}
		}
		
		# If tests failed, return false
		return false;
	}
	
	private function call_command($command) {
		# Render Command
		$command														= $this->render_command($command);
		
		# Log Activity
		logg("Controller: Calling command: {$command}");
		
		# Execute Command
		$output															= exec($command);
		
		# Return Command
		return $output;
	}
	
	private function render_command($command) {
		# Global Variables
		global $_GLOBALS;
		
		# Prepare Variables for Rendering
		$vars															= array(	
																					"APPDIR"		=> $_GLOBALS['app_dir'],
																					"MESSAGE"		=> $this->input->message
																				);
		
		# Create Template Object
		$template														= new Template();
		
		# Render Command
		$command														= $template->render($command, $vars);
		
		# Return Command
		return $command;
	}
	
	/**
	 * decode()
	 * This function takes the string read in from the client and
	 * converts it into an XML object for processing.
	 * @param $input String: The input from the client containing the XML
	 * @return XML
	 */
	private function decode($input) {
		return simplexml_load_string($input);
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
		return $xml;
	}
	
	/**
	 * process_directive()
	 * This function takes in a directive, and then executes the directive's objective.
	 * A directive is a control structure passed to the ConvOS platform from an
	 * application call. It is formatted as [DIRECTIVE]. For example, [END:CONVERSATION]
	 * Directives are used to induce a state change within the Platform, and are not
	 * part of the message sent to the client for display.
	 * @param $directive String: This is the directive to execute.
	 */
	private function process_directive($directive) {
		if ($directive == "END:CONVERSATION") {
			# Get Platform
			$platform														= Platform::Factory();
			
			# End Conversation
			$platform->conversation											= 0;
		}
	}
	
}
