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

$job_id =  Tools::getValue("job_id");

if(!$job_id)
    errorReturn('Error: Job ID is not defined');

$param = Configurations::getConfiguration('PEPSI_SERVER')." ".$job_id." "
        ." --username ".$currentUser->aulogin
        ." --password ".$currentUser->aupass;

$command = "python ".Configurations::getConfiguration('REATLAS_CLIENT_PATH')."/cmd_job_status.py";
$command .= " $param --output JSON 2>&1";

$pid = popen( $command,"r");
$result = '';

while( !feof( $pid ) )
{
$result .= fread($pid, 256);
}
pclose($pid);


echo $result;   
