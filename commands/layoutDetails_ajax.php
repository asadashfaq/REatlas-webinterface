<?php
include_once('../init.php');

function errorReturn($message) {
    $outArr=array();
    $outArr['type']="Error";
    $outArr['text']="Error occured";
    $outArr['desc']=$message;
    $outArr['traceback']= '';
    $outArr['data'] = '' ;
    echo json_encode($outArr);
    die();
}

if(!$session->logged_in){
    errorReturn('Error: Session expires.');
}

$currentUser = new user($session->userid);

if($currentUser->aulogin =="" ||$currentUser->aulogin == null){
    errorReturn('Error: Technical problem. Contact Administrator.');
}

$cfgName =  Tools::getValue('cfgName');
$filterUser =  Tools::getValue("user");
$cutout =  Tools::getValue("cutout");

$parentDir = Configurations::getConfiguration('REATLAS_CLIENT_PATH').'/data/'.$currentUser->aulogin;

if(is_dir($parentDir))
{
    
$result = '';  
if(is_file($parentDir."/layout_".$currentUser->aulogin."_".$cutout."_".$cfgName.".npy"))
{
   $layout_file = $parentDir."/layout_".$currentUser->aulogin."_".$cutout."_".$cfgName.".npy";

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
