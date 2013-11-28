<?php /* Smarty version Smarty-3.1.15, created on 2013-11-25 21:24:42
         compiled from "/var/www/html/reatlas/admin/templates/admin-nav.tpl" */ ?>
<?php /*%%SmartyHeaderCode:97161337952926e28a7ab97-23054874%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2c4dd033b8cf2203899552ef630e8f4ee4f1167c' => 
    array (
      0 => '/var/www/html/reatlas/admin/templates/admin-nav.tpl',
      1 => 1385411078,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '97161337952926e28a7ab97-23054874',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.15',
  'unifunc' => 'content_52926e28a82ca8_70482551',
  'variables' => 
  array (
    'siteURL' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52926e28a82ca8_70482551')) {function content_52926e28a82ca8_70482551($_smarty_tpl) {?><!-- begin navigation -->
<nav id="navigation">
        <ul>
            <li id="homemenu"><a href="<?php echo $_smarty_tpl->tpl_vars['siteURL']->value;?>
admin/">Home</a></li>
            <li id="usersmenu"><a href="<?php echo $_smarty_tpl->tpl_vars['siteURL']->value;?>
admin/?action=users">Users</a></li>
            <li id="statisticsmenu"><a href="<?php echo $_smarty_tpl->tpl_vars['siteURL']->value;?>
admin/?action=statistics">Statistics</a></li>
            <li id="configurationsmenu"><a href="<?php echo $_smarty_tpl->tpl_vars['siteURL']->value;?>
admin/?action=configurations">Configurations</a></li>
            <li id="logoutmenu"><a href="<?php echo $_smarty_tpl->tpl_vars['siteURL']->value;?>
process.php?ref=admin">Logout</a></li>
        </ul>
</nav>
<!-- end navigation -->
<?php }} ?>
