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
    errorReturn('Error: Capacity type is not defined');
}

$parentDir = Configurations::getConfiguration('REATLAS_CLIENT_PATH').'/'.$capacityTypeFolder;

if(is_dir($parentDir))
{

$filearray =  array_diff(@scandir($parentDir), array('..', '.'));
$capacityArray = array();


foreach($filearray as $file)
{

    if($file == ".." || $file == ".")
        continue;
    
    //echo $file."<br/>";
    $lines_array = file($parentDir."/".$file);
    $search_string = "name =";
    //echo $lines_array."<br/>";
    foreach($lines_array as $line) {
        if(strpos($line, $search_string) !== false) {
            list(, $name_str) = explode("=", $line);
            $name_str = trim($name_str);
            //echo "<pre>".$name_str."</pre>";
            $capacityListObj = array('id'=>$file, 'name'=>$name_str);

            array_push($capacityArray, $capacityListObj);
        }
    }

}

$outArr=array();
$outArr['type']="Success";
$outArr['text']="Capacity list";
$outArr['desc']="List of available capacity";
$outArr['traceback']= '';
$outArr['data'] = $capacityArray ;
echo json_encode($outArr);
}
?>
