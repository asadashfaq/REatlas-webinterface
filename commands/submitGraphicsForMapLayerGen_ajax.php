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

$param = Configurations::getConfiguration('PEPSI_SERVER')." ".$cutoutName." "
        ." --username ".Configurations::getConfiguration('PEPSI_ADMIN_USER')
        ." --password ".Configurations::getConfiguration('PEPSI_ADMIN_PASS')
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
