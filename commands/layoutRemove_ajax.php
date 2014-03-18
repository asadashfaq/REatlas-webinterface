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

$layoutName =  Tools::getValue('layoutName');
$filterUser =  Tools::getValue("user");
$cutout =  Tools::getValue("cutout");

$parentDir = Configurations::getConfiguration('REATLAS_CLIENT_PATH').'/data/'.$currentUser->aulogin;

if(is_dir($parentDir))
{
    
$result = '';  
$layout_file = "layout_".$currentUser->aulogin."_".$cutout."_".$layoutName.".npy";
  
if(is_file($parentDir."/".$layout_file))
{
   @unlink($parentDir."/".$layout_file);
}

$param = Configurations::getConfiguration('PEPSI_SERVER')
        ." --username ".$currentUser->aulogin
        ." --password ".$currentUser->aupass
        ." ".$layout_file
        ." --output JSON";

$command = "python ".Configurations::getConfiguration('REATLAS_CLIENT_PATH')."/cmd_file_remove.py";
$command .= " $param 2>&1";
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

echo $result ;

}else
    errorReturn("Technical Error.");
?>
