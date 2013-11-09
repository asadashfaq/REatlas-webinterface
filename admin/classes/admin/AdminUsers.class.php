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
class AdminUsers {
    
    private $_userLevelArr = array("0"=>'Guest',"1"=>'Agent',"2"=>'Member',"8"=>'Master',"9"=>'Admin');
    var $_html;
    var $_dbCon;
    
    public function AdminUsers() {
        $this->_dbCon = DB::getInstance();
        $this->filterParams();
    }
    
    private function filterParams() {
        if (isset($_REQUEST['limit'])) {
            $_SESSION['limit'] = $_REQUEST['limit'];
        }
        if (isset($_REQUEST['page'])) {
            $_SESSION['page'] = $_REQUEST['page'];
        }
        if (isset($_REQUEST['query'])) {
            $_SESSION['query'] = $_REQUEST['query'];
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
    private function listFilter($pagelimit,$currentPage,$queryFilter=null) {
        $filterVal = array();
        if($queryFilter && $queryFilter !=''){
            $queryFilterArr = explode(';', $queryFilter);
            foreach ($queryFilterArr as $q) {
                $queryParamArr = explode(':', $q);
                $filterVal[$queryParamArr[0]] = $queryParamArr[1];
             }
        }
        $localHtml = '';
        $localHtml .= '<tr id="filterBar" class="theader">'
                . '<td><input type="text" id="id" style="width:35px;" value="'.(isset($filterVal['id'])?$filterVal['id']:'').'"/></td>'
                . '<td><input type="text" id="username" style="width:100px;" value="'.(isset($filterVal['username'])?$filterVal['username']:'').'"/></td>'
                . '<td><select id="userlevel">'
                . '<option value="">--</option>'
                . '<option value="0">Guest</option>'
                . '<option value="1">Agent</option>'
                . '<option value="2">Member</option>'
                . '<option value="8">Master</option>'
                . '<option value="9">Admin</option>'
                . '</select></td>'
                . '<td><input type="text" id="email" style="width:100px;" value="'.(isset($filterVal['email'])?$filterVal['email']:'').'"/></td>'
/*                . '<td><input type="text" id="parent_dir" style="width:100px;" value="'.(isset($filterVal['parent_dir'])?$filterVal['parent_dir']:'').'"/></td>' */
                . '<td><select id="active">'
                . '<option value="">--</option>'
                . '<option value="0">NO</option>'
                . '<option value="1">YES</option>'
                . '</select></td>'
                . '<td><input type="text" id="aulogin" style="width:100px;" value="'.(isset($filterVal['aulogin'])?$filterVal['aulogin']:'').'"/></td>'
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
            foreach ($queryFilterArr as $q) {
                $queryParamArr = explode(':', $q);
                if($queryParamArr[2] == "like"){
                    $where .= $queryParamArr[0]." like '%".$queryParamArr[1]."%' AND";
                }else {
                    $where .= $queryParamArr[0]." = '".$queryParamArr[1]."' AND";
                }
            }
            $where = substr($where, 0, strlen($where)-3);
        }
        $_query = new DbQuery();
        $_query->from("users");
         $_query->where($where);
        $res = $this->_dbCon->executeS($_query);
        $totalRec = $this->_dbCon->numRows();
        
        $_query->limit($pageLimit, $pageOffset);
        $_query->where($where);
      
        $res = $this->_dbCon->executeS($_query);
        
        $this->_html .= $this->listPagination($totalRec,$pageLimit,$pageOffset,$queryFilter);
        $this->_html .= '<div id="content">';
        $this->_html .= '<table cellspacing="0">';
        $this->_html .= '<tr><th>ID</th><th>User Name</th><th>User Level</th><th>E-mail</th><!--th>Parent Dir</th--><th>Active</th><th>AU login</th><th>&nbsp;</th></tr>';
        $this->_html .= $this->listFilter($pageLimit,$currentPage,$queryFilter);
        if($res){
            foreach ($res as $row) {
                $this->_html .= '<tr>'
                        . '<td>'.$row['id'].'</td>'
                        . '<td>'.$row['username'].'</td>'
                        . '<td>'.($this->_userLevelArr[$row['userlevel']]).'</td>'
                        . '<td>'.$row['email'].'</td>'
/*                        . '<td>'.$row['parent_directory'].'</td>' */
                        . '<td><a href="'.$_SERVER['PHP_SELF'].'?action=users&activate&id='.$row['id'].(isset($_REQUEST['limit'])?'&limit='.$_REQUEST['limit']:'').(isset($_REQUEST['page'])?'&page='.$_REQUEST['page']:'').(isset($_REQUEST['query'])?'&query='.$_REQUEST['query']:'').'">'
                        . '<img src="images/'.($row['active']==1?'check.png':'cross.png').'"/>'
                        . '</a></td>'
                        . '<td>'.$row['aulogin'].'</td>'
                        . '<td><a href="'.$_SERVER['PHP_SELF'].'?action=users&edit&id='.$row['id'].(isset($_SESSION['limit'])?'&limit='.$_SESSION['limit']:'').(isset($_SESSION['page'])?'&page='.$_SESSION['page']:'').(isset($_SESSION['query'])?'&query='.$_SESSION['query']:'').'">'
                        . '<img src="images/edit.png"/>'
                        . '</a></td>'
                        . '</tr>';
            }
            
         
        }else{
            $this->_html .= '<tr><td colspan="8">No records found.</td></tr>';
        }
         $this->_html .= '</table>';
         $this->_html .= '</div>';
    }
    
    public function display() {
        
        echo $this->_html;
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
    public function editUser() {
         $userId = isset($_REQUEST['id'])?$_REQUEST['id']:NULL;
        if(isset($_REQUEST['edit']) && $userId){
             $this->_html = '<div id="contentEdit">';
             $this->_html .= '<h1>Edit User</h1>';
             $this->_html .= '<hr/>';
             $this->_html .= '<form name="loginform" id="loginform" action="'.$_SERVER['PHP_SELF'].'?action=users'.(isset($_SESSION['limit'])?'&limit='.$_SESSION['limit']:'').(isset($_SESSION['page'])?'&page='.$_SESSION['page']:'').(isset($_SESSION['query'])?'&query='.$_SESSION['query']:'').'" method="post">'
                     . '</form>';
             $this->_html .= '</div>';
        
        }
    }
}
