<?php
/**
 * Project
 * 
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 1.0
 * @package Project
 */

# =========================================================================
# FUNCTIONS
# =========================================================================

function example_select($prefix="", $default="") {
	# Construct Data
	$values									= array(	"New",
														"Pre Initial Meeting",
														"Post Initial Meeting",
														"Quotation",
														"Project Inception",
														"Design",
														"Development",
														"Testing",
														"Implementation",
														"Complete"
													);
	
	# Generate HTML
	$html									= generate_select($prefix . "status", $values, $default);
	
	# Return HTML
	return $html;
}

# =========================================================================
# THE END
# =========================================================================

?>