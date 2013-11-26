<?php /* Smarty version Smarty-3.1.15, created on 2013-11-25 23:11:14
         compiled from "/var/www/html/reatlas/admin/templates/admin-config-submenu.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1229929305293c80b9b8126-87653236%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd6cc97d390a6d383e2f496a99a398c5957024bb8' => 
    array (
      0 => '/var/www/html/reatlas/admin/templates/admin-config-submenu.tpl',
      1 => 1385417472,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1229929305293c80b9b8126-87653236',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.15',
  'unifunc' => 'content_5293c80b9d6726_52694299',
  'variables' => 
  array (
    'requestURL' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5293c80b9d6726_52694299')) {function content_5293c80b9d6726_52694299($_smarty_tpl) {?><style>
 #navSublist
{
    background-color: lightgray;
    left: 20%;
    position: relative;
    text-align: center;
    width: 50%
}
#navSublist li
{
    display: inline;
    list-style-type: none;
    padding-right: 20px;

}
</style>
<div id="navSublist">
    <ul>
        <li><a href="<?php echo $_smarty_tpl->tpl_vars['requestURL']->value;?>
?action=configurations&editGen">General</a></li>
        <li><a href="<?php echo $_smarty_tpl->tpl_vars['requestURL']->value;?>
?action=configurations&editUser">User/Registration</a></li>
        <li><a href="<?php echo $_smarty_tpl->tpl_vars['requestURL']->value;?>
?action=configurations&editReatlas">REAtlas</a></li>
        <li><a href="<?php echo $_smarty_tpl->tpl_vars['requestURL']->value;?>
?action=configurations&editDb">DB</a></li>
        <li><a href="<?php echo $_smarty_tpl->tpl_vars['requestURL']->value;?>
?action=configurations&editNotif">Notification</a></li>
    </ul>
</div>
    <br/><?php }} ?>
