<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of user
 *
 * @author manila
 */
class user {
  var $id;
  var $username;
  var $password;
  var $userkey;
  var $userlevel;
  var $email;
  var $timestamp;
  var $parent_directory;
  var $active;
  var $aulogin;
  var $aupass;
  
  function user($id=null){
      if($id) {
           $sql = "Select * from users where id='".$id."'";
           $res = DB::getInstance()->executeS($sql);
           if($res != null){
               $this->id = $res[0]['id'];
               $this->username = $res[0]['username'];
               $this->password = $res[0]['password'];
               $this->userkey = $res[0]['userkey'];
               $this->userlevel = $res[0]['userlevel'];
               $this->email = $res[0]['email'];
               $this->timestamp = $res[0]['timestamp'];
               $this->parent_directory = $res[0]['parent_directory'];
               $this->active = $res[0]['active'];
               $this->aulogin = $res[0]['aulogin'];
               $this->aupass = $res[0]['aupass'];
           }
      }
  
    }
}

?>
