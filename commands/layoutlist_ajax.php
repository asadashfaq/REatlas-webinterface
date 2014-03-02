<?php
include_once('../init.php');

if(!$session->logged_in){
    echo 'Error: Session expires.';
    die();
}

$currentUser = new user($session->userid);

if($currentUser->aulogin =="" ||$currentUser->aulogin == null){
    echo 'Error: Technical problem. Contact Administrator.';
    die();
}
$filterUser =  Tools::getValue("user");
$cutout =  Tools::getValue("cutout");
//echo "filterUser is $filterUser";
$param = Configurations::getConfiguration('PEPSI_SERVER')
        ." --username ".$currentUser->aulogin
        ." --password ".$currentUser->aupass
        ." --layoutuser ".$filterUser
        ." --cutout ".$cutout
        ." --output JSON";
        
$command = "python ".Configurations::getConfiguration('REATLAS_CLIENT_PATH')."/cmd_list_layouts.py";
$command .= " $param 2>&1";

//echo "command is: $command";


$pid = popen( $command,"r");
$result = '';

while( !feof( $pid ) )
{
$result .= fread($pid, 256);
}
pclose($pid);


echo $result;   
die();