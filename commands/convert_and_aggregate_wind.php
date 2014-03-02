<?php
include_once('../init.php');

if(!$session->logged_in){
    echo 'Error: Session expires.';
    die();
}

$currentUser = new user($session->userid);

if($currentUser->aulogin =="" ||$currentUser->aulogin == null){
    echo 'Error: Technical problem. Contact Administrator.';
   die();
}

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
$parentDir = Configurations::getConfiguration('REATLAS_CLIENT_PATH').'/data/'.$filterUser;
$layout_file = null;

if(is_dir($parentDir))
    if(is_file($parentDir."/".$filterUser."_".$cutout."_layout_".$capacitylayout.".npy"))
        $layout_file = $parentDir."/".$filterUser."_".$cutout."_layout_".$capacitylayout.".npy";

 
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
        ." ".Configurations::getConfiguration('REATLAS_WINDTURBINE_CONFIG_PATH')."/".$offshoreconfig
        ." ".Configurations::getConfiguration('REATLAS_WINDTURBINE_CONFIG_PATH')."/".$onshoreconfig
        ." ".$layout_file
        ." --username ".$currentUser->aulogin
        ." --password ".$currentUser->aupass
        ." --cutoutuser ".$filterUser
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


echo $result ;

die();