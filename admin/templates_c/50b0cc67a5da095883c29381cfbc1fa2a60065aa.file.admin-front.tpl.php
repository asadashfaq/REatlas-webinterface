<?php /* Smarty version Smarty-3.1.15, created on 2013-11-25 21:17:47
         compiled from "/var/www/html/reatlas/admin/templates/admin-front.tpl" */ ?>
<?php /*%%SmartyHeaderCode:118334722752921a3ce5db49-46377827%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '50b0cc67a5da095883c29381cfbc1fa2a60065aa' => 
    array (
      0 => '/var/www/html/reatlas/admin/templates/admin-front.tpl',
      1 => 1385410664,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '118334722752921a3ce5db49-46377827',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.15',
  'unifunc' => 'content_52921a3cf0f649_40277801',
  'variables' => 
  array (
    'runningJobsBlock' => 0,
    'newUpdatesBlock' => 0,
    'serverStatusBlock' => 0,
    'trackingBlock' => 0,
    'onlineUsersBlock' => 0,
    'userUpdatesBlock' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52921a3cf0f649_40277801')) {function content_52921a3cf0f649_40277801($_smarty_tpl) {?><script src="js/jquery/ui/jquery-ui.js"></script>
<link rel="stylesheet" href="css/jquery/ui/themes/smoothness/jquery-ui-1.10.3.custom.css" />
<link rel="stylesheet" href="css/admin-front-grid.css" />

    <div class="column">
    <?php echo $_smarty_tpl->tpl_vars['runningJobsBlock']->value;?>

    <?php echo $_smarty_tpl->tpl_vars['newUpdatesBlock']->value;?>

    </div>
    <div class="column">
    <?php echo $_smarty_tpl->tpl_vars['serverStatusBlock']->value;?>

    <?php echo $_smarty_tpl->tpl_vars['trackingBlock']->value;?>

    </div>
    <div class="column">
    <?php echo $_smarty_tpl->tpl_vars['onlineUsersBlock']->value;?>

    <?php echo $_smarty_tpl->tpl_vars['userUpdatesBlock']->value;?>

    </div>
<script>
$(function() {
  $( ".column" ).sortable({
    connectWith: ".column"
  });

  $( ".portlet" ).addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
    .find( ".portlet-header" )
      .addClass( "ui-widget-header ui-corner-all" )
      .prepend( "<span class=\'ui-icon ui-icon-minusthick\'></span>")
      .end()
    .find( ".portlet-content" );

  $( ".portlet-header .ui-icon" ).click(function() {
    $( this ).toggleClass( "ui-icon-minusthick" ).toggleClass( "ui-icon-plusthick" );
    $( this ).parents( ".portlet:first" ).find( ".portlet-content" ).toggle();
  });

  $( ".column" ).disableSelection();
});
</script><?php }} ?>
