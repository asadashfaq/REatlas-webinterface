<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// Main stuff here
define('START',microtime());

// Users

define('DEFAULT_USER_GROUP','auesg');
define('DEFAULT_USER','gorm');


//selection
define('SELECTION_TOOLBAR',false);

define('_MODE_DEV_', true);

// Report all PHP errors
error_reporting(-1);

// Same as error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);


/**
 * Database Constants - these constants are required
 * in order for there to be a successful connection
 * to the MySQL database. Make sure the information is
 * correct.
 */
define("_DB_SERVER_", "localhost");
define('_SITE_DIRECTORY_', '/reatlas/');
define("_DB_USER_", "reatlas");
define("_DB_PASS_", "reatlas");
define("_DB_NAME_", "reatlas");
define('_DB_PREFIX_', '');
define('_MYSQL_ENGINE_', 'MyISAM');

/**
 * Database Table Constants - these constants
 * hold the names of all the database tables used
 * in the script.
 */
define("TBL_USERS", "users");
define("TBL_USER_PROFILE", "users_profile");
define("TBL_ACTIVE_USERS",  "active_users");
define("TBL_ACTIVE_GUESTS", "active_guests");
define("TBL_BANNED_USERS",  "banned_users");

/**
 * Special Names and Level Constants - the admin
 * page will only be accessible to the user with
 * the admin name and also to those users at the
 * admin user level. Feel free to change the names
 * and level constants as you see fit, you may
 * also add additional level specifications.
 * Levels must be digits between 0-9.
 */
define("ADMIN_NAME", "admin");    //1. admin conrol all
define("GUEST_NAME", "Guest");   
define("ADMIN_LEVEL", 9);        // 2. admin level .. control the master
define("MASTER_LEVEL", 8);       // 3. master level .. master control the agent
define("AGENT_LEVEL",  1);       // 4. agent level .. agent control the member
define("AGENT_MEMBER_LEVEL", 2); // 5. agent member level .. member control his/her own account
define("GUEST_LEVEL", 0);        // 6. guest level .. guest only control himself

/**
 * This boolean constant controls whether or
 * not the script keeps track of active users
 * and active guests who are visiting the site.
 */
define("TRACK_VISITORS", true);

/**
 * Timeout Constants - these constants refer to
 * the maximum amount of time (in minutes) after
 * their last page fresh that a user and guest
 * are still considered active visitors.
 */
define("USER_TIMEOUT", 30);
define("GUEST_TIMEOUT", 15);

/**
 * Cookie Constants - these are the parameters
 * to the setcookie function call, change them
 * if necessary to fit your website. If you need
 * help, visit www.php.net for more info.
 * <http://www.php.net/manual/en/function.setcookie.php>
 */
// define("COOKIE_EXPIRE", 60*60*24*30);  //30 days lang
define("COOKIE_EXPIRE", 60*60*24*100);  //100 days by default
define("COOKIE_PATH", "/");  //Avaible in whole domain

/**
 * Email Constants - these specify what goes in
 * the from field in the emails that the script
 * sends to users, and whether to send a
 * welcome email to newly registered users.
 */
define("EMAIL_FROM_NAME", "Admin AU");
define("EMAIL_FROM_ADDR", "manila@au.dk");
define("EMAIL_WELCOME", true);

/**
 * This constant forces all users to have
 * lowercase usernames, capital letters are
 * converted automatically.
 */
define("ALL_LOWERCASE", true);


/* REATlas Client API Settings
 * 
 */
define("REATLAS_CLIENT_PATH", "/development/AU/REatlas-client");