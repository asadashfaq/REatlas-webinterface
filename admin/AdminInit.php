<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once("../init.php");

$include_classes = array(
   // 'user'=>'classes/user.class.php',
    'FrontController' => 'classes/FrontController.php',
    'AdminFront'=>'controllers/AdminFrontController.php',
    'AdminUsers'=>'controllers/AdminUsersController.php',
    'AdminConfiguration'=>'controllers/AdminConfigurationController.php',
    'alias'=>'../config/alias.php'
    
);

$root_dir = dirname(__FILE__) . '/';
foreach ($include_classes as $classname => $classpath) {
    if (file_exists($root_dir . $classpath)) 
        require_once $root_dir . $classpath;
}

