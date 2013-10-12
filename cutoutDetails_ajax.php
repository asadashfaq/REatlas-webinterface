<?php

$filterUser = $_REQUEST["user"];
$cutout = $_REQUEST["cutout"];
$param = "-u ".$filterUser." -c ".$cutout;

$command = "python /development/AU/REatlas-client/scripts/getCutoutDetails.py";
$command .= " $param 2>&1";

$pid = popen( $command,"r");
$result = '';

while( !feof( $pid ) )
{
$result .= fread($pid, 256);
}
pclose($pid);


echo $result;   
