<?php

/*
 * Here comes the text of your license
 * Each line should be prefixed with  * 
 */

/**
 * Description of user
 *
 * @author manila
 */
class user {
    private $_table = "users";
    var $userid;
    var $fullname;
    var $username;
    var $userlevel;
    var $email;
    var $parent_directory;
    var $active;
    var $aulogin;
    private $dbquery;
    
    public function user(){
        $dbquery = new DbQuery();
        $dbquery->from($_table);
        Tools::p($dbquery->build());
    }
    
}
