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
$filterUser =  Tools::getValue("user");
$cutout =  Tools::getValue("cutout");
//echo "filterUser is $filterUser";
$param = Configurations::getConfiguration('PEPSI_SERVER')
        ." --username ".$currentUser->aulogin
        ." --password ".$currentUser->aupass
        ." --layoutuser ".$currentUser->aulogin
        ." --cutout ".$cutout
        ." --output JSON";
        
$command = "python ".Configurations::getConfiguration('REATLAS_CLIENT_PATH')."/cmd_list_layouts.py";
$command .= " $param 2>&1";

//echo "command is: $command";


$pid = popen( $command,"r");
$result = '';

while( !feof( $pid ) )
{
$result .= fread($pid, 256);
}
pclose($pid);


echo $result;   
die();
