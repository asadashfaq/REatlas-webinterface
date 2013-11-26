<?php /* Smarty version Smarty-3.1.15, created on 2013-11-25 22:36:28
         compiled from "/var/www/html/reatlas/admin/templates/admin-user-edit.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15972022155293c082e5edb3-45778877%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f95a724a1385bf3435c270c14491b9bd176cd3df' => 
    array (
      0 => '/var/www/html/reatlas/admin/templates/admin-user-edit.tpl',
      1 => 1385415385,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15972022155293c082e5edb3-45778877',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.15',
  'unifunc' => 'content_5293c082ea8277_10728732',
  'variables' => 
  array (
    'requestURL' => 0,
    'limit' => 0,
    'page' => 0,
    'query' => 0,
    'user' => 0,
    'userLevelComboFilter' => 0,
    'activeComboFilter' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5293c082ea8277_10728732')) {function content_5293c082ea8277_10728732($_smarty_tpl) {?><div id="contentEdit">
    <header id="contentHeader">
    <h1>Edit User</h1>
    <a href="<?php echo $_smarty_tpl->tpl_vars['requestURL']->value;?>
?action=users<?php if (isset($_smarty_tpl->tpl_vars['limit']->value)) {?>&limit=<?php echo $_smarty_tpl->tpl_vars['limit']->value;?>
<?php }?><?php if (isset($_smarty_tpl->tpl_vars['page']->value)) {?>&page=<?php echo $_smarty_tpl->tpl_vars['page']->value;?>
<?php }?><?php if (isset($_smarty_tpl->tpl_vars['query']->value)) {?>&query=<?php echo $_smarty_tpl->tpl_vars['query']->value;?>
<?php }?>" >Back to List</a>
    </header>
    <hr/>
    <form name="loginform" id="loginform" action="<?php echo $_smarty_tpl->tpl_vars['requestURL']->value;?>
?action=users<?php if (isset($_smarty_tpl->tpl_vars['limit']->value)) {?>&limit=<?php echo $_smarty_tpl->tpl_vars['limit']->value;?>
<?php }?><?php if (isset($_smarty_tpl->tpl_vars['page']->value)) {?>&page=<?php echo $_smarty_tpl->tpl_vars['page']->value;?>
<?php }?><?php if (isset($_smarty_tpl->tpl_vars['query']->value)) {?>&query=<?php echo $_smarty_tpl->tpl_vars['query']->value;?>
<?php }?>" method="post">
            <table>
            <tr>
            <td><label >Username</label></td>
            <td><?php echo $_smarty_tpl->tpl_vars['user']->value['username'];?>
</td>
            </tr>
            <tr>
            <td><label for="user_login">User level</label></td>
            <td><?php echo $_smarty_tpl->tpl_vars['userLevelComboFilter']->value;?>
</td>
            </tr>
            <tr>
            <td><label >E-mail</label></td>
            <td><input name="email" class="input" size="20" type="text" value="<?php echo $_smarty_tpl->tpl_vars['user']->value['email'];?>
"/></td>
            </tr>
            <tr>
            <td><label >AU unix user name</label></td>
            <td><input name="aulogin"  class="input" size="20" type="text" value="<?php echo $_smarty_tpl->tpl_vars['user']->value['aulogin'];?>
"/></td>
            </tr>
            <tr>
            <td><label >AU unix user pass</label></td>
            <td><input name="aupass"  class="input" size="20" type="text" value="<?php echo $_smarty_tpl->tpl_vars['user']->value['aupass'];?>
"/></td>
            </tr>
            <tr>
            <td><label >Active</label></td>
            <td><?php echo $_smarty_tpl->tpl_vars['activeComboFilter']->value;?>
</td>
            </tr>
            <tr><td colspan="2"></td></tr>
            <input type="hidden" name="id" value="<?php echo $_smarty_tpl->tpl_vars['user']->value['id'];?>
"/>
            <input type="hidden" name="user_edit_save" value="1"/>
            <tr><td colspan="2">
            <input value="Save" type="submit"/>
            </td></tr>
            </table>
            </form>
            <br/>
</div><br/><?php }} ?>
