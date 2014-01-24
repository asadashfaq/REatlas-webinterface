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
class AdminFrontController extends FrontController {

    var $_html;
    var $_dbCon;
    var $action;

    /**
     * class constructor
     */
    function __construct() {
        parent::__construct();

        $this->_dbCon = DB::getInstance();
        $this->collectRequestVar();
    }

    private function initHead() {
        $this->smarty->assign(
                array('action' => $this->action)
        );
        $this->smarty->display("admin-head.tpl");
    }

    private function initFooter() {
        $this->smarty->display("admin-footer.tpl");
    }

    private function collectRequestVar() {
        $this->action = Tools::getValue("action");
    }

    private function initContent() {
        $this->smarty->assign(
                array('siteURL' => Configurations::getConfiguration('SITE_DIRECTORY'))
        );
        echo "<body>";
        $this->smarty->display("admin-header.tpl");
        $this->smarty->display("admin-nav.tpl");
        echo "<!-- begin container -->
      <div id=\"container\">";
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

    private function contentEnds() {
        echo "</div></body>";
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
        if ($res) {
            $userCount = count($res);
            $localHtml .= $userCount == 1 ? "1 user" : $userCount . " users";
            $localHtml .= " online";
        } else {
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

        $param = Configurations::getConfiguration('PEPSI_SERVER') . " --username " . Configurations::getConfiguration('PEPSI_ADMIN_USER') . " --password " . Configurations::getConfiguration('PEPSI_ADMIN_PASS') . " --output JSON";
        $command = "python " . Configurations::getConfiguration('REATLAS_CLIENT_PATH') . "/cmd_job_list.py";
        $command .= " $param 2>&1";
         
        ob_start();
        passthru($command);
        $result = ob_get_clean(); 
        
        $resJson = json_decode($result);

        $res = ' <div class="portlet">
                <div class="portlet-header">Running Jobs</div>';
        
         if($resJson->type =="Success") {
            $res .="<h5>Total running jobs: " . $resJson->data[0]->total_jobs . " <=> Total ETA: " . $resJson->data[0]->total_ETA . "</h5>";
            if ($resJson->data[0]->total_jobs > 0) {
                $res .= "<div class=\"portlet-content\">"
                        . "<table><tr><th>Job ID</th><th>User</th><th>Job Name</th><th>ETA</th>";
                $jobs = $resJson->data[1]->jobs;
                foreach ($jobs as $job) {
                    $res .="<tr><td>$job->job_id</td><td>$job->user</td><td>$job->name</td><td>$job->time_estimate</td></tr>";
                }
                
                  $res .="</table></div>";
            }
            
        }
        else if($resJson->type =="Error") {
            $res.= "<div class=\"portlet-content\"><h4>".$resJson->text."</h4><span>".$resJson->desc."</span></div>";
        }else
            $res.= "<div class=\"portlet-content\">No jobs running.</div>";
        
        $res.= "</div>";
        return $res;
    }

    private function handleAction() {
        if ($this->action == "users") {
            $adminUsers = new AdminUsersController();
            $adminUsers->action = $this->action;
            $adminUsers->display();
            
        }  else if ($this->action == "configurations") {
            $adminConfig = new AdminConfigurationController();
            $adminConfig->action = $this->action;
            $adminConfig->display();
            
        }  else {
        
            $this->smarty->assign(
                array('userUpdatesBlock' => $this->userUpdatesBlock(),
                    'runningJobsBlock' => $this->runningJobsBlock(),
                    'onlineUsersBlock' => $this->onlineUsersBlock(),
                    'newUpdatesBlock' => $this->newUpdatesBlock(),
                    'serverStatusBlock' => $this->serverStatusBlock(),
                    'userUpdatesBlock' => $this->userUpdatesBlock(),
                    'trackingBlock' => $this->trackingBlock()));
            $this->smarty->display("admin-front.tpl");
        }
    }

    public function display() {
        $this->initHead();
        $this->initContent();
        $this->handleAction();
        
        $this->contentEnds();
        $this->initFooter();
    }

}
