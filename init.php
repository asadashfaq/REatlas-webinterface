<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'config/configurations.php';

$include_classes= array(
    'Smarty3' => 'Tools/Smarty3/Smarty.class.php',
    'Db' => 'include/classes/db/Db.php',
    'DbMySQLi' => 'include/classes/db/DbMySQLi.php',
    'DbPDO' => 'include/classes/db/DbPDO.php',
    'DbQuery' => 'include/classes/db/DbQuery.php',
    'MySQL' => 'include/classes/db/MySQL.php',
    'database'=>'include/classes/database.class.php',
    'mailer'=>'include/classes/mailer.class.php',
    'form'=>'include/classes/form.class.php',
    'FBCore' => 'Tools/FirePHPCore/FirePHP.class.php',
    'FB' => 'Tools/FirePHPCore/fb.php',
    'Configurations' => 'include/classes/Configurations.class.php',
    'Tools' => 'include/classes/Tools.class.php',
    'functions' => 'functions.php',
    'Profile'=>'include/classes/profile.class.php',
    'session'=>'include/classes/session.class.php',
    'alias'=>'config/alias.php',
    'swiftMailer'=>'Tools/SwiftMailer/swift_required.php'
    
);

$root_dir = dirname(__FILE__) . '/';

if (is_array($include_classes))
{
    foreach ($include_classes as $classpath) {
       if (file_exists($root_dir . $classpath))
           include_once $root_dir . $classpath;
       }
}

ob_start();
