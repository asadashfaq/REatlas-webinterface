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
$layoutdata = Tools::getValue("layoutdata");
$layoutName = Tools::getValue("layoutName");

if(!$cutout)
    errorReturn('Error: Cutout is not defined');

$tmpfname = tempnam(sys_get_temp_dir(), $cutout);

$handle = fopen($tmpfname, "rw+");
fwrite($handle, $layoutdata);
fclose($handle);
chmod($tmpfname, 0777);

// filename: data/auesg/meta_Denmark.npz
// python /development/AU/REatlas-client/cmd_save_layout.py Pepsimax.imf.au.dk Denmark /tmp/layoutTmp.npy --metadata /development/AU/REatlas-client/data/auesg/meta_Denmark.npz --username manila --password iet5hiuC --cutoutuser auesg

$fileName = "data/".$filterUser."/meta_".$cutout.".npz";
$param = Configurations::getConfiguration('PEPSI_SERVER')." ".$cutout." "
        .$tmpfname." "
        ." --metadata ".Configurations::getConfiguration('REATLAS_CLIENT_PATH')."/".$fileName
        ." --username ".$currentUser->aulogin
        ." --password ".$currentUser->aupass
        ." --cutoutuser ".$filterUser;

if($layoutName && $layoutName != "")
    $param .=" --name layout_".$currentUser->aulogin."_".$cutout."_".$layoutName;

$command = "python ".Configurations::getConfiguration('REATLAS_CLIENT_PATH')."/cmd_save_layout.py";
$command .= " $param --output JSON 2>&1";
/*
$myFile = "command_tst.txt";
$fh = fopen($myFile, 'a') or die("can't open file");
fwrite($fh, $command."\n");
*/

$pid = popen( $command,"r");
$result = '';

while( !feof( $pid ) )
{
$result .= fread($pid, 256);
}
pclose($pid);


//unlink($tmpfname);

echo $result;   
