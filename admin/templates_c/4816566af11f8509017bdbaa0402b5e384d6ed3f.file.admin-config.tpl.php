<?php /* Smarty version Smarty-3.1.15, created on 2013-11-26 01:59:21
         compiled from "/var/www/html/reatlas/admin/templates/admin-config.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15264420755293c979800bc2-39848555%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4816566af11f8509017bdbaa0402b5e384d6ed3f' => 
    array (
      0 => '/var/www/html/reatlas/admin/templates/admin-config.tpl',
      1 => 1385427482,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15264420755293c979800bc2-39848555',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.15',
  'unifunc' => 'content_5293c979801a03_87696157',
  'variables' => 
  array (
    'heading' => 0,
    'requestURL' => 0,
    'editGen' => 0,
    'serverURL' => 0,
    'siteDir' => 0,
    'trackLogin' => 0,
    'editUser' => 0,
    'useridsizemin' => 0,
    'useridsizemax' => 0,
    'userpasssizemin' => 0,
    'userpasssizemax' => 0,
    'usertimeout' => 0,
    'userloginatmp' => 0,
    'usernamelower' => 0,
    'editReatlas' => 0,
    'reatlasClientPath' => 0,
    'pepsiServer' => 0,
    'pepsiDefUserGrp' => 0,
    'pepsiAdminUser' => 0,
    'pepsiAdminPass' => 0,
    'editNotif' => 0,
    'notifUsrReg' => 0,
    'notifUsrBlock' => 0,
    'notifUsrPasReq' => 0,
    'notifUsrLogin' => 0,
    'notifUser' => 0,
    'notifEmail' => 0,
    'notifToUsrWelcome' => 0,
    'notifToUsrActive' => 0,
    'notifToUsrBlock' => 0,
    'emailFromName' => 0,
    'emailFromAddr' => 0,
    'mailFormat' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5293c979801a03_87696157')) {function content_5293c979801a03_87696157($_smarty_tpl) {?><div id="contentEdit">
    <header id="contentHeader">
    <h3>Configuration -> <?php echo $_smarty_tpl->tpl_vars['heading']->value;?>
</h3>
    <br/>
    </header>
    <hr/>
    <form name="loginform" id="loginform" action="<?php echo $_smarty_tpl->tpl_vars['requestURL']->value;?>
" method="post">
       <table>
     <?php if (isset($_smarty_tpl->tpl_vars['editGen']->value)) {?>  
          <tr>
            <td><label >Server URL</label></td>
            <td><input name="serverURL" class="input" size="20" type="text" value="<?php echo $_smarty_tpl->tpl_vars['serverURL']->value;?>
"/></td>
          </tr>
           <tr>
            <td><label >Site Directory</label></td>
            <td><input name="siteDir" class="input" size="20" type="text" value="<?php echo $_smarty_tpl->tpl_vars['siteDir']->value;?>
"/></td>
          </tr>
          <tr>
            <td><label >Track User login</label></td>
            <td><input name="trackLogin" class="input" type="checkbox" value="1" <?php if ($_smarty_tpl->tpl_vars['trackLogin']->value==1) {?>checked<?php }?>/></td>
          </tr>
          <tr>
              <td colspan="2">
                <input type="hidden" name="config_gen_edit_save" value="1"/>
               </td>
          </tr>
     <?php } elseif (isset($_smarty_tpl->tpl_vars['editUser']->value)) {?>  
          <tr>
            <td><label >UserID size (min , max)</label></td>
            <td>
                <input name="useridsizemin" class="input" size="5" type="text" value="<?php echo $_smarty_tpl->tpl_vars['useridsizemin']->value;?>
"/>
                <input name="useridsizemax" class="input" size="5" type="text" value="<?php echo $_smarty_tpl->tpl_vars['useridsizemax']->value;?>
"/>
            </td>
          </tr>
          <tr>
            <td><label >UserPass size (min , max)</label></td>
            <td>
                <input name="userpasssizemin" class="input" size="5" type="text" value="<?php echo $_smarty_tpl->tpl_vars['userpasssizemin']->value;?>
"/>
                <input name="userpasssizemax" class="input" size="5" type="text" value="<?php echo $_smarty_tpl->tpl_vars['userpasssizemax']->value;?>
"/>
            </td>
          </tr>
          <tr>
            <td><label >Session timeout</label></td>
            <td><input name="usertimeout" class="input" size="10" type="text" value="<?php echo $_smarty_tpl->tpl_vars['usertimeout']->value;?>
"/></td>
          </tr>
          <tr>
            <td><label >Login Attempts</label></td>
            <td><input name="userloginatmp" class="input" size="10" type="text" value="<?php echo $_smarty_tpl->tpl_vars['userloginatmp']->value;?>
"/></td>
          </tr>
           <tr>
            <td><label >Force User Name to lower case</label></td>
            <td><input name="usernamelower" class="input" type="checkbox" value="1" <?php if ($_smarty_tpl->tpl_vars['usernamelower']->value) {?>checked<?php }?>/></td>
          </tr>
          <tr>
              <td colspan="2">
                <input type="hidden" name="config_user_edit_save" value="1"/>
               </td>
          </tr>
       <?php } elseif (isset($_smarty_tpl->tpl_vars['editReatlas']->value)) {?>  
          <tr>
            <td><label >REAtlas client path</label></td>
            <td><input name="reatlasClientPath" class="input" size="20" type="text" value="<?php echo $_smarty_tpl->tpl_vars['reatlasClientPath']->value;?>
"/></td>
          </tr>
          <tr>
            <td><label >Pepsi Server</label></td>
            <td><input name="pepsiServer" class="input" size="20" type="text" value="<?php echo $_smarty_tpl->tpl_vars['pepsiServer']->value;?>
"/></td>
          </tr>
          <tr>
            <td><label >Pepsi Server default User Group</label></td>
            <td><input name="pepsiDefUserGrp" class="input" size="20" type="text" value="<?php echo $_smarty_tpl->tpl_vars['pepsiDefUserGrp']->value;?>
"/></td>
          </tr>
          <tr>
            <td><label >Pepsi Server default SuperUser</label></td>
            <td><input name="pepsiAdminUser" class="input" size="20" type="text" value="<?php echo $_smarty_tpl->tpl_vars['pepsiAdminUser']->value;?>
"/></td>
          </tr>
           <tr>
            <td><label >Pepsi SuperUser pass</label></td>
            <td><input name="pepsiAdminPass" class="input" size="20" type="text" value="<?php echo $_smarty_tpl->tpl_vars['pepsiAdminPass']->value;?>
"/></td>
          </tr>
          <tr>
              <td colspan="2">
                <input type="hidden" name="config_reat_edit_save" value="1"/>
               </td>
          </tr>
           <?php } elseif (isset($_smarty_tpl->tpl_vars['editNotif']->value)) {?> 
          <tr>
              <td colspan="2">
                  <h4>To main admin</h4>
               </td>
          </tr>
          <tr>
            <td><label >Notify user registration</label></td>
            <td><input name="notifUsrReg" class="input" type="checkbox" value="1" <?php if ($_smarty_tpl->tpl_vars['notifUsrReg']->value==1) {?>checked<?php }?>/></td>
          </tr>
           <tr>
            <td><label >Notify user blocking</label></td>
            <td><input name="notifUsrBlock" class="input" type="checkbox" value="1" <?php if ($_smarty_tpl->tpl_vars['notifUsrBlock']->value) {?>checked<?php }?>/></td>
          </tr>
           <tr>
            <td><label >Notify user password request</label></td>
            <td><input name="notifUsrPasReq" class="input" type="checkbox" value="1" <?php if ($_smarty_tpl->tpl_vars['notifUsrPasReq']->value) {?>checked<?php }?>/></td>
          </tr>
          <tr>
            <td><label >Notify user login</label></td>
            <td><input name="notifUsrLogin" class="input" type="checkbox" value="1" <?php if ($_smarty_tpl->tpl_vars['notifUsrLogin']->value) {?>checked<?php }?>/></td>
          </tr>
          <tr>
            <td><label >Notify to Registered Admin User</label></td>
            <td><?php echo $_smarty_tpl->tpl_vars['notifUser']->value;?>
</td>
          </tr>
           <tr>
            <td><label >Notify to Email</label></td>
            <td><input name="notifEmail" class="input" size="20" type="text" value="<?php echo $_smarty_tpl->tpl_vars['notifEmail']->value;?>
"/></td>
          </tr>
          <tr>
              <td colspan="2">
                  <h4>To Users</h4>
               </td>
          </tr>
           <tr>
            <td><label >Send Welcome mail to user</label></td>
            <td><input name="notifToUsrWelcome" class="input" type="checkbox" value="1" <?php if ($_smarty_tpl->tpl_vars['notifToUsrWelcome']->value) {?>checked<?php }?>/></td>
          </tr>
           <tr>
            <td><label >Notify user when activated</label></td>
            <td><input name="notifToUsrActive" class="input" type="checkbox" value="1" <?php if ($_smarty_tpl->tpl_vars['notifToUsrActive']->value) {?>checked<?php }?>/></td>
          </tr>
           <tr>
            <td><label >Notify user when blocked/unblocked</label></td>
            <td><input name="notifToUsrBlock" class="input" type="checkbox" value="1" <?php if ($_smarty_tpl->tpl_vars['notifToUsrBlock']->value) {?>checked<?php }?>/></td>
          </tr>
          <tr>
              <td colspan="2">
                  <h4>Defaults: Mail from</h4>
               </td>
          </tr>
          <tr>
            <td><label >Name </label></td>
            <td><input name="emailFromName" class="input" size="20" type="text" value="<?php echo $_smarty_tpl->tpl_vars['emailFromName']->value;?>
"/></td>
          </tr>
          <tr>
            <td><label >Email</label></td>
            <td><input name="emailFromAddr" class="input" size="20" type="text" value="<?php echo $_smarty_tpl->tpl_vars['emailFromAddr']->value;?>
"/></td>
          </tr>
          <tr>
            <td><label >Email format</label></td>
            <td>
                <input name="mailFormat" class="input" type="radio" value="0" <?php if ($_smarty_tpl->tpl_vars['mailFormat']->value==0) {?>checked<?php }?>>Plain Text</input>
                <input name="mailFormat" class="input" type="radio" value="1" <?php if ($_smarty_tpl->tpl_vars['mailFormat']->value==1) {?>checked<?php }?>>HTML</input>
            </td>
          </tr>
          <tr>
              <td colspan="2">
                <input type="hidden" name="config_notif_edit_save" value="1"/>
               </td>
          </tr>
       <?php }?>
            <tr>
                <td colspan="2">
                    <input value="Save" type="submit"/>
                </td>
            </tr>
            
            </table>
            </form>
            <br/>
</div><br/><?php }} ?>
