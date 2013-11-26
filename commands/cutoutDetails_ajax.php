<?php
include_once('../init.php');

$filterUser = $_REQUEST["user"];
$cutout = $_REQUEST["cutout"];
// filename: data/auesg/meta_Denmark.npz
// cmd_cutout_details.py Pepsimax.imf.au.dk Denmark data/auesg/meta_Denmark.npz --username manila --password iet5hiuC --cutoutuser auesg 
$fileName = "data/".$filterUser."/meta_".$cutout.".npz";
$param = Configurations::getConfiguration('PEPSI_SERVER')." ".$cutout." ".Configurations::getConfiguration('REATLAS_CLIENT_PATH')."/".$fileName." --username ".Configurations::getConfiguration('PEPSI_ADMIN_USER')." --password ".Configurations::getConfiguration('PEPSI_ADMIN_PASS')." --cutoutuser ".$filterUser;

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
