<?php
include("AdminInit.php");

if (!$session->logged_in)
   header("Location: login.php");
else {
if (!$session->isAdmin())
   header("Location: "._SITE_DIRECTORY_."main.php");
}
$action = isset($_REQUEST['action'])?$_REQUEST['action']:NULL;
?>
<!DOCTYPE HTML>
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
               $("#<?php echo $action;?>menu").addClass("current");
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

<body >
<!-- begin header -->
<div id="header" style="">
    <div id="top-header">
         <div id="headerLeft">
                    REAtlas - Admin
                    <div id="subheader">Aarhus University,Aarhus Denmark</div>
                </div>
    </div>
	<!-- begin navigation -->
	<nav id="navigation">
		<ul>
                    <li id="homemenu"><a href="<?php echo _SITE_DIRECTORY_; ?>admin/">Home</a></li>
                    <li id="usersmenu"><a href="<?php echo _SITE_DIRECTORY_; ?>admin/?action=users">Users</a></li>
                    <li id="statisticsmenu"><a href="<?php echo _SITE_DIRECTORY_; ?>admin/?action=statistics">Statistics</a></li>
                    <li id="configurationsmenu"><a href="<?php echo _SITE_DIRECTORY_; ?>admin/?action=configurations">Configurations</a></li>
                    <li id="logoutmenu"><a href="<?php echo _SITE_DIRECTORY_; ?>process.php?ref=admin">Logout</a></li>
		</ul>
	</nav>
	<!-- end navigation -->
	
</div>
<!-- end header -->
<!-- begin container -->
<div id="container">
    <?php 
    if($action == "users") {
        $adminUsers = new AdminUsers();
        if(isset($_REQUEST['activate'])){
            $adminUsers->processActivation();
        }else if(isset($_REQUEST['edit'])){
            $adminUsers->editUser();
            $adminUsers->display();
        }else {
            $adminUsers->userListHTML();
            $adminUsers->display();
        }
    }else {
        $adminUsers = new AdminFront();
        $adminUsers->display();
    }
    ?>
</div>
<!-- end container -->
<div id="footer" >
                © 2013-2014 Aarhus University - au.dk
            </div>
</body>
</html>