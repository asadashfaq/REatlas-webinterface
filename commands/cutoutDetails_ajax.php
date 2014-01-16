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

if(!$cutout)
    die('Error: Cutout is not defined');

// filename: data/auesg/meta_Denmark.npz
// cmd_cutout_details.py Pepsimax.imf.au.dk Denmark data/auesg/meta_Denmark.npz --username manila --password iet5hiuC --cutoutuser auesg 
$fileName = "data/".$filterUser."/meta_".$cutout.".npz";
$param = Configurations::getConfiguration('PEPSI_SERVER')." ".$cutout." ".Configurations::getConfiguration('REATLAS_CLIENT_PATH')."/".$fileName." --username ".$currentUser->aulogin." --password ".$currentUser->aupass." --cutoutuser ".$filterUser;

$command = "python ".Configurations::getConfiguration('REATLAS_CLIENT_PATH')."/cmd_cutout_details.py";
$command .= " $param 2>&1";

$pid = popen( $command,"r");
$result = '';

while( !feof( $pid ) )
{
$result .= fread($pid, 256);
}
pclose($pid);


echo $result;   
