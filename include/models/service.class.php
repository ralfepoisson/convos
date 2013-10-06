<?php
/**
 * ConvOS
 * 
 * Service Class
 * 
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 0.0.1
 * @license GPL3
 */

class Service {
	
	public $address;
	public $port;
	public $socket;
	public $resource;
	public $connected;
	
	public function __construct($address="127.0.0.1", $port="3333") {
		# Initialize Attributes
		$this->address													= $address;
		$this->port														= $port;
		$this->socket													= 0;
		$this->resource													= 0;
		$this->connected												= 0;
		
		# Set Congiruation Properties
		set_time_limit(0);
		ob_implicit_flush();
	}
	
	public function init_socket() {
		# Log Activity
		logg("Service: Initializing Socket.");
		
		# Create Socket Object
		logg(" - Creating Socket Object");
		if (!($this->socket 											= socket_create(AF_INET, SOCK_STREAM, SOL_TCP))) {
		    $this->error("socket_create() failed: reason: " . socket_strerror(socket_last_error()));
		}
		
		# Bind Socket to Address and Port
		logg(" - Binding Socket to Address {$this->address} and Port {$this->port}");
		if (!socket_bind($this->socket, $this->address, $this->port)) {
		    $this->error("socket_bind() failed: reason: " . socket_strerror(socket_last_error($this->socket)));
		}
		
		# Set Socket to Listen for incommning connections
		logg(" - Begin listening for incomming connections");
		if (!socket_listen($this->socket, 5)) {
		    $this->error("socket_listen() failed: reason: " . socket_strerror(socket_last_error($this->socket)));
		}
		/*
		# Setup Events
		$base = event_base_new();
		$event = event_new();
		event_set($event, $socket, EV_READ | EV_PERSIST, "Service::new_client()", $base);
		event_base_set($event, $base);
		event_add($event);
		event_base_loop($base);
		*/
		# Return True
		return true;
	}
	
	public function listen() {
		while (true) {
			if ($this->resource == 0) {
				if (!($this->resource									= socket_accept($this->socket))) {
					$this->error("socket_accept() failed: reason: " . socket_strerror(socket_last_error($this->socket)));
					break;
				}
				else {
					return true;
				}
				
				# Log Activity
				logg("Service: Received incoming connection.");
			}
		}
	}
	
	public function get_input() {
		while (true) {
			# Read Input
			$input														= $this->read();
			
			# Return Input
			if ($input) {
				logg("Service: Received Input: [{$input}]");
				return $input;
			}
		}
	}
	
	public function close_socket() {
		# Log Activity
		logg("Service: Closing Socket");
		
		# Close Socket
		socket_close($this->resource);
		
		# Mark as disconnected
		$this->connected												= 0;
	}
	
	public function read() {
		if (!($input 													= socket_read($this->resource, 2048, PHP_NORMAL_READ))) {
			$this->error("socket_read() failed: reason: " . socket_strerror(socket_last_error($this->resource)));
			break 2;
		}
		else {
			$this->connected											= 1;
		}
		
		# Log Activity
		logg(" - Read data.");
		
		# Format input
		$input															= trim($input);
		
		# Return Input
		if (strlen($input)) {
			return $input;
		}
		else {
			return false;
		}
	}
	
	public function write($output) {
		# Log Activity
		logg("Service: Writing to Socket.");

		if ($this->connected) {
			# Format Output
			$output														= trim($output);
		
			# Ensure there is something to output
			if (strlen($output)) {
				# Write to Socket
				if (!socket_write($this->resource, $output, strlen($output))) {
					$this->error("socket_write() failed: reason: " . socket_strerror(socket_last_error($this->resource)));
				}
			}
		}
		else {
			logg(" - could not write to socket as connection has been closed.");
		}
		
		# Return True
		return true;
	}
	
	private function error($message) {
		# Log Error
		logg("Service Error: " . $message);
		
		# Throw Exception
		throw new Exception($message);
	}
	
}
