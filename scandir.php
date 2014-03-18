<?php

echo "hello";
$search = 'name';
$files = scandir('/development/AU/REatlas-client/TurbineConfig/');
foreach($files as $file) {
 if(strpos($file, $search) !== false)
    echo $file;
}


foreach (glob("/development/AU/REatlas-client/TurbineConfig/*.cfg") as $filename) {
    if(strpos($filename, $search) !== false)
    echo $filename;
}



$dir = '/development/AU/REatlas-client/TurbineConfig';
foreach (glob("$dir/*") as $file) {
    $content = file_get_contents("$dir/$file");
    if (strpos($content, 'name =') !== false) {
        echo $content;
    }
}
?>