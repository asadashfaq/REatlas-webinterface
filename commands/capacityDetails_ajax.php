<?php
include_once('../init.php');

if(!$session->logged_in){
    echo 'Error: Session expires.';
    die();
}

$cfgName =  Tools::getValue('cfgName');
$capacityType = Tools::getValue('capacityType');
$capacityTypeFolder = "";

if($capacityType =="Wind")
{
    $capacityTypeFolder ="TurbineConfig";
}else if($capacityType =="Solar")
{
    $capacityTypeFolder ="SolarPanelData";
}else if($capacityType =="Layout")
{
    $capacityTypeFolder ="Layout";
}else
{
    die('Error: Capacity type is not defined');
}

$parentDir = Configurations::getConfiguration('REATLAS_CLIENT_PATH').'/'.$capacityTypeFolder;

if(is_dir($parentDir))
{

$capacityDetailArray = array();
    
if(is_file($parentDir."/".$cfgName))
{
   // print_r($parentDir."/".$cfgName);
   $cfg_file_text = file_get_contents($parentDir."/".$cfgName);
   // since # is not supported by parse_ini_file so we are replacing and writing into tmp file
  
   //print_r($cfg_file_text);
   $cfg_file_text= str_replace("#", ";", $cfg_file_text);
   $cfg_file_text     = iconv("ISO-8859-1", "UTF-8", $cfg_file_text);
  
   $temp_file = tempnam(sys_get_temp_dir(), $cfgName);
 
   file_put_contents($temp_file, $cfg_file_text."\n", FILE_APPEND | LOCK_EX);
   $capacityDetailArray = parse_ini_file($temp_file, FALSE,INI_SCANNER_RAW);
   
   unlink($temp_file); 
}
//print_r($capacityDetailArray);

$outArr=array();
$outArr['type']="Success";
$outArr['text']="Capacity detail";
$outArr['desc']="Capacity detail for ".$cfgName;
$outArr['traceback']= '';
$outArr['data'] = $capacityDetailArray ;
echo json_encode($outArr);
}
?>
