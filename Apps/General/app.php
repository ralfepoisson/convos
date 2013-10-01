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

# Get Commandline Argument
$arg = $argv[1];
if ($arg == "hello") {
	print "Hello\n";
}
else if ($arg == "date") {
	print "Today is the " . date("jS") . " of " . date("F Y") . "\n";
}
