<?php
include_once('../init.php');

if(!$session->logged_in){
    echo 'Error: Session expires.';
    die();
}

$capacityType = Tools::getValue('turbineName');
$capacityTypeFolder = "TurbineConfig";

$parentDir = Configurations::getConfiguration('REATLAS_CLIENT_PATH').'/'.$capacityTypeFolder;

if(is_dir($parentDir))
{

$filearray =  array_diff(@scandir($parentDir), array('..', '.'));
$capacityArray = array();


foreach($filearray as $file)
{

    if($file == ".." || $file == ".")
        continue;
    
    echo "filename is $file"."<br/>";
   
    $lines_array = file($parentDir."/".$file);
    $search_string = "V =";
    $search_string1 = "POW =";
    echo "lines_array $lines_array"."<br/>";
    foreach($lines_array as $line) {
        if(strpos($line, $search_string) !== false) {
            list(, $name_str) = explode("=", $line);
            $name_str = trim($name_str);
            echo "<pre>"."Velocity array $name_str"."</pre>";
            $capacityListObj = array('id'=>$file, 'name'=>$name_str);

            array_push($capacityArray, $capacityListObj);
        }
    }
     foreach($lines_array as $line) {
        if(strpos($line, $search_string1) !== false) {
            list(, $name_str) = explode("=", $line);
            $name_str1 = trim($name_str);
            echo "<pre>"."Power array $name_str"."</pre>";
            $capacityListObj = array('id'=>$file, 'name'=>$name_str1);

            array_push($capacityArray, $capacityListObj);
        }
    }

}

    

}
?>
<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
