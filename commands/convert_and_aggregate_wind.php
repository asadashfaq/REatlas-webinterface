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

//offshoreconfig
$offshoreconfig =  Tools::getValue("offshoreconfig");
//onshoreconfig
$onshoreconfig =  Tools::getValue("onshoreconfig");
//capacitylayout
$capacitylayout =  Tools::getValue("capacitylayout");
$conversionName =  Tools::getValue("conversionName");
$parentDir = Configurations::getConfiguration('REATLAS_CLIENT_PATH').'/data/'.$currentUser->aulogin;
$layout_file = null;

if(is_dir($parentDir))
    if(is_file($parentDir."/layout_".$currentUser->aulogin."_".$cutout."_".$capacitylayout.".npy"))
        $layout_file = $parentDir."/layout_".$currentUser->aulogin."_".$cutout."_".$capacitylayout.".npy";

 
if(!$cutout)
    errorReturn('Cutout is not defined');

if(!$layout_file)
    errorReturn('Layout is not defined');

// filename: data/auesg/meta_Denmark.npz
// cmd_convert_and_aggregate_wind.py [-h] [-p [PORT]]
                                       //  [--username [USERNAME]]
                                        // [--password [PASSWORD]]
                                       //  [--cutoutuser [CUTOUTUSER]]
                                       //  [--name [NAME]]
                                      //   server(done) cutoutname(done) onshorepowercurve(done)
                                      //   offshorepowercurve(done) capacitylayout
                                       //  [capacitylayout ...]

//$fileName = "data/".$filterUser."/meta_".$cutout.".npz";
//python cmd_convert_and_aggregate_wind.py servername Denmark \ 
//--cutoutuser auesg --name Windconversion1
//TurbineConfig/Siemens_SWT_2300kW.cfg \
//TurbineConfig/Vestas_V90_3MW.cfg \ 
//layout1.shp layout2.csv

$param = Configurations::getConfiguration('PEPSI_SERVER')." ".$cutout
        ." ".Configurations::getConfiguration('REATLAS_CLIENT_PATH').'/'
        .Configurations::getConfiguration('REATLAS_WINDTURBINE_CONFIG_PATH')."/".$offshoreconfig
        ." ".Configurations::getConfiguration('REATLAS_CLIENT_PATH').'/'
        .Configurations::getConfiguration('REATLAS_WINDTURBINE_CONFIG_PATH')."/".$onshoreconfig
        ." ".$layout_file
        ." --username ".$currentUser->aulogin
        ." --password ".$currentUser->aupass
        ." --cutoutuser ".$filterUser
        ." --name ".$conversionName
        ." --output JSON";


$command = "python ".Configurations::getConfiguration('REATLAS_CLIENT_PATH')."/cmd_convert_and_aggregate_wind.py";
$command .= " $param  2>&1";

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
    $job->name = "wind_".$currentUser->aulogin."_".$cutout."_".$conversionName;
    $job->action = "conversion";
    $job->type = "wind";
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