<?php
include_once('../init.php');

if(!$session->logged_in){
    echo 'Error: Session expires.';
    die();
}

$cfgName =  Tools::getValue('cfgName');
$filterUser =  Tools::getValue("user");
$cutout =  Tools::getValue("cutout");

$parentDir = Configurations::getConfiguration('REATLAS_CLIENT_PATH').'/data/'.$filterUser;

if(is_dir($parentDir))
{
    
$result = '';  
if(is_file($parentDir."/".$filterUser."_".$cutout."_layout_".$cfgName.".npy"))
{
   $layout_file = $parentDir."/".$filterUser."_".$cutout."_layout_".$cfgName.".npy";

$command = "python ".Configurations::getConfiguration('REATLAS_CLIENT_PATH')."/cmd_layout_details.py";
$command .= " $layout_file 2>&1";
/*
$myFile = "command_tst.txt";
$fh = fopen($myFile, 'a') or die("can't open file");
fwrite($fh, $command."\n");
*/

$pid = popen( $command,"r");


while( !feof( $pid ) )
{
$result .= fread($pid, 256);
}
pclose($pid);
}

$outArr=array();
$outArr['type']="Success";
$outArr['text']="Layout detail";
$outArr['desc']="Layout detail for ".$cfgName;
$outArr['traceback']= '';
$outArr['data'] = $result ;
echo json_encode($outArr);
}
?>
