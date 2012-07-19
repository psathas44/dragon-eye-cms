<?php

/* ****************************************************************** /
	 -----------------------------------------------------------
	 -                                                         -
	- ######################################################### -
	- Dragon Eye CMS System							            -
	- Visit www.DragonEyeCMS.com system in order to report BUGS -
	- For contact, send an email at michael9ufo@yahoo.co.uk		-
	- Note* This is an open-source project (free for anyone)    -
	- In order to use it you must only keep our copyright       -
	- ######################################################### -
	-                                                           -
	- Best regards,                                             -
	 -		Michael9ufo                                        -
	 -----------------------------------------------------------
/* ***************************************************************** */

// Define the constant which we'll need later to check if a webpage it's not accessed from other sides.
define('DragonEye', 'V.1.7');

// Define website location
define('CMS_DIR', dirname(__FILE__));

// Define user IP Address
define('USER_IP', $_SERVER['REMOTE_ADDR']);

session_start();
session_name('DragonEyeCMS');

function sep_path($path)
{

	// Replace every " / " and/or " \ " of a path with default directory separator
	return preg_replace('/[\/\\\]/', DIRECTORY_SEPARATOR, $path);

}

// Here we load all we need in order to make website work.
@require_once(sep_path(CMS_DIR.'/libraries/main.class.inc'));
@require_once(sep_path(CMS_DIR.'/libraries/anti-flood.class.inc'));
@require_once(sep_path(CMS_DIR.'/libraries/exceptions.class.inc'));
@require_once(sep_path(CMS_DIR.'/libraries/configurations.class.inc'));
@require_once(sep_path(CMS_DIR.'/libraries/D64Code.class.inc'));

// Load Anti-Flood config file
Configs::load('flood');
// Load main config file
Configs::load('main');
// Load DB Settings config file
Configs::load('connection');
// Load Template config file
Configs::load('template');
// Load DB Structure config file
Configs::load('db_structure');
// Load DB Queryes
Configs::load('db_queryes');
// Load Access levels config file
Configs::load('access_levels');
// Load Statistics config file
Configs::load('statistics');
// Load Vote System config file
Configs::load('vote_system');
// Load Donate System config file
Configs::load('donate_system');
// Load Shop config file
Configs::load('shop');

if(!$GLOBALS['CONFIG_DEV_DEBUG'])
{

	error_reporting(0);
	@ini_set(‘display_errors’, 0);

}

@require_once(sep_path(CMS_DIR.'/libraries/language.class.inc'));
@require_once(sep_path(CMS_DIR.'/libraries/template.class.inc'));
@require_once(sep_path(CMS_DIR.'/libraries/account.class.inc'));
@require_once(sep_path(CMS_DIR.'/libraries/content.class.inc'));
@require_once(sep_path(CMS_DIR.'/libraries/mail.class.inc'));

try
{

	// Instantiate Main class
	$main = new Main(isset($_GET['page']) ? $_GET['page'] : $GLOBALS['CONFIG_DEFAULT_PAGE']);

	// Now we are just loading the web page
	$main->load();

	// After page loaded, we must unload it (close mysql/mssql connections etc)
	unset($main);

}
catch (Dragon_Eye_Exception $e)
{

	echo $e->errorMSG();
	return false;

}
