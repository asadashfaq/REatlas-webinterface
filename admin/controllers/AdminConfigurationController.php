<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminConfigurationController
 *
 * @author manila
 */
class AdminConfigurationController extends FrontController {

    var $action;

    function __construct() {
        parent::__construct();
    }

    public function display() {
        $this->smarty->assign(array(
            'requestURL' => $_SERVER['PHP_SELF'],
        ));
        $this->displaySubMenu();
        $this->handleAction();
    }

    private function displaySubMenu() {
        $this->smarty->display("admin-config-submenu.tpl");
    }

    private function handleAction() {
        if (Tools::getIsset('editGen')) {
            if(Tools::getIsset('config_gen_edit_save')){
                Configurations::updateConfiguration('SERVER_URL',  Tools::getValue("serverURL"));
                Configurations::updateConfiguration('SITE_DIRECTORY',  Tools::getValue("siteDir"));
                Configurations::updateConfiguration('TRACK_VISITORS',  Tools::getValue("trackLogin"));
                header("Location: ".$_SERVER['PHP_SELF']."?action=configurations&editGen");
            }
            $this->smarty->assign(array(
                'editGen' => true,
                'serverURL' => Configurations::getConfiguration('SERVER_URL'),
                'siteDir' => Configurations::getConfiguration('SITE_DIRECTORY'),
                'trackLogin' => Configurations::getConfiguration('TRACK_VISITORS'),
                'heading' => "General",
                'requestURL' => $_SERVER['PHP_SELF']."?action=configurations&editGen",
            ));
        } else if (Tools::getIsset('editUser')) {
            if(Tools::getIsset('config_user_edit_save')){
                Configurations::updateConfiguration('USER_ID_SIZE_MIN',  Tools::getValue("useridsizemin"));
                Configurations::updateConfiguration('USER_ID_SIZE_MAX',  Tools::getValue("useridsizemax"));
                Configurations::updateConfiguration('USER_PASS_SIZE_MIN',  Tools::getValue("userpasssizemin"));
                Configurations::updateConfiguration('USER_PASS_SIZE_MAX',  Tools::getValue("userpasssizemax"));
                Configurations::updateConfiguration('USER_TIMEOUT',  Tools::getValue("usertimeout"));
                Configurations::updateConfiguration('LOGIN_ATTEMPTS',  Tools::getValue("userloginatmp"));
                Configurations::updateConfiguration('ALL_LOWERCASE',  Tools::getValue("usernamelower"));
                
                header("Location: ".$_SERVER['PHP_SELF']."?action=configurations&editUser");
            }
            $this->smarty->assign(array(
                'editUser' => true,
                'heading' => "User/Registration",
                'useridsizemin' => Configurations::getConfiguration('USER_ID_SIZE_MIN'),
                'useridsizemax' => Configurations::getConfiguration('USER_ID_SIZE_MAX'),
                'userpasssizemin' => Configurations::getConfiguration('USER_PASS_SIZE_MIN'),
                'userpasssizemax' => Configurations::getConfiguration('USER_PASS_SIZE_MAX'),
                'usertimeout' => Configurations::getConfiguration('USER_TIMEOUT'),
                'userloginatmp' => Configurations::getConfiguration('LOGIN_ATTEMPTS'),
                'usernamelower' => Configurations::getConfiguration('ALL_LOWERCASE'),
                'requestURL' => $_SERVER['PHP_SELF']."?action=configurations&editUser",
            ));
        } else if (Tools::getIsset('editReatlas')) {
            if(Tools::getIsset('config_reat_edit_save')){
                Configurations::updateConfiguration('REATLAS_CLIENT_PATH',  Tools::getValue("reatlasClientPath"));
                Configurations::updateConfiguration('PEPSI_SERVER',  Tools::getValue("pepsiServer"));
                Configurations::updateConfiguration('PEPSI_DEFAULT_USER_GROUP',  Tools::getValue("pepsiDefUserGrp"));
                Configurations::updateConfiguration('PEPSI_ADMIN_USER',  Tools::getValue("pepsiAdminUser"));
                Configurations::updateConfiguration('PEPSI_ADMIN_PASS',  Tools::getValue("pepsiAdminPass"));
                header("Location: ".$_SERVER['PHP_SELF']."?action=configurations&editReatlas");
            }
            $this->smarty->assign(array(
                'editReatlas' => true,
                'heading' => "REAtlas",
                'reatlasClientPath' => Configurations::getConfiguration('REATLAS_CLIENT_PATH'),
                'pepsiServer' => Configurations::getConfiguration('PEPSI_SERVER'),
                'pepsiDefUserGrp'=>Configurations::getConfiguration('PEPSI_DEFAULT_USER_GROUP'),
                'pepsiAdminUser' => Configurations::getConfiguration('PEPSI_ADMIN_USER'),
                'pepsiAdminPass' => Configurations::getConfiguration('PEPSI_ADMIN_PASS'),
                'requestURL' => $_SERVER['PHP_SELF']."?action=configurations&editReatlas",
              
            ));
        } else if (Tools::getIsset('editDb')) {
            if(Tools::getIsset('config_db_edit_save')){
                header("Location: ".$_SERVER['PHP_SELF']."?action=configurations&editDb");
            }
            $this->smarty->assign(array(
                'editDb' => true,
                'serverURL' => Configurations::getConfiguration('SERVER_URL'),
                'heading' => "DB",
                'requestURL' => $_SERVER['PHP_SELF']."?action=configurations&editDb",
            ));
        } else if (Tools::getIsset('editNotif')) {
            if(Tools::getIsset('config_notif_edit_save')){
                Configurations::updateConfiguration('NOTIF_TO_ADMIN_USR',  Tools::getValue("notifToAdmin"));
                Configurations::updateConfiguration('NOTIF_USR_REG',  Tools::getValue("notifUsrReg",0));
                Configurations::updateConfiguration('NOTIF_USR_BLOCK',  Tools::getValue("notifUsrBlock",0));
                Configurations::updateConfiguration('NOTIF_USR_PASS_REQ',  Tools::getValue("notifUsrPasReq",0));
                Configurations::updateConfiguration('NOTIF_USR_LOGIN',  Tools::getValue("notifUsrLogin",0));
                Configurations::updateConfiguration('NOTIF_USR_CUTOUT_REQ',  Tools::getValue("notifUsrCutReq",0));
                Configurations::updateConfiguration('NOTIF_EMAIL',  Tools::getValue("notifEmail"));
                Configurations::updateConfiguration('NOTIF_TO_USR_WELCOME',  Tools::getValue("notifToUsrWelcome",0));
                Configurations::updateConfiguration('NOTIF_TO_USR_ACTIVE',  Tools::getValue("notifToUsrActive",0));
                Configurations::updateConfiguration('NOTIF_TO_USR_BLOCK',  Tools::getValue("notifToUsrBlock",0));
                Configurations::updateConfiguration('EMAIL_FROM_NAME',  Tools::getValue("emailFromName"));
                Configurations::updateConfiguration('EMAIL_FROM_ADDR',  Tools::getValue("emailFromAddr"));
                Configurations::updateConfiguration('EMAIL_HTML',  Tools::getValue("mailFormat",0));
                header("Location: ".$_SERVER['PHP_SELF']."?action=configurations&editNotif");
            }
            
            $this->smarty->assign(array(
                'editNotif' => true,
                'serverURL' => Configurations::getConfiguration('SERVER_URL'),
                'heading' => "Notification",
                'notifUser' => $this->generateUserSelect(ADMIN_LEVEL,"notifToAdmin",Configurations::getConfiguration('NOTIF_TO_ADMIN_USR'),true),
                'notifUsrReg' => Configurations::getConfiguration('NOTIF_USR_REG'),
                'notifUsrBlock' => Configurations::getConfiguration('NOTIF_USR_BLOCK'),
                'notifUsrPasReq' => Configurations::getConfiguration('NOTIF_USR_PASS_REQ'),
                'notifUsrLogin' => Configurations::getConfiguration('NOTIF_USR_LOGIN'),
                'notifUsrCutReq' => Configurations::getConfiguration('NOTIF_USR_CUTOUT_REQ'),
                'notifEmail' =>Configurations::getConfiguration('NOTIF_EMAIL'),
                'notifToUsrWelcome' =>Configurations::getConfiguration('NOTIF_TO_USR_WELCOME'),
                'notifToUsrActive' =>Configurations::getConfiguration('NOTIF_TO_USR_ACTIVE'),
                'notifToUsrBlock' =>Configurations::getConfiguration('NOTIF_TO_USR_BLOCK'),
                'emailFromName' =>Configurations::getConfiguration('EMAIL_FROM_NAME'),
                'emailFromAddr' =>Configurations::getConfiguration('EMAIL_FROM_ADDR'),
                'mailFormat' =>Configurations::getConfiguration('EMAIL_HTML'),
                'requestURL' => $_SERVER['PHP_SELF']."?action=configurations&editNotif",
            ));
        }else {
           if(Tools::getIsset('config_gen_edit_save')){
                Configurations::updateConfiguration('SERVER_URL',  Tools::getValue("serverURL"));
                Configurations::updateConfiguration('SITE_DIRECTORY',  Tools::getValue("siteDir"));
                Configurations::updateConfiguration('TRACK_VISITORS',  Tools::getValue("trackLogin"));
                header("Location: ".$_SERVER['PHP_SELF']."?action=configurations");
            }
            $this->smarty->assign(array(
                'editGen' => true,
                'serverURL' => Configurations::getConfiguration('SERVER_URL'),
                 'siteDir' => Configurations::getConfiguration('SITE_DIRECTORY'),
                'trackLogin' => Configurations::getConfiguration('TRACK_VISITORS'),
                'heading' => "General",
                'requestURL' => $_SERVER['PHP_SELF']."?action=configurations",
            ));
        }

        $this->smarty->display("admin-config.tpl");
    }
    
    private function generateUserSelect($level = ADMIN_LEVEL,$id="adminuser",$selected=null,$default=false){
        
        $_query = new DbQuery();
        $_query->from("users","u");
        $_query->where("NOT EXISTS(SELECT * FROM banned_users WHERE `username` =  u.`username`) AND userlevel=".$level);
        $_query->select("u.id, u.username");
        $userArr = $this->_dbCon->executeS($_query);
        $cmb = "<select id='$id' name='$id'>";
        if($default)
            $cmb.= "<option value=''>--</option>";
        
        foreach ($userArr as $user) {
            $cmb .= "<option value='".$user['username']."' ".($selected!==NULL && $selected==$user['username']?"selected":"").">".$user['username']."</option>";
        }
            $cmb .= "</select>"; 
            
            return $cmb;
    }

}

?>
