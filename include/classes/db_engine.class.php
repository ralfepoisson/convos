<?php
/**
 * Database Engine Class (db_engine.class.php)
 * 
 * MySQLi Implementation
 *
 * This class creates a dabase engine object to interface
 * with a MySQL Database
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 2.0
 * @package Intranet
 */
  
/**
 * DB_Engine
 * @package Project
 * @subpackage classes
 */ 
class db_engine {
	# --- Variables ---
	/**
	* Contains the MySQL Host
	* @access var
	* @var string
	*/
	var $mysql_host;
	/**
	* Contains the MySQL Username
	* @access var
	* @var string
	*/
	var $mysql_user;
	/**
	* Contains the MySQL Password
	* @access var
	* @var string
	*/
	var $mysql_pass;
	/**
	* Contains the MySQL Database to acces
	* @access var
	* @var string
	*/
	var $mysql_db;
	/**
	* Contains the Database Handler
	* @access var
	* @var mysql_connection
	*/
	var $link;
	/**
	* Contains the Logging Engine
	* @access var
	* @var global_log
	*/
	var $logger;
	/**
	* Contains the Database Connection Status
	* @access var
	* @var string
	*/
	var $status;
	/**
	* Sets whether or not to turn Debuggin on
	* @access var
	* @var string
	*/
	var $debug;
	
	# --- Functions ---
	/**
	* Constructor sets up {$mysql_host, $mysql_user, $mysql_pass, $mysql_db, $debug}
	*/
	function db_engine($mysql_host="", $mysql_user="", $mysql_pass="", $mysql_db="", $debug="0"){
		# Initialise Variables
		$this->mysql_host												= $mysql_host;
		$this->mysql_user												= $mysql_user;
		$this->mysql_pass												= $mysql_pass;
		$this->mysql_db													= $mysql_db;
		$this->debug													= $debug;
	}

	/**
	 * Connects to the MySQL database and sets up the database handler.
	*/
	function db_connect(){
		# Check that the connection is still active
		# Create Connection To Database
		$this->link														= new mysqli($this->mysql_host, $this->mysql_user, $this->mysql_pass, $this->mysql_db);
		$this->err_handler();
	}
	
	/**
	 * Connects to the database, executes a query, returns the result
	 * if needed, and closes the connection.
	 * @param string $query The SQL query to execute. 
	 * @return mysql_result
	*/
	function query($query){
		# Connect To Database
		$this->db_connect();
		
		# Execute SQL Command
		$result 														= $this->link->query($query);
		
		# Handle Errors
		$this->err_handler();
		
		# Return Result
		return $result;
	}
	
	/**
	 * Connects to the database, executes a query, returns an array
	 * of objects.
	 * @param string $query The SQL query to execute. 
	 * @return Array
	*/
	function fetch($query){
		# Connect To Database
		$this->db_connect();
		
		# Execute SQL Command
		$result 														= $this->query($query);
		
		# Handle Errors
		$this->err_handler();
		
		# Get Array
		$arr															= array();
		while ($item													= $result->fetch_object()) {
			$arr[]														= $item;
		}
		
		# Return array
		return $arr;
	}
	
	/**
	 * Connects to the database, executes a query, returns an array
	 * of objects.
	 * @param string $query The SQL query to execute. 
	 * @return Array
	*/
	function fetch_one($query){
		# Connect To Database
		$this->db_connect();
		
		# Execute SQL Command
		$result 														= $this->query($query);
		
		# Handle Errors
		$this->err_handler();
		
		# Get Array
		$item															= $result->fetch_object();
		
		# Return array
		return $item;
	}
	
	/**
	 * Connects to the database, executes a query, returns a single value.
	 * @param string $query The SQL query to execute. 
	 * @return Array
	*/
	function fetch_single($query){
		# Connect To Database
		$this->db_connect();
		
		# Execute SQL Command
		$result 														= $this->query($query);
		
		# Handle Errors
		$this->err_handler();
		
		# Get Data
		$row															= $result->fetch_row();
		$data															= $row[0];
		
		# Return Data
		return $data;
	}
	
	/**
	 * Handles any errors that are generated by MySQL. If $debug is set
	 * to true, then the error will be displayed.
	 * @param mysql_error $err The MySQL Error object.
	*/
	function err_handler(){
		# Global Variables
		global $_GLOBALS;
		
		# Check for the existance of an error message
		if ($this->link->connect_errno) {
		    # Display Standard Error Message
			print "<div class='info'>Oops... It seems there has been an error on the system. A message has been sent to the system admins to sort out.<br /><br />\n";
			print "<a href='?p=home'>Click Here to Continue</a></div>\n";
			
			# Generate Detailed Error Message
			$err 														.= ($this->query)? "<br />QUERY = {$query}" : "";
			$trace 														= print_r(debug_backtrace(), 1);
			$error_message												= "<b>DB Error</b>: ";
			$error_message												.= "Failed to connect to MySQL: (" . $this->link->connect_errno . ") ";
			$error_message												.= $this->link->connect_error . "<br /><br />";
			$error_message												.= "Host Info: " . $this->link->host_info;
			$error_message												.= "<b>Stack Trace</b><br /><br />$trace";
			
			# Send Email to Admin
			mail($_GLOBALS['admin_email'], "DB ERROR", $error_message);
			
			# Display Detailed Error Message if Debug is on
			if ($this->debug) {
					print format_plaintext($error_message);
			}
			die();
		}
		else if ($this->link->errno) {
		    # Display Standard Error Message
			print "<div class='info'>Oops... It seems there has been an error on the system. A message has been sent to the system admins to sort out.<br /><br />\n";
			print "<a href='?p=home'>Click Here to Continue</a></div>\n";
			
			# Generate Detailed Error Message
			$err 														.= ($this->query)? "<br />QUERY = {$query}" : "";
			$trace 														= print_r(debug_backtrace(), 1);
			$error_message												= "<b>DB Error</b>: ";
			$error_message												.= "Failed to execute Query: (" . $this->link->errno . ") ";
			$error_message												.= $this->link->error . "<br /><br />";
			$error_message												.= "<b>Stack Trace</b><br /><br />$trace";
			
			# Send Email to Admin
			mail($_GLOBALS['admin_email'], "DB ERROR", $error_message);
			
			# Display Detailed Error Message if Debug is on
			if ($this->debug) {
					print format_plaintext($error_message);
			}
			die();
		}
	}
	
	/**
	 * Inserts a record into a table and returns the uid
	 * @param string $table The table to insert into
	 * @param array $data An array with the row data
	 * @return Integer
	 */
	function insert($table, $data) {
		# Construct Insert Query
		$query															= "INSERT INTO `$table` (";
		$x																= 0;
		foreach ($data as $field => $value) {
			$query														.= ($x)? ", " : "";
			$query														.= " `$field` ";
			$x++;
		}
		$query															.= " ) VALUES ( ";
		$x																= 0;
		foreach ($data as $field => $value) {
			$query														.= ($x)? ", " : "";
			$query														.= " \"{$value}\" ";
			$x++;
		}
		$query															.= ")";
		
		# Execute Query
		$this->query($query);
		
		# Return UID
		return mysqli_insert_id($this->link);
	}
	
	/**
	 * Updates the data of a row in a table.
	 * @param string $table The table name to update.
	 * @param array $data An array with the new values.
	 * @param array $id The index of the row to update.
	*/
	function update($table, $data, $id) {
		# Construct Update Query
		$query 															= "UPDATE `$table` ";
		$x 																= 0;
		foreach ($data as $field => $value){
			if ($x 														== 0){
				$query													.= "SET ";
				$query													.= "`{$field}` = \"{$value}\" ";
				$x++;
			}
			else {
				$query													.= ", `{$field}` = \"{$value}\" ";
			}
			$x++;
		}
		$x 																= 0;
		$query 															.= " WHERE ";
		foreach ($id as $field => $value) {
			$query														.= ($x)? " AND " : "";
			$query														.= "`{$field}` = \"{$value}\" ";
			$x++;
		}
		
		# Execute Query
		$this->query($query);
	}
	
	/**
	 * Delete data from a table
	 * @param String $table The Table from which to delete
	 * @param String $field The field to match
	 * @param String $value The value to match
	 */
	function delete($table, $field, $value) {
		$this->query("	DELETE
							FROM `$table`
						WHERE
							`$field` = \"$value\"");
	}
	
	/**
	 * Returns a single value from a table using search criteria
	 * @param String $table The table to search withing
	 * @param String $return_field The field to return
	 * @param String $search_field The field to match against
	 * @param String $search_value The value to match the search_field with 
	 * @return String
	 */
	function get_data($table, $return_field, $search_field, $search_value) {
		# Construct Query
		$query															= "	SELECT
																				`$return_field`
																			FROM
																				`$table`
																			WHERE
																				`$search_field` = \"{$search_value}\"";
		
		# Fetch Data
		$data															= $this->fetch_single($query);
		
		# Return Data
		return $data;
	}
	
	/**
	 * Sets the `active` field to 0
	 * @param String $table The table name
	 * @param Integer $uid The UID of the record to disable
	 */
	function disable($table, $uid) {
		$this->update(
			$table,
			array(
				"active"												=> 0
			),
			array(
				"uid"													=> $uid
			)
		);
	}
	
	/**
	 * Sets the MySQL Host
	*/
	function set_mysql_host($data){
		$this->mysql_host												= $data;
	}
	
	/**
	 * Sets the MySQL Host
	*/
	function set_logger($logger){
		$this->logger 													= $logger;
	}
	
	/**
	 * Sets the MySQL Username
	*/
	function set_mysql_user($data){
		$this->mysql_user 												= $data;
	}
	
	/**
	 * Sets the MySQL Password
	*/
	function set_mysql_pass($data){
		$this->mysql_pass 												= $data;
	}
	
	/**
	 * Sets the MySQL Database
	*/
	function set_mysql_db($data){
		$this->mysql_db 												= $data;
	}
	
	/**
	 * Turns debuggin mode on or off
	*/
	function set_debug($debug){
		$this->debug 													= $debug;
	}
	
	/**
	 * Checks the status of a table
	 * @param String $table The table name
	 * @return String $result
	 */
	function check_table($table) {
		$result															= $this->fetch_one("CHECK TABLE `{$table}`");
		return $result->Msg_text;
	}
}
