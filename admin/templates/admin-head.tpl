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
               $("#{$action}menu").addClass("current");
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
