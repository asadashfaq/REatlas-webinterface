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

$param = Configurations::getConfiguration('PEPSI_SERVER')." --username ".$currentUser->aulogin." --password ".$currentUser->aupass." --cutoutuser ".$filterUser." --output JSON";
        
$command = "python ".Configurations::getConfiguration('REATLAS_CLIENT_PATH')."/cmd_list_cutouts.py";
$command .= " $param 2>&1";

$pid = popen( $command,"r");
$result = '';

while( !feof( $pid ) )
{
$result .= fread($pid, 256);
}
pclose($pid);
 

if(strpos($result,"Invalid username")!==false) {
    //echo 'Error: Technical problem. Contact Administrator.';
}
else {
     echo $result;    
}
die();
