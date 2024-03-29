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

//cutoutuser
$filterUser =  Tools::getValue("user");
//echo "filteruser is $filterUser";

//cutoutname
$cutout =  Tools::getValue("cutout");

//panelconf
$panelconf =  Tools::getValue("panelconf");
//orientation
$orientation =  Tools::getValue("orientation");
//capacitylayout
$capacitylayout =  Tools::getValue("capacitylayout");
$conversionName =  Tools::getValue("conversionName");

//slope
$slope =  Tools::getValue("slope");
//azimuth
$azimuth =  Tools::getValue("azimuth");

// Create Orientation config file
$tmpfname = tempnam(sys_get_temp_dir(), 'Orientation');

$orientationString = '';
if($orientation =="FixedOrientation")
{
    $orientationString .= "[constant]\n";
    $orientationString .= "slope = ".$slope."\n";
    $orientationString .= "azimuth = ".$azimuth."\n";
    $orientationString .= "weight = 1.0\n";
}else if($orientation =="VerticalTracking")
{
    $orientationString .= "[vertical_tracking]\n";
    $orientationString .= "azimuth = ".$azimuth."\n";
    $orientationString .= "weight = 1.0\n";
}
else if($orientation =="HorizontalTracking")
{
    $orientationString .= "[horizontal_tracking]\n";
    $orientationString .= "slope = ".$slope."\n";
    $orientationString .= "weight = 1.0\n";
}else
{
    $orientationString .= "[full_tracking]\n";
    $orientationString .= "weight = 1.0\n";
}

$orientationtmpfname = $tmpfname.'.cfg';
file_put_contents($orientationtmpfname, $orientationString);
unlink($tmpfname);//to delete an empty file that tempnam creates
chmod($orientationtmpfname, 0777);


/* Layout file */    
$layout_file = null;
$parentDir = Configurations::getConfiguration('REATLAS_CLIENT_PATH').'/data/'.$currentUser->aulogin;
if(is_dir($parentDir))
    if(is_file($parentDir."/layout_".$currentUser->aulogin."_".$cutout."_".$capacitylayout.".npy"))
        $layout_file = $parentDir."/layout_".$currentUser->aulogin."_".$cutout."_".$capacitylayout.".npy";

/* Panel Conf file */    
$panel_conf_file = null;
$panel_conf_parentDir = Configurations::getConfiguration('REATLAS_CLIENT_PATH').'/SolarPanelData';
if(is_dir($panel_conf_parentDir))
    if(is_file($panel_conf_parentDir."/".$panelconf))
        $panel_conf_file = $panel_conf_parentDir."/".$panelconf;

    
if(!$cutout)
    errorReturn('Cutout is not defined');

if(!$layout_file)
    errorReturn('Layout is not defined');

if(!$panel_conf_file)
    errorReturn('Solar panel config file is missing.');

//usage: cmd_convert_and_aggregate_PV.py [-h] [-p [PORT]]
//                                       [--username [USERNAME]]
//                                       [--password [PASSWORD]]
//                                       [--cutoutuser [CUTOUTUSER]]
//                                       [--name [NAME]]
//                                       server cutoutname panelconf
//                                       orientationconf capacitylayout
//                                       [capacitylayout ...]


//$fileName = "data/".$filterUser."/meta_".$cutout.".npz";
$param = Configurations::getConfiguration('PEPSI_SERVER')
        ." ".$cutout
        ." ".$panel_conf_file
        ." ".$orientationtmpfname
        ." ".$layout_file
        ." --username ".$currentUser->aulogin
        ." --password ".$currentUser->aupass
        ." --cutoutuser ".$filterUser
        ." --name ".$conversionName;

$command = "python ".Configurations::getConfiguration('REATLAS_CLIENT_PATH')."/cmd_convert_and_aggregate_PV.py";
$command .= " $param --output JSON 2>&1";


$pid = popen( $command,"r");
$result = '';

while( !feof( $pid ) )
{
$result .= fread($pid, 256);
}
pclose($pid);

/*
 * {
 * "text": "Submitted wind conversion job.", 
 * "traceback": "", 
 * "type": "Success", 
 * "data": "{\"job_id\":72,\"resultname\":\"wind_manila_Denmark_test_wind_conv_13Apr_6\",\"ETA\":\"0:01:00\"}", 
 * "desc": " Job id: 72\n Result name: wind_manila_Denmark_test_wind_conv_13Apr_6\n ETA: 0:01:00"
 * } 
 */
$return_res = json_decode($result);
if($return_res && $return_res->data)
    $return_data = json_decode ($return_res->data);

if($return_res && $return_data){
    $job = new Job();
    $job->job_id = $return_data->job_id;
    $job->name = "solar_".$currentUser->aulogin."_".$cutout."_".$conversionName;
    $job->action = "conversion";
    $job->type = "solar";
    $job->desc = $return_res->desc;
    $job->ETA = $return_data->ETA;
    $job->user = $currentUser->username;
    $job->userid = $currentUser->id;
    $job->status = $return_res->type;
    $job->data = $return_res->data;
    $job->save();

}

echo $result ;

die();
