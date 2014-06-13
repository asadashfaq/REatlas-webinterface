<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//{"cutoutName":"rrg","geometry_type":"polygon","geometry_data":{"southwest_latitude":-72.23684375000217,"southwest_longitude":31.259294953114185,"northeast_latitude":-108.44778124999252,"northeast_longitude":47.79802337889069}}
        
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


$cutoutName =  Tools::getValue("cutoutName");
$geometry_type =  Tools::getValue("geometry_type");
$geometry_data = json_decode( Tools::getValue("geometry_data"));
$cutoutStartDate =  Tools::getValue("cutoutStartDate");
$cutoutEndDate =  Tools::getValue("cutoutEndDate");

$cutoutStartDateArr = explode('-', $cutoutStartDate);
$cutoutEndDateArr = explode('-', $cutoutEndDate);
$command = null;

if($geometry_type == "rectangle" || $geometry_type == "polygon") {
/* cmd_create_CFSR_rectangular_cutout.py Pepsimax.imf.au.dk Denmark --username manila --password iet5hiuC
cmd_create_CFSR_rectangular_cutout.py [-h] [-p [PORT]]
                                             [--username [USERNAME]]
                                             [--password [PASSWORD]]
                                             [-fy [FIRSTYEAR]]
                                             [-ly [LASTYEAR]]
                                             [-fm [FIRSTMONTH]]
                                             [-lm [LASTMONTH]]
                                             server cutout_name
                                             southwest_latitude
                                             southwest_longitude
                                             northeast_latitude
                                             northeast_longitude
*/

$param = " --username ".$currentUser->aulogin
        ." --password ".$currentUser->aupass
        ." -fy ".$cutoutStartDateArr[1]
        ." -fm ".$cutoutStartDateArr[0]
        ." -ly ".$cutoutEndDateArr[1]
        ." -lm ".$cutoutEndDateArr[0]
        ." ".Configurations::getConfiguration('PEPSI_SERVER')." ".$cutoutName." "
        ." ".$geometry_data->southwest_latitude
        ." ".$geometry_data->southwest_longitude
        ." ".$geometry_data->northeast_latitude
        ." ".$geometry_data->northeast_longitude
        ." --output JSON";
        
$command = "python ".Configurations::getConfiguration('REATLAS_CLIENT_PATH')."/cmd_create_CFSR_rectangular_cutout.py";
$command .= " $param 2>&1";
} else if ($geometry_type == "multipoint" || $geometry_type =="point"){
  /*  cmd_create_CFSR_pointwise_cutout.py [-h] [-p [PORT]]
                                           [--username [USERNAME]]
                                           [--password [PASSWORD]]
                                           [-fy [FIRSTYEAR]] [-ly [LASTYEAR]]
                                           [-fm [FIRSTMONTH]]
                                           [-lm [LASTMONTH]]
                                           server cutout_name
                                           GPS_coordinate_pairs
                                           [GPS_coordinate_pairs ...]
*/
$geometry_data_str = ''; 
foreach($geometry_data as $point) {
    $geometry_data_str .= $point[0].",".$point[1]." ";
}

$param = " --username ".$currentUser->aulogin
        ." --password ".$currentUser->aupass
        ." -fy ".$cutoutStartDateArr[1]
        ." -fm ".$cutoutStartDateArr[0]
        ." -ly ".$cutoutEndDateArr[1]
        ." -lm ".$cutoutEndDateArr[0]
        ." --output JSON "
        ." ".Configurations::getConfiguration('PEPSI_SERVER')." ".$cutoutName." "
        ." -- ".$geometry_data_str;
        
$command = "python ".Configurations::getConfiguration('REATLAS_CLIENT_PATH')."/cmd_create_CFSR_pointwise_cutout.py";
$command .= " $param 2>&1";
}
if (!$command)
    exit();

$pid = popen( $command,"r");
$result = '';

while( !feof( $pid ) )
{
$result .= fread($pid, 256);
}
pclose($pid);


echo $result;   
