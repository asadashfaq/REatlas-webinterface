<?php

/*
 * Here comes the text of your license
 * Each line should be prefixed with  * 
 */

/**
 * Description of AdminUsers
 *
 * @author manila
 */
class AdminUsersController extends FrontController {
    
    private $_userLevelArr = array(GUEST_LEVEL=>'Guest',AGENT_LEVEL=>'Agent',AGENT_MEMBER_LEVEL=>'Member',MASTER_LEVEL=>'Master',ADMIN_LEVEL=>'Admin');
    var $_html;
    var $action;
    
    function __construct() {
        parent::__construct();
    }
    public function AdminUsersController() {
        
        $this->filterParams();
    }
    
    private function filterParams() {
        if (isset($_REQUEST['limit'])) {
            $_SESSION['limit'] = $_REQUEST['limit'];
        }else {
            unset($_SESSION['limit']);
        }
        if (isset($_REQUEST['page'])) {
            $_SESSION['page'] = $_REQUEST['page'];
        }else {
            unset($_SESSION['page']);
        }
        if (isset($_REQUEST['query'])) {
            $_SESSION['query'] = $_REQUEST['query'];
        }else {
            unset($_SESSION['query']);
        }
    }
    
    private function listPagination($recNum,$pagelimit = 10,$pageoffset=0,$queryFilter=null) {
        
        $limitArr = array('10'=>'10','20'=>'20','30'=>'30','50'=>'50','100'=>'100','0'=>'All');
        $localHtml = '<div id="paginationBar" >';
        $localHtml .= '';
        // page navigation
         $localHtml .= '<span>';
        if($recNum && $recNum >0 && $pagelimit>0){
            $paginationCnt = ceil(($recNum/$pagelimit));
            $localHtml .= '<span>Total results:'.$recNum.'</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                
            if($paginationCnt>1){
                $localHtml .= '<a href="'.$_SERVER['PHP_SELF'].'?action=users&limit='.$pagelimit.'&page=1'.($queryFilter?'&query='.$queryFilter:'').'"><-</a> ';
                for($i=0;$i<$paginationCnt;$i++){
                    $localHtml .= '<a href="'.$_SERVER['PHP_SELF'].'?action=users&limit='.$pagelimit.'&page='.($i+1).''.($queryFilter?'&query='.$queryFilter:'').'">'.($i+1).'</a> ';
                }
                $localHtml .= ' <a href="'.$_SERVER['PHP_SELF'].'?action=users&limit='.$pagelimit.'&page='.$paginationCnt.''.($queryFilter?'&query='.$queryFilter:'').'">-></a> ';
               
            }
        }
         $localHtml .= '</span>';
        // page limit combo
        $localHtml .= '<span style="float:right;">Items per page:<select  id="pageLimit" name="pageLimit">';
        foreach ($limitArr as $key => $value) {
            if($key == '0'){
                $localHtml .= '<option value="0" '.($key==$pagelimit?'selected':'').'>All</option>';
            }else {
                $localHtml .= '<option value="'.$key.'" '.($key==$pagelimit?'selected':'').'>'.$value.'</option>';
            }
        
        }
        
        $localHtml .= '</select></span>';
        $localHtml .= '<script>'
                . '$( "#pageLimit" ).change(function() {
                    var newLoc = "'.$_SERVER['PHP_SELF'].'?action=users&limit="+$(this).val()+"&query='.$queryFilter.'";
                    window.location = newLoc;
                  });'
                . '</script>';
        $localHtml .= '</div>';
        
        return $localHtml;
    }
    private function comboFilter($id,$valueArr,$selected = NULL,$default=false) {
        $cmb = "<select id='$id' name='$id'>";
        if($default)
            $cmb.= "<option value=''>--</option>";
        
        foreach ($valueArr as $key => $value) {
            
            $cmb .= "<option value='$key' ".($selected!==NULL && $selected==$key?"selected":"").">$value</option>";
        }
            $cmb .= "</select>"; 
            
            return $cmb;
    }
    private function listFilter($pagelimit,$currentPage,$queryFilter=null) {
        $filterVal = array();
        if($queryFilter && $queryFilter !=''){
            $queryFilterArr = explode(';', $queryFilter);
            foreach ($queryFilterArr as $q) {
                $queryParamArr = explode(':', $q);
                if($queryParamArr[1]!=NULL)
                    $filterVal[$queryParamArr[0]] = $queryParamArr[1];
             }
        }
       
        $localHtml = '';
        $localHtml .= '<tr id="filterBar" class="theader">'
                . '<td><input type="text" id="id" style="width:35px;" value="'.(isset($filterVal['id'])?$filterVal['id']:'').'"/></td>'
                . '<td><input type="text" id="username" style="width:100px;" value="'.(isset($filterVal['username'])?$filterVal['username']:'').'"/></td>'
                . '<td>'.$this->comboFilter("userlevel",$this->_userLevelArr,(isset($filterVal['userlevel'])?$filterVal['userlevel']:NULL),true).'</td>'
                . '<td><input type="text" id="email" style="width:100px;" value="'.(isset($filterVal['email'])?$filterVal['email']:'').'"/></td>'
/*                . '<td><input type="text" id="parent_dir" style="width:100px;" value="'.(isset($filterVal['parent_dir'])?$filterVal['parent_dir']:'').'"/></td>' */
                . '<td><input type="text" id="aulogin" style="width:100px;" value="'.(isset($filterVal['aulogin'])?$filterVal['aulogin']:'').'"/></td>'
                . '<td>'.$this->comboFilter("active",array(0=>"NO",1=>"YES"),(isset($filterVal['active'])?$filterVal['active']:NULL),true).'</td>'
                . '<td>'.$this->comboFilter("banned",array(0=>"NO",1=>"YES"),(isset($filterVal['banned'])?$filterVal['banned']:NULL),true).'</td>'
               . '<td><input type="button" onclick="resetFilter()" value="Reset Filter"/></td></tr>';
         $localHtml .= '<script>'
                 . 'function resetFilter(){'
                 . ' var newLoc = "'.$_SERVER['PHP_SELF'].'?action=users'.($pagelimit>0?'&limit='.$pagelimit:'').($currentPage>0?'&page='.$currentPage:'').'";
                    window.location = newLoc;'
                 . '}'
                . '$( "#filterBar input" ).change(function() {
                    var newLoc = "'.$_SERVER['PHP_SELF'].'?action=users'.($pagelimit>0?'&limit='.$pagelimit:'').($currentPage>0?'&page='.$currentPage:'').'&query="+$(this).attr("id")+":"+$(this).val()+":like";
                    window.location = newLoc;
                  });'
                 . '$( "#filterBar select" ).change(function() {
                    var newLoc = "'.$_SERVER['PHP_SELF'].'?action=users'.($pagelimit>0?'&limit='.$pagelimit:'').($currentPage>0?'&page='.$currentPage:'').'&query="+$(this).attr("id")+":"+$(this).val()+":equal";
                    window.location = newLoc;
                  });'
                . '</script>';
        return $localHtml;
    }
    public function userListHTML() {
        $pageLimit = isset($_REQUEST['limit'])?$_REQUEST['limit']:10;
        $currentPage = isset($_REQUEST['page'])?$_REQUEST['page']-1:0;
        $pageOffset = $pageLimit*$currentPage;
        
        $queryFilter = isset($_REQUEST['query'])?$_REQUEST['query']:'';
        $where = '';
        if($queryFilter && $queryFilter !=''){
            $queryFilterArr = explode(';', $queryFilter);
            foreach ($queryFilterArr as $qf) {
                if($qf == null)
                    continue;
                $queryParamArr = explode(':', $qf);
              
                if($queryParamArr[1]!=NULL){
                    if($queryParamArr[0] =="banned"){
                        if($queryParamArr[1]==0)
                            $where .= "NOT ";
                        $where .= "EXISTS(SELECT * FROM banned_users WHERE `username` = u.`username`) AND";
                    }else {
                        if($queryParamArr[2] == "like"){
                            $where .= $queryParamArr[0]." like '%".$queryParamArr[1]."%' AND";
                        }else {
                            $where .= $queryParamArr[0]." = '".$queryParamArr[1]."' AND";
                        }
                    }
                }
            }
           
        }
        
        $where .= " username <> '".$_SESSION['username']."' AND"; 
        
        $where = substr($where, 0, strlen($where)-3);
         
        $_query = new DbQuery();
        $_query->from("users","u");
        /* First get number of all records */
        $_query->where($where);
        $this->_dbCon->executeS($_query);
        $totalRec = $this->_dbCon->numRows();
        
        /* Get list based on pagination and filter */
        $_query->limit($pageLimit, $pageOffset);
        $_query->select("u.* , EXISTS(SELECT * FROM banned_users WHERE `username` =  u.`username`) as banned");
        
        $res = $this->_dbCon->executeS($_query);
        
        $this->smarty->assign('listPagination', $this->listPagination($totalRec,$pageLimit,$pageOffset,$queryFilter));
        $this->smarty->assign('listFilter',$this->listFilter($pageLimit,$currentPage,$queryFilter));
        $userList = '';
        if($res){
            foreach ($res as $row) {
                $userList.= '<tr>'
                        . '<td>'.$row['id'].'</td>'
                        . '<td>'.$row['username'].'</td>'
                        . '<td>'.($this->_userLevelArr[$row['userlevel']]).'</td>'
                        . '<td>'.$row['email'].'</td>'
/*                        . '<td>'.$row['parent_directory'].'</td>' */
                         . '<td>'.$row['aulogin'].'</td>'
                        . '<td><a href="'.$_SERVER['PHP_SELF'].'?action=users&activate&id='.$row['id'].(isset($_REQUEST['limit'])?'&limit='.$_REQUEST['limit']:'').(isset($_REQUEST['page'])?'&page='.$_REQUEST['page']:'').(isset($_REQUEST['query'])?'&query='.$_REQUEST['query']:'').'">'
                        . '<img src="images/'.($row['active']==1?'check.png':'cross.png').'"/>'
                        . '</a></td>'
                        . '<td><a href="'.$_SERVER['PHP_SELF'].'?action=users&block&id='.$row['id'].(isset($_REQUEST['limit'])?'&limit='.$_REQUEST['limit']:'').(isset($_REQUEST['page'])?'&page='.$_REQUEST['page']:'').(isset($_REQUEST['query'])?'&query='.$_REQUEST['query']:'').'">'
                        . '<img src="images/'.($row['banned']==1?'check.png':'cross.png').'"/>'
                        . '</a></td>'
                        . '<td><a href="'.$_SERVER['PHP_SELF'].'?action=users&edit&id='.$row['id'].(isset($_SESSION['limit'])?'&limit='.$_SESSION['limit']:'').(isset($_SESSION['page'])?'&page='.$_SESSION['page']:'').(isset($_SESSION['query'])?'&query='.$_SESSION['query']:'').'">'
                        . '<img src="images/edit.png"/>'
                        . '</a></td>'
                        . '</tr>';
            }
            
         
        }else{
            $userList .= '<tr><td colspan="8">No records found.</td></tr>';
        }
        $this->smarty->assign('userList',$userList);

    }
    
    public function display() {
        $this->handleAction();
    }
    
     private function handleAction() {
            $displayUserList = true;
            if (Tools::getIsset('activate')) {
                $this->processActivation();
            } else if (Tools::getIsset('block')) {
                $this->processBlocking();
            } else if (Tools::getIsset('edit')) {
                $this->editUser();
                $displayUserList = false;
            } else if (Tools::getIsset('user_edit_save')) {
                $this->userEditSave();
            } 
            
            if($displayUserList){
                $this->userListHTML();
                $this->smarty->display("admin-users.tpl");
            }
     
    }
    
    public function processActivation() {
         $userId = isset($_REQUEST['id'])?$_REQUEST['id']:NULL;
        if(isset($_REQUEST['activate']) && $userId){
            
            $_query = "UPDATE users set `active` = NOT `active` where id='".$userId."'";
            $res = $this->_dbCon->execute($_query);
            if($res){
                header('Location: '.$_SERVER['PHP_SELF'].'?action=users'.(isset($_SESSION['limit'])?'&limit='.$_SESSION['limit']:'').(isset($_SESSION['page'])?'&page='.$_SESSION['page']:'').(isset($_SESSION['query'])?'&query='.$_SESSION['query']:''));
            }
            
        }
        
    }
    public function processBlocking() {
         $userId = isset($_REQUEST['id'])?$_REQUEST['id']:NULL;
         $res = false;
         
        if(isset($_REQUEST['block']) && $userId){
            
            /* Calculate number of users at site */
            $q = "SELECT username, EXISTS(SELECT * FROM ".TBL_BANNED_USERS." WHERE `username` = u.`username`) as banned FROM ".TBL_USERS." u WHERE id='$userId'";
            $result = $this->_dbCon->executeS($q);
            
            if($result){
                $user_is_banned = $result[0]['banned'];
                $username = $result[0]['username'];
                
                if($user_is_banned){
                    $q = "DELETE FROM ".TBL_BANNED_USERS." WHERE username= '$username'";
                    $res = $this->_dbCon->execute($q);
                   
                    $q = "INSERT INTO ".TBL_LOGIN_ATTEMPTS." (username, count, timestamp) "
                       . " VALUES ('$username', 0, '".time()."') ON DUPLICATE KEY UPDATE count = 0,timestamp='".time()."'";
                    $this->_dbCon->execute($q);
      
                }else {
                    $q = "INSERT INTO ".TBL_BANNED_USERS." VALUES ('$username', '".time()."')";
                    $res= $this->_dbCon->execute($q);
                }
            }
           
            if($res){
                header('Location: '.$_SERVER['PHP_SELF'].'?action=users'.(isset($_SESSION['limit'])?'&limit='.$_SESSION['limit']:'').(isset($_SESSION['page'])?'&page='.$_SESSION['page']:'').(isset($_SESSION['query'])?'&query='.$_SESSION['query']:''));
            }
            
        }
    }
    public function editUser() {
         $userId = isset($_REQUEST['id'])?$_REQUEST['id']:NULL;
        if(isset($_REQUEST['edit']) && $userId){
            
            $_query = new DbQuery();
            $_query->from("users","u");
            /* First get number of all records */
            $_query->where(" id='$userId'");
            $res = $this->_dbCon->executeS($_query);
          
             $this->smarty->assign(array(
                 'requestURL'=>$_SERVER['PHP_SELF'],
                 'user'=>(isset($res[0])?$res[0]:NULL),
                 'userLevelComboFilter'=>$this->comboFilter("userlevel",$this->_userLevelArr,(isset($res[0]['userlevel'])?$res[0]['userlevel']:NULL)),
                 'activeComboFilter'=>$this->comboFilter("active",array(0=>"NO",1=>"YES"),(isset($res[0]['active'])?$res[0]['active']:NULL)),
                 ));
             $this->smarty->display("admin-user-edit.tpl");
        }
    }
    
    public function userEditSave() {
        
         $userId = Tools::getValue("id");
         
        if(Tools::getValue("user_edit_save") && $userId){
            $_query="UPDATE users SET "
                    . "userlevel = '".Tools::getValue("userlevel")."'"
                    . ", email = '".Tools::getValue("email")."'"
                    . ", aulogin = '".Tools::getValue("aulogin")."'"
                    . ", aupass = '".Tools::getValue("aupass")."'"
                    . ", active = '".Tools::getValue("active")."'"
                    ." WHERE id='".$userId."'";
            
            $res = $this->_dbCon->execute($_query);
            
            if($res){
                header('Location: '.$_SERVER['PHP_SELF'].'?action=users'.(isset($_SESSION['limit'])?'&limit='.$_SESSION['limit']:'').(isset($_SESSION['page'])?'&page='.$_SESSION['page']:'').(isset($_SESSION['query'])?'&query='.$_SESSION['query']:''));
            }
            
        }
    }
}
