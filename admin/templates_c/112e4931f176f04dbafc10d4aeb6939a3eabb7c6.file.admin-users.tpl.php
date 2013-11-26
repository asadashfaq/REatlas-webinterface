<?php /* Smarty version Smarty-3.1.15, created on 2013-11-25 22:10:46
         compiled from "/var/www/html/reatlas/admin/templates/admin-users.tpl" */ ?>
<?php /*%%SmartyHeaderCode:21286770035293bcd65d3ae6-19962535%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '112e4931f176f04dbafc10d4aeb6939a3eabb7c6' => 
    array (
      0 => '/var/www/html/reatlas/admin/templates/admin-users.tpl',
      1 => 1385413843,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21286770035293bcd65d3ae6-19962535',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'listPagination' => 0,
    'listFilter' => 0,
    'userList' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.15',
  'unifunc' => 'content_5293bcd65e5025_81469888',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5293bcd65e5025_81469888')) {function content_5293bcd65e5025_81469888($_smarty_tpl) {?><?php echo $_smarty_tpl->tpl_vars['listPagination']->value;?>

<div id="content">
    <table cellspacing="0">
    <tr>
        <th>ID</th>
        <th>User Name</th>
        <th>User Level</th>
        <th>E-mail</th>
     
         <th>AU login</th>
        <th>Active</th>             
        <th>Blocked</th>
        <th>&nbsp;</th>
    </tr>
   <?php echo $_smarty_tpl->tpl_vars['listFilter']->value;?>

   <?php echo $_smarty_tpl->tpl_vars['userList']->value;?>

</table>
</div><?php }} ?>
