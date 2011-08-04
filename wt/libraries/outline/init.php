<?php defined('SYSPATH') or die('No direct script access.');

if (!isset($_GET['bypass'])) define('RECOMPILE', true);

try
{
    // Kohana::auto_load('outlinefunctions'); // load custom outline functions
	// Load the view within the current scope
	Kohana::auto_load('outline/engine');

}
catch (Exception $e)
{	
	// Re-throw the exception
	throw $e;
}