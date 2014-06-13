<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Jobs
 *
 * @author manila
 */
class Job {

    //put your code here
    var $userid;
    var $user;
    var $job_id;
    var $name;
    var $action;
    var $type;
    var $start_time;
    var $ETA;
    var $end_time;
    var $desc;
    var $data;
    var $status;
    var $job_status;

    function Job($id = null) {
        if ($id) {
            $res = DB::getInstance()->executeS("Select * from job where job_id='" . $id . "'");
            if ($res != null) {
                $this->userid = $res[0]['userid'];
                $this->user = $res[0]['user'];
                $this->job_id = $res[0]['job_id'];
                $this->name = $res[0]['name'];
                $this->action = $res[0]['action'];
                $this->type = $res[0]['type'];
                $this->start_time = $res[0]['start_time'];
                $this->ETA = $res[0]['ETA'];
                $this->end_time = $res[0]['end_time'];
                $this->desc = $res[0]['desc'];
                $this->data = $res[0]['data'];
                $this->status = $res[0]['status'];
                $this->job_status = array();

                $res_progress = DB::getInstance()->executeS("Select * from job_progress where job_id='" . $id . "'");
                foreach ($res_progress as $progress) {
                    $job_progress = new Job_progress();
                    $job_progress->id = $progress['id'];
                    $job_progress->job_id = $progress['job_id'];
                    $job_progress->time = $progress['time'];
                    $job_progress->complete = $progress['complete'];
                    $job_progress->desc = $progress['desc'];
                    $job_progress->data = $progress['data'];
                    $job_progress->status = $progress['status'];

                    array_push($this->job_status, $job_progress);
                }
            }
        }
    }

    /**
     * 
     * @return boolean
     */
    function save() {
        if ($this->job_id && $this->userid) {
            $res = DB::getInstance()->executeS("Select * from job where job_id='" . $this->job_id . "' and userid='" . $this->userid . "'");
            if ($res != null) {
                return $this->update($this->job_id, $this->userid, $this->user, $this->name, $this->action, $this->type, $this->start_time, $this->ETA, $this->end_time, $this->desc, $this->data, $this->status);
            } else {
                return $this->create($this->job_id, $this->action, $this->type, $this->userid, $this->user, $this->name, $this->desc, $this->start_time, $this->ETA, $this->end_time, $this->data, $this->status);
            }
        }
        return false;
    }

    /**
     * 
     * @return boolean
     */
    function remove() {
        if ($this->job_id && $this->userid) {
            $res = DB::getInstance()->executeS("Select * from job where job_id='" . $this->job_id . "' and userid='" . $this->userid . "'");
            if ($res != null) {
                return $this->delete($this->job_id, $this->userid, $this->user, $this->name, $this->action, $this->type, $this->desc, $this->start_time, $this->ETA, $this->end_time, $this->data, $this->status);
            }
        }
        return false;
    }

    /**
     * 
     * @param type $job_id
     * @param type $action
     * @param type $type
     * @param type $userid
     * @param type $user
     * @param type $name
     * @param type $desc
     * @param type $start_time
     * @param type $ETA
     * @param type $end_time
     * @param type $data
     * @param type $status
     * @return type
     */
    function create($job_id, $action, $type, $userid, $user, $name, $desc, $start_time = null, $ETA = null, $end_time = null, $data = null, $status = null) {

        return DB::getInstance()->execute("Insert INTO job values("
                        . "'" . $userid . "'"
                        . ",'" . $user . "'"
                        . ",'" . $job_id . "'"
                        . ",'" . $name . "'"
                        . ",'" . $action . "'"
                        . ",'" . $type . "'"
                        . ($start_time ? ",'" . date_create($start_time, 'Y-m-d H:i:s') . "'" : ",now()")
                        . ($ETA ? ",'" . $ETA . "'" : ",NULL")
                        . ($end_time ? ",'" . date_create($end_time, 'Y-m-d H:i:s') . "'" : ",NULL")
                        . ($desc ? ",'" . $desc . "'" : ",NULL")
                        . ($data ? ",'" . $data . "'" : ",NULL")
                        . ($status ? ",'" . $status . "'" : ",NULL")
                        . ")");
    }

    /**
     * 
     * @param type $job_id
     * @param type $userid
     * @param type $user
     * @param type $name
     * @param type $action
     * @param type $type
     * @param type $start_time
     * @param type $ETA
     * @param type $end_time
     * @param type $desc
     * @param type $data
     * @param type $status
     * @return type
     */
    function update($job_id, $userid, $user, $name = null, $action = null, $type = null, $start_time = null, $ETA = null, $end_time = null, $desc = null, $data = null, $status = null) {

        return DB::getInstance()->execute("Update job SET "
                        . ($userid ? "userid='" . $userid . "'" : "")
                        . ($action ? ",name='" . $name . "'" : "")
                        . ($action ? "action='" . $action . "'" : "")
                        . ($type ? ",type='" . $type . "'" : "")
                        . ($start_time ? ",start_time='" . date_create($start_time, 'Y-m-d H:i:s') . "'" : "")
                        . ($ETA ? ",ETA='" . $ETA . "'" : "")
                        . ($end_time ? ",end_time='" . date_create($end_time, 'Y-m-d H:i:s') . "'" : "")
                        . ($desc ? ",desc='" . $desc . "'" : "")
                        . ($data ? ",data='" . $data . "'" : "")
                        . ($status ? ",status='" . $status . "'" : "")
                        . "WHERE "
                        . "userid='" . $userid . "'"
                        . "and user='" . $user . "'"
                        . "and job_id'" . $job_id . "'"
                        . "");
    }

    /**
     * 
     * @param type $job_id
     * @param type $userid
     * @param type $user
     * @param type $name
     * @param type $action
     * @param type $type
     * @param type $desc
     * @param type $start_time
     * @param type $ETA
     * @param type $end_time
     * @param type $data
     * @param type $status
     * @return type
     */
    function delete($job_id, $userid, $user, $name = null, $action = null, $type = null, $desc = null, $start_time = null, $ETA = null, $end_time = null, $data = null, $status = null) {

        return DB::getInstance()->execute("Delete from job "
                        . "WHERE "
                        . "userid='" . $userid . "' "
                        . "and user='" . $user . "' "
                        . "and job_id'" . $job_id . "' "
                        . ($name ? "and name='" . $name . "' " : " ")
                        . ($type ? "and type='" . $type . "' " : " ")
                        . ($action ? "and action='" . $action . "' " : " ")
                        . ($start_time ? "and start_time='" . date_create($start_time, 'Y-m-d H:i:s') . "' " : " ")
                        . ($ETA ? "and ETA='" . $ETA . "' " : " ")
                        . ($end_time ? "and end_time='" . date_create($end_time, 'Y-m-d H:i:s') . "' " : " ")
                        . ($desc ? "and desc='" . $desc . "' " : " ")
                        . ($data ? "and data='" . $data . "' " : " ")
                        . ($status ? "and status='" . $status . "' " : " ")
        );
    }

    /**
     * 
     * @param type $userid
     * @param type $user
     * @param type $action
     * @param type $type
     * @return array
     */
    public static function getJobsForUser($userid, $user = NULL, $action = null, $type = null) {
        if ($userid) {
            $res_jobs = DB::getInstance()->executeS("Select distinct job_id from job where userid='" . $userid . "'"
                    . ($user ? "and user='" . $user . "'" : "")
                    . ($type ? "and type='" . $type . "' " : "")
                    . ($action ? "and action='" . $action . "' " : "")
            );

            if ($res_jobs != null) {
                $job_list = array();
                foreach ($res_jobs as $res_job) {
                    $cur_job = new Job($res_job['job_id']);
                    array_push($job_list, $cur_job);
                }
                return $job_list;
            }
        }
        return array();
    }

    /**
     * 
     * @param type $userid
     * @param type $user
     * @param type $action
     * @param type $type
     * @return type
     */
    public static function getJobListForUser($userid, $user = NULL, $action = null, $type = null) {
     
        $job_list = Job::getJobsForUser($userid, $user, $action, $type);
        $job_list_str = "";
        foreach ($job_list as $job) {
            
            $job_name_arr = explode("_", $job->name);
            unset($job_name_arr[0]); // remove item at index 0 -- type
            unset($job_name_arr[1]); // remove item at index 1 --  user
            unset($job_name_arr[2]); // remove item at index 2 -- cutout
            $job_name = implode("_", array_values($job_name_arr));

            $resClass=$job->status;
            $getresult=null;
            if(!$resClass)
                $getresult = json_decode(self::getJobStatusUsingCommand($job->job_id));
            
            if($getresult)
                $resClass = $getresult->type;
            
            switch ($resClass) {
                case "Success":
                    $resClass = "success";
                    break;
                case "Failure":
                    $resClass = "failure";
                    break;
                case "Waiting":
                    $resClass = "waiting";
                    break;
                case "Error":
                default:
                    $resClass = "error";
                    break;
                }
            
            $job_list_str .="<label class=\"$resClass\"><input type=\"radio\" class=\"radio $resClass\" name=\"joblist_$action\" id=\"joblist_" . $action . "_" . $job->job_id . "\"  value=\"$job->name\"> $job_name </label><br/>";
        }

        return $job_list_str;
    }

    private function getJobStatusUsingCommand($job_id = 0) {
        global $session;
        if ($job_id == 0)
            return;


        if (!$session->logged_in) {
            return;
        }

        $currentUser = new user($session->userid);

        if ($currentUser->aulogin == "" || $currentUser->aulogin == null) {
           return;
        }
        $param = Configurations::getConfiguration('PEPSI_SERVER') . " " . $job_id . " "
                . " --username " . $currentUser->aulogin
                . " --password " . $currentUser->aupass;

        $command = "python " . Configurations::getConfiguration('REATLAS_CLIENT_PATH') . "/cmd_job_status.py";
        $command .= " $param --output JSON 2>&1";

        $pid = popen($command, "r");
        $result = '';

        while (!feof($pid)) {
            $result .= fread($pid, 256);
        }
        pclose($pid);


        return $result;
    }

}
