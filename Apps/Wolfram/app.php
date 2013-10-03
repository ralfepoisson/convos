<?php
/**
 * General ConvOS App
 * 
 * This is a sample application to demonstrate the ConvOS platform.
 * 
 * @author Ralfe Poisson <ralfepoisson@gmail.com>
 * @version 0.0.1
 * @license GPL3
 */

# ====================================================================
# CONFIGURATION
# ====================================================================

$api_key 																= "2PWVUY-R92WA52EG8";
$api_server 															= "http://api.wolframalpha.com/v2/query?appid={$api_key}&input=";

# ====================================================================
# PROCESS REQUEST
# ====================================================================

# Prepare Query String
array_shift($argv);
$arg 																	= strtoupper(implode(" ", $argv));

# Handle the conversation starter
if ($arg == "ACCESS SCIENCE DATABASE" || $arg == "ACCESS WOLFRAM ALPHA" || $arg == "ACCESS THE WOLFRAM DATABASE") {	
	output("Science database accessed.");
}
# Handle the end of the conversation
else if ($arg == "CLOSE DATABASE" || $arg == "END" || $arg == "CLOSE SCIENCE DATABASE") {
	output("Science Database Closed. [END:CONVERSATION]");
}
# Handle the conversation requests
else {
	# Clean up Input
	$arg 																= str_replace("WHAT IS THE VALUE OF ", "", $arg);
	$arg 																= str_replace("WHAT IS ", "", $arg);
	
	# Construct URL
	$url 																= $api_server . urlencode($arg);
	
	# Get the Response from Wolfram
	$response 															= get_web_page($url);
	
	# Interpret the response
	$result 															= simplexml_load_string($response);
	
	# Return the Answer
	if (isset($result->pod[1]->subpod->plaintext)) {
		$answer 														= (string)$result->pod[1]->subpod->plaintext;
		output($answer);
	}
	else {
		output("I am sorry. I cannot find the answer to that.");
	}
}

# ====================================================================
# FUNCTIONS
# ====================================================================

function get_web_page($url) {
	# Initialise a CURL object
	$ch 																= curl_init(); 
	
	# Configure the CURL object
	curl_setopt($ch, CURLOPT_URL, $url); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	
	# Execute the CURL request
	$output 															= curl_exec($ch);
	
	# Close the CURL object
	curl_close($ch);
	
	# Return the result
	return $output;
}

function output($message) {
	print "<?xml version='1.0' ?><document><message>{$message}</message></document>\n";
}

# ====================================================================
# THE END
# ====================================================================
