<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//{"cutoutName":"rrg","geomatry_type":"polygon","geomatry_data":{"southwest_latitude":-72.23684375000217,"southwest_longitude":31.259294953114185,"northeast_latitude":-108.44778124999252,"northeast_longitude":47.79802337889069}}
        
include_once('../init.php');

if(!$session){
    echo 'Error: Session expires.';
    die();
}

$currentUser = new user($session->userid);

if($currentUser->aulogin =="" ||$currentUser->aulogin == null){
    echo 'Error: Technical problem. Contact Administrator.';
   die();
}


$cutoutName = $_REQUEST["cutoutName"];
$geomatry_type = $_REQUEST["geomatry_type"];
$geomatry_data = json_decode($_REQUEST["geomatry_data"]);
$cutoutStartDate = $_REQUEST["cutoutStartDate"];
$cutoutEndDate = $_REQUEST["cutoutEndDate"];

$cutoutStartDateArr = explode('-', $cutoutStartDate);
$cutoutEndDateArr = explode('-', $cutoutEndDate);

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
        ." -fy ".$cutoutStartDateArr[0]
        ." -fm ".$cutoutStartDateArr[1]
        ." -ly ".$cutoutEndDateArr[0]
        ." -lm ".$cutoutEndDateArr[1]
        ." ".Configurations::getConfiguration('PEPSI_SERVER')." ".$cutoutName." "
        ." ".$geomatry_data->southwest_latitude
        ." ".$geomatry_data->southwest_longitude
        ." ".$geomatry_data->northeast_latitude
        ." ".$geomatry_data->northeast_longitude;

$command = "python ".Configurations::getConfiguration('REATLAS_CLIENT_PATH')."/cmd_create_CFSR_rectangular_cutout.py";
$command .= " $param ";
echo $command;
$pid = popen( $command,"r");
$result = '';

while( !feof( $pid ) )
{
$result .= fread($pid, 256);
}
pclose($pid);


echo $result;   
