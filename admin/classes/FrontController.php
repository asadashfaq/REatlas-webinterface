<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FrontController
 *
 * @author manila
 */

class FrontController  {
    var $smarty;
    var $_dbCon;
    function __construct() {
        $this->_dbCon = DB::getInstance();
      $this->smarty = new Smarty();
    
      $this->smarty->setTemplateDir(_ADMIN_DIR_ . '/templates');
      $this->smarty->setCompileDir(_ADMIN_DIR_ . '/templates_c');
      $this->smarty->setConfigDir(_ADMIN_DIR_ . '/configs');
      $this->smarty->setCacheDir(_ADMIN_DIR_ . '/cache');
      
    }

}

?>
