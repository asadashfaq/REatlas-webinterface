<?php

/*
 * Here comes the text of your license
 * Each line should be prefixed with  * 
 */

/**
 * Description of AdminFront
 *
 * @author manila
 */
class AdminFront {
    var $_html;
    var $_dbCon;
    
    public function AdminFront() {
        $this->_dbCon = DB::getInstance();
        
        $this->_html .= '<script src="js/jquery/ui/jquery-ui.js"></script>';
        $this->_html .= '
        <link rel="stylesheet" href="css/jquery/ui/themes/smoothness/jquery-ui-1.10.3.custom.css" />
        <link rel="stylesheet" href="css/admin-front-grid.css" />
        <script>
        $(function() {
          $( ".column" ).sortable({
            connectWith: ".column"
          });

          $( ".portlet" ).addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
            .find( ".portlet-header" )
              .addClass( "ui-widget-header ui-corner-all" )
              .prepend( "<span class=\'ui-icon ui-icon-minusthick\'></span>")
              .end()
            .find( ".portlet-content" );

          $( ".portlet-header .ui-icon" ).click(function() {
            $( this ).toggleClass( "ui-icon-minusthick" ).toggleClass( "ui-icon-plusthick" );
            $( this ).parents( ".portlet:first" ).find( ".portlet-content" ).toggle();
          });

          $( ".column" ).disableSelection();
        });
        </script>';
        $this->_html .='<div class="column">';
        $this->_html .= $this->runningJobsBlock();
        $this->_html .= $this->newUpdatesBlock();
        $this->_html .='</div>
                  <div class="column">';
        $this->_html .= $this->serverStatusBlock();
        $this->_html .= $this->trackingBlock();
         $this->_html .='</div>
                  <div class="column">';
         $this->_html .= $this->onlineUsersBlock();
        $this->_html .= $this->userUpdatesBlock();
         $this->_html .='</div>
            </div>';
    }
    private function userUpdatesBlock() {
        return ' <div class="portlet">
                <div class="portlet-header">User updates</div>
                <div class="portlet-content">No updates</div>
              </div>';
    }
    private function serverStatusBlock() {
        return ' <div class="portlet">
                <div class="portlet-header">Server Status</div>
                <div class="portlet-content">Server is running fine</div>
              </div>';
    }
    private function newUpdatesBlock() {
        return ' <div class="portlet">
                <div class="portlet-header">Updates</div>
                <div class="portlet-content">No new updates.</div>
              </div>';
    }
    private function onlineUsersBlock() {
        $_query = new DbQuery();
        $_query->from("active_users");
        $res = $this->_dbCon->executeS($_query);
        
        $localHtml = ' <div class="portlet">
                <div class="portlet-header">Online Users</div>
                <div class="portlet-content">';
        if($res){
            $userCount = count($res);
            $localHtml .= $userCount==1?"1 user":$userCount." users";
            $localHtml .= " online";
        }else {
            $localHtml .= "No users online";
        }
        //Lorem ipsum dolor sit amet, consectetuer adipiscing elit
         $localHtml .= '</div>
              </div>';
         return $localHtml;
    }
    private function trackingBlock() {
        return ' <div class="portlet">
                <div class="portlet-header">Trace</div>
                <div class="portlet-content">Tracing will be displayed here</div>
              </div>';
    }
    private function runningJobsBlock() {
        
        $param = PEPSI_SERVER." --username ".PEPSI_ADMIN_USER." --password ".PEPSI_ADMIN_PASS." --output JSON";
        $command = "python ".REATLAS_CLIENT_PATH."/cmd_job_list.py";
        $command .= " $param 2>&1";

        $pid = popen( $command,"r");
        $result = '';

        while( !feof( $pid ) )
        {
        $result .= fread($pid, 256);
        }
        pclose($pid);
        $resJson = json_decode($result);
        
        $res = ' <div class="portlet">
                <div class="portlet-header">Running Jobs</div>';
        if($result){
            $res .="<h5>Total running jobs: ".$resJson[0]->total_jobs." <=> Total ETA: ".$resJson[0]->total_ETA."</h5>";
            if($resJson[0]->total_jobs >0){
                $res .= "<div class=\"portlet-content\">"
                        ."<table><tr><th>Job ID</th><th>User</th><th>Job Name</th><th>ETA</th>";
                $jobs = $resJson[1]->jobs;
                foreach ($jobs as $job) {
                    $res .="<tr><td>$job->job_id</td><td>$job->user</td><td>$job->name</td><td>$job->time_estimate</td></tr>";
                }
                $res .="</table></div>";
            }
        }
        else
            $res.= "<div class=\"portlet-content\">No jobs running</div>";
        $res.= "</div>";
        return $res;
    }
    
    public function display() {
        echo $this->_html;
    }
}
