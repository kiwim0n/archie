<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 
/**
 * Init Library
 *
 *
 * LICENSE: GNU General Public License, version 2 (GPLv2)
 * Copyright (c) 2001 - 2011 Ampache.org All Rights Reserved
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License v2
 * as published by the Free Software Foundation
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @package	Ampache
 * @copyright	2001 - 2011 Ampache.org
 * @author	Modified - Karl Vollmer 2012-2013
 * @license	http://opensource.org/licenses/gpl-2.0 GPLv2
 * @link	http://www.ampache.org/
 */

// Use output buffering, this gains us a few things and
// fixes some CSS issues
ob_start();

if (defined('CLI')) {
  $_SERVER['HTTP_HOST'] = false;
  $_SERVER['SERVER_PORT'] = false;
  $_SERVER['SERVER_NAME'] = false;
  $_SERVER['REQUEST_URI'] = false;
}

// Do a check for PHP5 because nothing will work without it
if (floatval(phpversion()) < 5.3) {
	echo "ERROR: Archie requires PHP5.3";
	exit;
}

//error_reporting(E_ERROR); // Only show fatal errors in production

$file_path = dirname(__FILE__);
$prefix = realpath($file_path . "/../");
$configfile = "$prefix/config/settings.php";
require_once $prefix . '/class/general.php';
require_once $prefix . '/class/config.class.php';
require_once $prefix . '/class/database_object.abstract.php';
require_once $prefix . '/lib/phpqrcode/qrlib.php';
require_once $prefix . '/lib/fpdf/fpdf.php';
require_once $prefix . '/class/ui.namespace.php'; 
require_once $prefix . '/class/update.namespace.php'; 

// Define some base level config options
Config::set('prefix',$prefix);

/*
 Check to see if this is http or https
*/
if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || Config::get('force_ssl') == true) {
	$http_type = "https://";
}
else {
	$http_type = "http://";
}

// Use the built in PHP function, suppress errors here so we can handle it
// properly below
$results = parse_ini_file($configfile);
$results['web_prefix']		= $results['web_path']; 
$results['web_path']		= $http_type . $_SERVER['HTTP_HOST'] . $results['web_path'];
$results['ajax_url']		= $results['web_path'] . '/server/ajax.server.php'; 
$results['http_port']		= $_SERVER['SERVER_PORT'];
if (!$results['http_port']) {
	$results['http_port']	= '80';
}
if (!isset($results['site_charset'])) {
	$results['site_charset'] = "UTF-8";
}
if (!isset($results['raw_web_path'])) {
	$results['raw_web_path'] = '/';
}
if (!$_SERVER['SERVER_NAME']) {
	$_SERVER['SERVER_NAME'] = '';
}

/* Variables needed for vauth class */
$results['cookie_path'] 	= $results['raw_web_path'];
$results['cookie_domain']	= $_SERVER['SERVER_NAME'];
$results['cookie_life']		= $results['session_cookielife'];
$results['cookie_secure']	= $results['session_cookiesecure'];
$results['mysql_password']	= $results['database_password'];
$results['mysql_username']	= $results['database_username'];
$results['mysql_hostname']	= $results['database_hostname'];
$results['mysql_db']		= $results['database_name'];

// Define that we've loaded the INIT file
define('INIT_LOADED','1');

Config::set_by_array($results,1);

// check and see if database upgrade(s) need to be done
if (!defined('OUTDATED_DATABASE_OK')) { 
  if (!\Update\Database::check()) { 
    require_once Config::get('prefix') . '/template/database_upgrade.inc.php'; 
    exit(); 
  }
}

// Setup location
\UI\sess::set_location($_SERVER['REQUEST_URI']); 

/* Set a new Error Handler */
if (!defined('NO_LOG')) {
	$old_error_handler = set_error_handler('ampache_error_handler');
}
// In case the local setting is 0
ini_set('session.gc_probability','5');
// If we just need to check the session
if (defined('CHECK_ONLY_SESSION')) { 
  // Verify their session
  if (!vauth::session_exists($_COOKIE[Config::get('session_name')])) { 
    vauth::logout($_COOKIE[Config::get('session_name')]); 
    exit;
  } 
}
elseif (!defined('CLI') AND !defined('NO_SESSION')) { 
	// Verify their session
  if (isset($_COOKIE[Config::get('session_name')])) {
  	if (!vauth::session_exists($_COOKIE[Config::get('session_name')])) { 
  		vauth::logout($_COOKIE[Config::get('session_name')]); 
  		exit;
  	} 
  }

	// Start the session and pull in the user we've got in it
	vauth::check_session();
  if (!isset($_SESSION)) {
    \UI\sess::set_user(User::get_from_username(false));
  }
  else {
  	\UI\sess::set_user(User::get_from_username($_SESSION['sess_data']['username'])); 
  }

	// If nothing comes back kick-em-out
	if (!\UI\sess::$user->uid) { vauth::logout(session_id()); exit; }
	vauth::session_extend(session_id());

  // Load events, errors and clear old
  Event::init(); 
  Error::auto_init(); 
  $_SESSION['errors'] = array(); 
  $_SESSION['warnings'] = array(); 

} 


/* Clean up a bit */
unset($array);
unset($results);
?>
