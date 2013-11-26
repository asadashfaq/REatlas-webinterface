<?php /* Smarty version Smarty-3.1.15, created on 2013-11-25 21:20:52
         compiled from "/var/www/html/reatlas/admin/templates/admin-head.tpl" */ ?>
<?php /*%%SmartyHeaderCode:173995982052926e28a74cd6-20894707%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '74118d5fc8071c490ee6581dd6144b37457a63df' => 
    array (
      0 => '/var/www/html/reatlas/admin/templates/admin-head.tpl',
      1 => 1385410847,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '173995982052926e28a74cd6-20894707',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.15',
  'unifunc' => 'content_52926e28a791b0_71211894',
  'variables' => 
  array (
    'action' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52926e28a791b0_71211894')) {function content_52926e28a791b0_71211894($_smarty_tpl) {?><!DOCTYPE HTML>
<html>
<head>
	<!-- begin meta -->
    <meta charset="utf-8">
    <meta name="description" content="This is a simple and elegant navigation menu built with HTML5 and CSS3.">
    <meta name="keywords" content="HTML5, CSS3, navigation, navigation menu, gray">
    <meta name="author" content="Ixtendo">
	<!-- end meta -->
	
	<!-- begin CSS -->
    <link href="css/style.css" type="text/css" rel="stylesheet">
	<!--link href="css/html5-reset.css" type="text/css" rel="stylesheet"-->
	<!-- end CSS -->
	
	<!-- begin JS -->
        <script src="js/jquery/jquery-1.10.2.min.js" type="text/javascript"></script> 
	<script src="js/modernizr-2.0.6.min.js" type="text/javascript"></script> 
        <script type="text/javascript">
        $(function() {
               /* For zebra striping */
               $("#<?php echo $_smarty_tpl->tpl_vars['action']->value;?>
menu").addClass("current");
       });
       </script>
        <script type="text/javascript">
        $(function() {
               /* For zebra striping */
               $("table tr:nth-child(odd)").addClass("odd-row");
                       /* For cell text alignment */
                       $("table td:first-child, table th:first-child").addClass("first");
                       /* For removing the last border */
                       $("table td:last-child, table th:last-child").addClass("last");
       });
       </script>
	<!-- end JS -->
	
    <title>REAtlas - Admin</title>
</head>
<?php }} ?>
