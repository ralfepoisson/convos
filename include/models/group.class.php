<?php
/**
 * Project
 * 
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 2.0
 * @package Project
 */

# ==========================================================================================
# CLASS
# ==========================================================================================

class Group extends Model {
	
	# --------------------------------------------------------------------------------------
	# ATTRIBUTES
	# --------------------------------------------------------------------------------------
	
	var $uid;
	
	# --------------------------------------------------------------------------------------
	# METHODS
	# --------------------------------------------------------------------------------------
	
	/**
	 * Constructor
	 * 
	 * Set the Table and the UID of the object.
	 * 
	 * @param $uid Integer: The Unique Identifier of the object.
	 */
	function __construct($uid=0) {
		# Set Table
		$this->table													= "groups";
		
		# Initialize UID from Parameter
		$this->uid														= $uid;
		if ($uid) {
			$this->load();
		}
	}
	
	function check_auth($function) {
		# Global Variables
		global $_db;
		
		# Check Authentication
		$query															= "	SELECT
																				COUNT(*)
																			FROM
																				`group_functions`
																			WHERE
																				`group` = '{$this->uid}'";
		$result															= $_db->fetch_single($query);
		
		# Return Result
		return $result;
	}
	
	/**
	 * Clear out Allowed Functions for this user
	 */
	function clear_auths() {
		# Global Variables
		global $_db;
		
		# Clear out Allowed Functions
		$_db->delete("group_functions", "group", $this->uid);
	}
	
	function get_auths() {
		# Global Variables
		global $_db;
		
		# Get Allowed Functions
		$query															= "	SELECT
																				`function`
																			FROM
																				`group_functions`
																			WHERE
																				`group` = '{$this->uid}'";
		$auths															= $_db->fetch($query);
		
		# Generate Response
		$data															= array();
		foreach ($auths as $item) {
			$data[]														= $item->function;
		}
		
		# Return Response
		return $data;
	}
	
	/**
	 * Add Allowed Function
	 * @param String $function The function that this user is allowed to perform
	 */
	function add_allowed_function($function) {
		# Global Variables
		global $_db;
		
		# Add Function
		$_db->insert(
			"group_functions",
			array(
				"user"												=> $this->uid,
				"function"											=> $function
			)
		);
	}
	
}

# ==========================================================================================
# THE END
# ==========================================================================================
