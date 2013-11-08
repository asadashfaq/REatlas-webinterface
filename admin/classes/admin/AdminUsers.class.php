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
        
        $this->userListHTML();
    }
    
    private function listPagination($recNum,$pagelimit = 10,$pageoffset=0) {
        
        $limitArr = array('10'=>'10','20'=>'20','30'=>'30','50'=>'50','100'=>'100','0'=>'All');
        $localHtml = '<div id="paginationBar" style="width:65%; padding: 10px;margin: 50px auto;">';
        $localHtml .= '';
        // page navigation
         $localHtml .= '<span style="float:left;clear:both;">';
        if($recNum && $recNum >0 && $pagelimit>0){
            $paginationCnt = ceil(($recNum/$pagelimit));
            if($paginationCnt>1){
                $localHtml .= '<a href="'.$_SERVER['PHP_SELF'].'?action=users&limit='.$pagelimit.'&page=1"><-</a> ';
                for($i=0;$i<$paginationCnt;$i++){
                    $localHtml .= '<a href="'.$_SERVER['PHP_SELF'].'?action=users&limit='.$pagelimit.'&page='.($i+1).'">'.($i+1).'</a> ';
                }
                $localHtml .= ' <a href="'.$_SERVER['PHP_SELF'].'?action=users&limit='.$pagelimit.'&page='.$paginationCnt.'">-></a> ';
               
            }
        }
         $localHtml .= '</span>';
        // page limit combo
        $localHtml .= '<span style="float:right;clear:both;">Items per page:<select  id="pageLimit" name="pageLimit">';
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
                    var newLoc = "'.$_SERVER['PHP_SELF'].'?action=users&limit="+$(this).val();
                    window.location = newLoc;
                  });'
                . '</script>';
        $localHtml .= '</div>';
        
        return $localHtml;
    }
    private function listFilter($pagelimit,$currentPage) {
        $localHtml = '';
        $localHtml .= '<tr id="filterBar" class="theader">'
                . '<td><input type="text" id="id"/></td>'
                . '<td><input type="text" id="username"/></td>'
                . '<td><select id="userlevel">'
                . '<option value="">--</option>'
                . '<option value="0">Guest</option>'
                . '<option value="1">Agent</option>'
                . '<option value="2">Member</option>'
                . '<option value="8">Master</option>'
                . '<option value="9">Admin</option>'
                . '</select></td>'
                . '<td><input type="text" id="email"/></td>'
                . '<td><input type="text" id="parent_dir"/></td>'
                . '<td><select id="active">'
                . '<option value="">--</option>'
                . '<option value="0">NO</option>'
                . '<option value="1">YES</option>'
                . '</select></td>'
                . '<td><input type="text" id="aulogin"/></td>'
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
    private function userListHTML() {
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
        $res = $this->_dbCon->executeS($_query);
        $totalRec = $this->_dbCon->numRows();
        
        $_query->limit($pageLimit, $pageOffset);
        $_query->where($where);
      
        $res = $this->_dbCon->executeS($_query);
        
        $this->_html .= $this->listPagination($totalRec,$pageLimit,$pageOffset);
        
        $this->_html .= '<table cellspacing="0">';
        $this->_html .= '<tr><th>ID</th><th>User Name</th><th>User Level</th><th>E-mail</th><th>Parent Dir</th><th>Active</th><th>AU login</th><th>&nbsp;</th></tr>';
        $this->_html .= $this->listFilter($pageLimit,$currentPage);
        if($res){
            foreach ($res as $row) {
                $this->_html .= '<tr><td>'.$row['id'].'</td><td>'.$row['username'].'</td><td>'.($this->_userLevelArr[$row['userlevel']]).'</td><td>'.$row['email'].'</td><td>'.$row['parent_directory'].'</td><td>'.($row['active']==1?'YES':'NO').'</td><td>'.$row['aulogin'].'</td><td>&nbsp;</td></tr>';
            }
            
         
        }else{
            $this->_html .= '<tr><td colspan="8">No records found.</td></tr>';
        }
         $this->_html .= '</table>';
    }
    
    public function display() {
        echo $this->_html;
    }
}
