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

class UserGroup extends Model {
	
	# --------------------------------------------------------------------------------------
	# ATTRIBUTES
	# --------------------------------------------------------------------------------------
	
	var $user_id;
	var $groups;
	
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
	function __construct($user_id) {
		# Set Table
		$this->table													= "user_groups";
		
		# Initialize UID from Parameter
		$this->uid														= $uid;
		if ($uid) {
			$this->load();
		}
	}
	
	function get_groups() {
		# Global Variables
		global $_db;
		
		# Get Groups
		$query															= "	SELECT
																				`group_id`
																			FROM
																				`user_groups`
																			WHERE
																				`user_id` = '{$this->user_id}'
																				AND `active` = 1";
		$data															= $_db->fetch($query);
		$this->groups													= array();
		foreach ($data as $item) {
			$this->groups[]												= new Group($item->group_id);
		}
		
		# Done
		return true;
	}
	
	function check_group($group_id) {
		# Get Groups
		$this->get_groups();
		
		# Check if Group Exists
		foreach ($this->groups as $group) {
			if ($group->uid == $group_id) {
				return true;
			}
		}
	}
	
	function add_group($group_id) {
		# Global Variables
		global $_db;
		
		# Add Group
		$_db->insert(
			"user_groups",
			array(
				"datetime"												=> date("Y-m-d H:i:s"),
				"user"													=> get_user_uid(),
				"group_id"												=> $group_id,
				"user_id"												=> $this->user_id
			)
		);
		
		# Done
		return true;
	}
	
	function remove_group($group_id) {
		# Global Variables
		global $_db;
		
		# Remove Group
		$_db->query("DELETE FROM `user_group` WHERE `user_id` = '{$this->user_id}' AND `group_id` = '{$group_id}'");
		
		# Done
		return true;
	}
	
	function clear_groups() {
		# Global Variables
		global $_db;
		
		# Clear Groups
		$_db->delete("user_groups", "user_id", $this->user_id);
		
		# Done
		return true;
	}
}

# ==========================================================================================
# THE END
# ==========================================================================================

