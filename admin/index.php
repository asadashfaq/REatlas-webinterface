<?php

define('_ADMIN_DIR_', getcwd());

require(dirname(__FILE__).'/AdminInit.php');

if (!$session->logged_in)
   header("Location: login.php");
else {
if (!$session->isAdmin())
   header("Location: ".Configurations::getConfiguration('SITE_DIRECTORY')."main.php");
}

$action = isset($_REQUEST['action'])?$_REQUEST['action']:NULL;

$adminController = new AdminFrontController();
$adminController->display();
