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
//echo "filteruser is $filterUser";
$cutout =  Tools::getValue("cutout");
$withdata = Tools::getValue("withdata");
$limit = Tools::getValue("limit");

if(!$cutout)
    errorReturn('Error: Cutout is not defined');

// filename: data/auesg/meta_Denmark.npz
// cmd_cutout_details.py Pepsimax.imf.au.dk Denmark data/auesg/meta_Denmark.npz --username manila --password iet5hiuC --cutoutuser auesg 
$fileName = "data/".$filterUser."/meta_".$cutout.".npz";
$param = Configurations::getConfiguration('PEPSI_SERVER')." ".$cutout." "
        .Configurations::getConfiguration('REATLAS_CLIENT_PATH')."/".$fileName
        ." --username ".$currentUser->aulogin
        ." --password ".$currentUser->aupass
        ." --cutoutuser ".$filterUser;

if($withdata){
    
   if($limit)
     $param .= " --limitextent '".$limit."'";
   
   $param .= " --withdata ";
 
}

$command = "python ".Configurations::getConfiguration('REATLAS_CLIENT_PATH')."/cmd_cutout_details.py";
$command .= " $param --output JSON 2>&1";
/*
$myFile = "command_tst.txt";
$fh = fopen($myFile, 'a') or die("can't open file");
fwrite($fh, $command."\n");
*/
//echo $command;

$pid = popen( $command,"r");
$result = '';

while( !feof( $pid ) )
{
$result .= fread($pid, 256);
}
pclose($pid);


echo $result;   
