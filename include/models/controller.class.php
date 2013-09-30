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
		if (!$output) {
			$output														= $this->route_apps();
		}
		
		//TODO: Record Conversation
		
		# Encode Output
		$output															= $this->encode($output);
		
		# Return Output
		return $output;
	}
	
	private function route_internals() {
		//TODO: Implement Internals
		return false;
	}
	
	private function route_apps() {
		# Get Message Component
		$message														= trim((string)$this->input->message);
		
		# Get Applications
		$platform														= Platform::Factory();
		
		# Loop through Hooks
		foreach ($platform->apps as $app) {
			foreach ($app->hooks as $call) {
				$result													= $this->test_call($call, $message);
				if (!($result === false)) {
					# Update Current Application Context
					$platform->context									= $app;
					
					# Log Activity
					logg("Controller: Routing to App '{$app->name}' through Hook: '{$call->input}'");
					
					# Return Result
					return $result;
				}
			}
		}
		
		# Loop through Assides
		foreach ($platform->apps as $app) {
			foreach ($app->assides as $call) {
				$result													= $this->test_call($call, $message);
				if (!($result === false)) {
					logg("Controller: Routing to App '{$app->name}' through Asside: '{$call->input}'");
					return $result;
				}
			}
		}
		
		# Loop through Conversation Starters
		foreach ($platform->apps as $app) {
			foreach ($app->starters as $call) {
				$result													= $this->test_call($call, $message);
				if (!($result === false)) {
					# Update Current Application Context
					$platform->context									= $app;
					
					# Log Activity
					logg("Controller: Routing to App '{$app->name}' through Conversation Starter: '{$call->input}'");
					
					# Return Result
					return $result;
				}
			}
		}
		
		# If Nothing Has been understood, Retrun false
		return false;
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
		//TODO: Apply template class
		return $command;
	}
	
	private function decode($input) {
		return simplexml_load_string($input);
	}
	
	private function encode($output) {
		//TODO: Implement XML Encoding
		return $output;
	}
	
}
