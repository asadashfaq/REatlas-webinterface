<?php
include_once('init.php');

$filterUser = $_REQUEST["user"];
$cutout = $_REQUEST["cutout"];
$param = "-u ".$filterUser." -c ".$cutout;

$command = "python ".REATLAS_CLIENT_PATH."/scripts/getCutoutDetails.py";
$command .= " $param 2>&1";

$pid = popen( $command,"r");
$result = '';

while( !feof( $pid ) )
{
$result .= fread($pid, 256);
}
pclose($pid);


echo $result;   
