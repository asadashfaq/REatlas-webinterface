<?php
include_once('../init.php');

if(!$session->logged_in){
    echo 'Error: Session expires.';
    die();
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
    die('Error: Capacity type is not defined');
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

echo json_encode($capacityArray);
}
?>
