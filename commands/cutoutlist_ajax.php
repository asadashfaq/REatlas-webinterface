<?php
include_once('../init.php');

$filterUser = $_REQUEST["user"];
$param = Configurations::getConfiguration('PEPSI_SERVER')." --username ".Configurations::getConfiguration('PEPSI_ADMIN_USER')." --password ".Configurations::getConfiguration('PEPSI_ADMIN_PASS')." --cutoutuser ".$filterUser." --output JSON";
        
$command = "python ".Configurations::getConfiguration('REATLAS_CLIENT_PATH')."/cmd_list_cutouts.py";
$command .= " $param 2>&1";

$pid = popen( $command,"r");
$result = '';

while( !feof( $pid ) )
{
$result .= fread($pid, 256);
}
pclose($pid);

echo $result;