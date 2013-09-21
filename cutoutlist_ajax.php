<?php

$filterUser = $_REQUEST["user"];
$param = "";

$command = "python /development/AU/REatlas-client/scripts/cutoutList.py";
$command .= " $param 2>&1";

$pid = popen( $command,"r");
$result = '';

while( !feof( $pid ) )
{
$result .= fread($pid, 256);
}
pclose($pid);

$records = array(); 
preg_match_all ('/\[([^\]]*)\]/',$result,$matches);
foreach($matches[1] as $value){    
	if(strpos($value,$filterUser)>0){
		$output=str_replace('[','',$value);
		$output=str_replace(']','',$output);
		$output=str_replace("u'","'",$output);
		$output=str_replace("'","",$output);                
		$output=str_replace($filterUser."/","",$output);
		$out_arr = explode(",",$output); 
		$records[]=Array("cutout"=>$out_arr[0],"cutoutId"=>$out_arr[1]);
	}
}


echo json_encode($records);   
