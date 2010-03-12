<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ', 							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 					'ab');
define('FOPEN_READ_WRITE_CREATE', 				'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 			'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

/*
|--------------------------------------------------------------------------
| Twitter
|--------------------------------------------------------------------------
|
| These parameters are required for Twitter functionality.
|
*/

define('TWITTER_CONSUMER_KEY', 'INSERT CONSUMER KEY HERE'); // Edit Me
define('TWITTER_CONSUMER_SECRET', 'INSERT CONSUMER SECRET HERE'); // Edit Me

/*
|--------------------------------------------------------------------------
| Facebook Connect
|--------------------------------------------------------------------------
|
| These parameters are required for Facebook Connect functionality.
|
*/

define('APP_ID', 'INSERT APP ID HERE'); // Edit Me
define('API_KEY', 'INSERT API KEY HERE'); // Edit Me
define('SECRET', 'INSERT APP SECRET HERE'); // Edit Me
define('CANVAS_PAGE_URL', 'INSERT CANVAS PAGE URL HERE'); // Excluding Traling Slash
define('CANVAS_CALLBACK_URL', 'INSERT CANVAS CALLBACK URL HERE'); // Excluding Trailing Slash

/*
|--------------------------------------------------------------------------
| Google Friend Connect
|--------------------------------------------------------------------------
|
| These parameters are required for Google Friend Connect functionality.
|
*/

define('SITE_ID', 'INSERT APP ID HERE'); // Edit Me

/* End of file constants.php */
/* Location: ./system/application/config/constants.php */