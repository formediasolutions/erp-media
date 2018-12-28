<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

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
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

/*
|--------------------------------------------------------------------------
| Website Title
|--------------------------------------------------------------------------
*/
define('TITLE',             "ERP MEDIA | ");
define('COMPANY_NAME',      "ERP MEDIA");

/*
|--------------------------------------------------------------------------
| Server/Base URL
|--------------------------------------------------------------------------
*/
define('SCHEMA', ( @$_SERVER["HTTPS"] == "on" ) ? "https://" : "http://");
define('BASE_URL', SCHEMA . ( isset( $_SERVER["SERVER_NAME"] ) ? $_SERVER["SERVER_NAME"] : '' ) . '/');


/*
|--------------------------------------------------------------------------
| Document Root Path
|--------------------------------------------------------------------------
*/
define('ROOTPATH', rtrim(@$_SERVER['DOCUMENT_ROOT'], '/') . '/');

/*
|--------------------------------------------------------------------------
| Page Settings
|--------------------------------------------------------------------------
|
| Frontend and Backend page
|
*/
define('VIEW_FRONT',        'frontend/');
define('VIEW_BACK',         'backend/');

/*
|--------------------------------------------------------------------------
| Assets Path Settings
|--------------------------------------------------------------------------
|
| These modes for assets path setting
|
*/
define('CSS_PATH',          BASE_URL . 'assets/css/');
define('IMG_PATH',          BASE_URL . 'assets/img/');
define('JS_PATH',           BASE_URL . 'assets/js/');
define('VENDORS_PATH',      BASE_URL . 'assets/vendors/');
define('ASSETS_PATH',       BASE_URL . 'assets/');
define('LIBRARY_PATH',      BASE_URL . 'application/libraries/');

define('BOOTSTRAP_PATH',    BASE_URL . 'assets/bootstrap/');
define('PLUGIN_PATH',       BASE_URL . 'assets/js/plugins/');
define('FRONTEND_PATH',     BASE_URL . 'assets/frontend/');
define('AVATAR_PATH',       BASE_URL . 'assets/img/member/');
define('COMINGSOON_PATH',   BASE_URL . 'assets/comingsoon/');
define('MAINTENANCE_PATH',  BASE_URL . 'assets/maintenance/');

/*
|--------------------------------------------------------------------------
| MM Constant 
|--------------------------------------------------------------------------
|
| These modes for set cookie
|
*/
define('AUTH_KEY',          '%4 N}|@na%Q;Tq$!3m?1^=u|PO_OO?!6Cr_l4h%MLbB<qu?%oj}l)+C~7;8p!vqI');
define('SECURE_AUTH_KEY',   '9`)6N;cRNBBEQG<}6P5zNS*F~#NU| uBsFb$K33-ynxgX1FE=SUP;BF-^@)Bj`CO');
define('LOGGED_IN_KEY',     '~16PA%~YtB1eWEvbozyjv01vo*4`[q3bI,O]I_].#9~S>qZHWgv/F??$=+?>uQ2l');
define('NONCE_KEY',         '))Z3:G![C@Oyb2bi=,OedV,n97J5b2M/Z&IJ*SmK*j/ApHxsRVt.cq|RDsY1mQ,)');
define('AUTH_SALT',         'w?e[S&y@,Pv7qJ&i.3*_I}{&uVm=2%B3AHt3{?PjFwvOQ|vYA^IPTf.^@,vx=d8&');
define('SECURE_AUTH_SALT',  '/wKdAgx=D?{wbw8{Mi-57JG6(+rfS:]MD{Gxp`dWyr^WyCtW]+ihseR]Rmh5p=N*');
define('LOGGED_IN_SALT',    'E(:=@55g ^ODRh9i6>PVRpW4J/u-}70N}7ALGnBey1hg7_#|-@1G<c8g]*|Fp]Q1');
define('NONCE_SALT',        'l`)q2S5Y6rY&%/Q`U,17@KfP)Okc?[Dwxqq,P*X!vh!Lp0/E|cw^d?z6D:F|4FuP');

/*
|--------------------------------------------------------------------------
| MM Unique Hash Cookie
|--------------------------------------------------------------------------
|
| Used to guarantee unique hash cookies
|
*/
if ( !defined('COOKIEHASH') )           define('COOKIEHASH', md5('[:erp:]'));
if ( !defined('MEMBER_COOKIE') )        define('MEMBER_COOKIE', 'erpmember_' . COOKIEHASH);
if ( !defined('PASS_COOKIE') )          define('PASS_COOKIE', 'erppass_' . COOKIEHASH);
if ( !defined('AUTH_COOKIE') )          define('AUTH_COOKIE', 'erp_' . COOKIEHASH);
if ( !defined('SECURE_AUTH_COOKIE') )   define('SECURE_AUTH_COOKIE', 'erp_sec_' . COOKIEHASH);
if ( !defined('LOGGED_IN_COOKIE') )     define('LOGGED_IN_COOKIE', 'erp_logged_in_' . COOKIEHASH);

/*
|--------------------------------------------------------------------------
| Mailer Engine
|--------------------------------------------------------------------------
|
| Swift Mailer Location
|
*/
define('SWIFT_MAILSERVER',      realpath(dirname(__FILE__) . '/..') . DIRECTORY_SEPARATOR . '/libraries/swiftmailer/swift_required.php');

/*
|--------------------------------------------------------------------------
| Php Grid Lite Engine
|--------------------------------------------------------------------------
|
| Php Grid Lite Location
|
*/
define('PHPGRID_LITESERVER',      realpath(dirname(__FILE__) . '/..') . DIRECTORY_SEPARATOR . '/libraries/phpGrid_Lite/conf.php');

/**
 * Member type constants
 * @author	Iqbal
 */
define('MEMBER',                1);
define('ADMINISTRATOR',         2);
define('SALES',                 3);


/**
 * Status
 * @author	Rifal
 */
define('NONACTIVE',             0);
define('ACTIVE',                1);
define('BANNED',                2);

/**
 * CSS and JS versioning
 * Used to version custom CSS and JS so that user do not have clear their browser cache manually
 * @author	Ahmad
 */
define('CSS_VER_FRONT',         '1.0.0');
define('CSS_VER_BACK',          '1.0.0');
define('JS_VER_FRONT',          '1.0.0');
define('JS_VER_BACK',           '1.0.4');

/* End of file constants.php */
/* Location: ./application/config/constants.php */
