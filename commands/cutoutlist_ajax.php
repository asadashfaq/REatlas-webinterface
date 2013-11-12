<?php
include_once('../init.php');

$filterUser = $_REQUEST["user"];
$param = PEPSI_SERVER." --username ".PEPSI_ADMIN_USER." --password ".PEPSI_ADMIN_PASS." --cutoutuser ".$filterUser." --output JSON";
        
$command = "python ".REATLAS_CLIENT_PATH."/cmd_list_cutouts.py";
$command .= " $param 2>&1";

$pid = popen( $command,"r");
$result = '';

while( !feof( $pid ) )
{
$result .= fread($pid, 256);
}
pclose($pid);

echo $result;