<?php

//- turn off compression on the server
@apache_setenv('no-gzip', 1);
@ini_set('zlib.output_compression', 'Off');

include_once('../init.php');

// hide notices
@ini_set('error_reporting', E_ALL & ~ E_NOTICE);

function errorReturn($message) {
    $outArr = array();
    $outArr['type'] = "Error";
    $outArr['text'] = "Error occured";
    $outArr['desc'] = $message;
    $outArr['traceback'] = '';
    $outArr['data'] = '';
    echo json_encode($outArr);
    die();
}

if (!$session->logged_in) {
    errorReturn('Error: Session expires.');
}

$currentUser = new user($session->userid);

if ($currentUser->aulogin == "" || $currentUser->aulogin == null) {
    errorReturn('Error: Technical problem. Contact Administrator.');
}

$job_id = Tools::getValue("job_id");
$job_name = Tools::getValue("job_name");
$downloadtype = Tools::getValue("downloadtype");

if (!$job_id)
    errorReturn('Error: Job ID is not defined');

if (!$job_name)
    errorReturn('Error: Job Name is not defined');

/* python /development/AU/REatlas-client/cmd_get_result.py 
 * Pepsimax.imf.au.dk 
 * 72 
 * wind_manila_Denmark_test_wind_conv_13Apr_6 
 * tmpdata/wind_manila_Denmark_test_wind_conv_13Apr_6.csv 
 * --username manila --password iet5hiuC
 * --output JSON
 */
if($downloadtype && $downloadtype != "")
    $file_ext = $downloadtype;
else
    $file_ext = "csv";

$file_name = $job_name.".".$file_ext;


$param = Configurations::getConfiguration('PEPSI_SERVER') . " "
        . $job_id . " "
        . $job_name . " "
        . Configurations::getConfiguration('REATLAS_CLIENT_PATH')."/tmpdata/" . $job_name . ".".$file_ext." "
        . " --username " . $currentUser->aulogin
        . " --password " . $currentUser->aupass;

$command = "python " . Configurations::getConfiguration('REATLAS_CLIENT_PATH') . "/cmd_get_result.py";
$command .= " $param --output JSON 2>&1";

$pid = popen($command, "r");
$result = '';

while (!feof($pid)) {
    $result .= fread($pid, 256);
}
pclose($pid);

$result_json = json_decode($result);
if (isset($result_json->{'data'}) && $result_json->{'data'} !=null && $result_json->{'data'}!="") {
    $parentDir = Configurations::getConfiguration('REATLAS_CLIENT_PATH') . '/tmpdata';
    $file_path = $parentDir . "/" . $job_name . ".".$file_ext;
    // allow a file to be streamed instead of sent as an attachment
    $is_attachment = isset($_REQUEST['stream']) ? false : true;
    $file_name_download_arr = explode("_", $file_name);
    unset($file_name_download_arr[0]);
    unset($file_name_download_arr[1]);
    unset($file_name_download_arr[2]);
    $file_name_download = implode("_", $file_name_download_arr);
    
// make sure the file exists
    if (is_file($file_path)) {
        $file_size = filesize($file_path);
        $file = @fopen($file_path, "rb");
        if ($file) {
            // set the headers, prevent caching
            header("Pragma: public");
            header("Expires: -1");
            header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
            header('Content-Encoding: UTF-8');
            header("Content-Disposition: attachment; filename=\"$file_name_download\"");

            // set appropriate headers for attachment or streamed file
            if ($is_attachment)
                header("Content-Disposition: attachment; filename=\"$file_name_download\"");
            else
                header('Content-Disposition: inline;');

            // set the mime type based on extension, add yours if needed.
            $ctype_default = "application/octet-stream";
            $content_types = array(
            "npy" => "application/octet-stream",
            "mat" => "application/octet-stream",
            "csv" => "text/csv; charset=UTF-8",
            );
          
            $ctype = isset($content_types[$file_ext]) ? $content_types[$file_ext] : $ctype_default;
            header("Content-Type: " . $ctype);

            //check if http_range is sent by browser (or download manager)
            if (isset($_SERVER['HTTP_RANGE'])) {
                list($size_unit, $range_orig) = explode('=', $_SERVER['HTTP_RANGE'], 2);
                if ($size_unit == 'bytes') {
                    //multiple ranges could be specified at the same time, but for simplicity only serve the first range
                    //http://tools.ietf.org/id/draft-ietf-http-range-retrieval-00.txt
                    list($range, $extra_ranges) = explode(',', $range_orig, 2);
                } else {
                    $range = '';
                    header('HTTP/1.1 416 Requested Range Not Satisfiable');
                    exit;
                }
            } else {
                $range = '';
            }

            //figure out download piece from range (if set)
            list($seek_start, $seek_end) = explode('-', $range, 2);

            //set start and end based on range (if set), else set defaults
            //also check for invalid ranges.
            $seek_end = (empty($seek_end)) ? ($file_size - 1) : min(abs(intval($seek_end)), ($file_size - 1));
            $seek_start = (empty($seek_start) || $seek_end < abs(intval($seek_start))) ? 0 : max(abs(intval($seek_start)), 0);

            //Only send partial content header if downloading a piece of the file (IE workaround)
            if ($seek_start > 0 || $seek_end < ($file_size - 1)) {
                header('HTTP/1.1 206 Partial Content');
                header('Content-Range: bytes ' . $seek_start . '-' . $seek_end . '/' . $file_size);
                header('Content-Length: ' . ($seek_end - $seek_start + 1));
            } else
                header("Content-Length: $file_size");

            header('Accept-Ranges: bytes');

            set_time_limit(0);
            fseek($file, $seek_start);

            while (!feof($file)) {
                print(@fread($file, 1024 * 8));
                ob_flush();
                flush();
                if (connection_status() != 0) {
                    @fclose($file);
                    exit;
                }
            }

            // file save was a success
            @fclose($file);
            exit;
        } else {
            // file couldn't be opened
            header("HTTP/1.0 500 Internal Server Error");
            exit;
        }
    } else {
        // file does not exist
        header("HTTP/1.0 404 Not Found");
        exit;
    }
    
} else
    echo $result;