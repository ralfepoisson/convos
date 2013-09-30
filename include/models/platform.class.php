<?php
/**
 * ConvOS
 * 
 * Platform Class
 * 
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 0.0.1
 * @license GPL3
 */

class Platform {
	
	/*	-------------------------------------------------------
		Attributes
	*/
	
	public $apps;
	public $context;
	public $address;
	public $port;
	public $socket;
	
	public function __construct() {
		# Set Attributes
		$this->apps														= array();
		$this->context													= 0;
		
		# Set Congiruation Properties
		set_time_limit(0);
		ob_implicit_flush();
	}
	
	/*	-------------------------------------------------------
		Public Methods
	*/
	
	public function Factory() {
		# Global Variables
		global $app;
		
		# Ensure Singleton
		if (is_object($app)) {
			return $app;
		}
		else {
			return new Platform();
		}
	}
	
	public function init() {
		# Log Activity
		logg("Platform: Initiating...");
		
		# Load Mainifests
		$this->load_apps();
		
		# Log Activity
		logg("Platform: Ready.");
	}
	
	public function run() {
		# Create Socket Connection
		$this->init_socket();
		
		# Loop
		do {
		    if (($msgsock = socket_accept($this->socket)) === false) {
		        echo "socket_accept() failed: reason: " . socket_strerror(socket_last_error($this->socket)) . "\n";
		        break;
		    }
		    /* Send instructions. */
		    $msg = "\nWelcome to the PHP Test Server. \n" .
		        "To quit, type 'quit'. To shut down the server type 'shutdown'.\n";
		    socket_write($msgsock, $msg, strlen($msg));

		    do {
		        if (false === ($buf = socket_read($msgsock, 2048, PHP_NORMAL_READ))) {
		            echo "socket_read() failed: reason: " . socket_strerror(socket_last_error($msgsock)) . "\n";
		            break 2;
		        }
		        if (!$buf = trim($buf)) {
		            continue;
		        }
		        if ($buf == 'quit') {
		            break;
		        }
		        if ($buf == 'shutdown') {
		            socket_close($msgsock);
		            break 2;
		        }
		        $talkback = "PHP: You said '$buf'.\n";
		        socket_write($msgsock, $talkback, strlen($talkback));
		        echo "$buf\n";
		    } while (true);
		    socket_close($msgsock);
		} while (true);
		
		# Close Socket
		socket_close($this->socket);
	}
	
	/*	-------------------------------------------------------
		Private Methods
	*/
	
	private function load_apps() {
		# Global Variables
		global $_GLOBALS;
		
		# Log Activity
		logg("Platform: Loading Apps ...");
		
		# Clear Apps
		$this->apps														= array();
		
		# Load Apps from Dir
		$dir															= $_GLOBALS['app_dir'];
		$d																= opendir($dir);
		while ($entry													= readdir($d)) {
			logg($entry);
			if (file_exists($dir . $entry . "/Manifest.xml")) {
				$tmp													= new App($dir . $entry . "/");
				$this->apps[]											= $tmp;
				if ($entry == "General") {
					$this->context										= $tmp;
				}
			}
		}
	}
	
	private function init_socket() {
		# Create Socket Object
		if (($this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
		    throw new Exception("socket_create() failed: reason: " . socket_strerror(socket_last_error()));
		}
		
		# Bind Socket to Address and Port
		if (socket_bind($this->socket, $this->address, $this->port) === false) {
		    throw new Exception("socket_bind() failed: reason: " . socket_strerror(socket_last_error($this->socket)));
		}
		
		# Set Socket to Listen for incommning connections
		if (socket_listen($this->socket, 5) === false) {
		    throw new Exception("socket_listen() failed: reason: " . socket_strerror(socket_last_error($this->socket)));
		}
	}
	
}
