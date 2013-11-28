<?php
include("init.php");

if ($session->logged_in)
    $profile = new Profile($session->profileid);


?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=7, IE=9, IE=10"> 
        <!--The viewport meta tag is used to improve the presentation and behavior of the samples 
          on iOS devices-->
        <meta name="viewport" content="initial-scale=1, maximum-scale=1,user-scalable=no"> 
        <title>REAtlas - Aarhus</title>

        <link rel="stylesheet" href="http://js.arcgis.com/3.7/js/dojo/dijit/themes/claro/claro.css">
        <link rel="stylesheet" type="text/css" href="http://js.arcgis.com/3.7/js/esri/css/esri.css">
        <link rel="stylesheet" href="css/layout.css"/> 
        
        <script>var dojoConfig = {parseOnLoad: true};</script>
        <script src="http://js.arcgis.com/3.7/"></script>
        <script src="js/jquery/jquery-1.9.1.js"></script>
        <script src="js/jquery/ui/jquery-ui.js"></script>
        
        <script>

<?php if (SELECTION_TOOLBAR) { ?>
                var showToolbar=1;
<?php } else {?>
            var showToolbar=0;
  <?php } ?>  
        var defaultUserGroup = '<?php echo Configurations::getConfiguration('PEPSI_DEFAULT_USER_GROUP'); ?>';
        var defaultUser = '<?php echo Configurations::getConfiguration('PEPSI_ADMIN_USER'); ?>';
        var currentUser = '<?php echo $_SESSION['aulogin']; ?>';
        var currentUserID ='<?php echo $session->userid;?>';
    
        </script>
        <script src="js/reatlas.js"></script>
        <script src="js/reatlas-divselection.js"></script>
    </head>

    <body class="claro">
      <?php
      
if (!$session->logged_in)
    include 'login.php';

?>
        <div id="mainWindow" 
             data-dojo-type="dijit.layout.BorderContainer" 
             data-dojo-props="design:'headline', gutters:false" 
             style="width:100%; height:100%;">

            <div id="header" 
                 data-dojo-type="dijit.layout.ContentPane" data-dojo-props="region:'top'">
                <div id="headerLeft">
                    REAtlas
                    <div id="subheader">Aarhus University,Aarhus Denmark</div>
                </div>
                <div id="top-tool" >
                    <button id="cutoutselectorBtn" class="down">Cutout Selector</button>
                    <button id="capacitymapBtn">Capacity Map</button>
                </div>
                <div id="headerRight"><?php if ($session->logged_in): ?>
                    Welcome, <?php echo $profile->fullname;?><br/>
                    <a href="profile.php?ref=main">My Account</a>
                    <a href="process.php?ref=front">Logout</a>
                <?php endif; ?>
                </div>
            </div>
            <div id="mapDiv" data-dojo-type="dijit.layout.ContentPane" data-dojo-props="region:'center', splitter:false" style="width: 100%;overflow:hidden;">
                <div id="HomeButton"></div>
                <div id="LocateButton"></div>
                <?php if (SELECTION_TOOLBAR) { ?>
                    <div id="header-tool" >
                        Selection tool:
                        <button id="Point">Point</button>
                        <button id="Multipoint">Multipoint</button>
                        <button id="Line">Line</button>
                        <button id="Polyline">Polyline</button>
                        <button id="FreehandPolyline">Freehand Polyline</button>
                        <button id="Triangle">Triangle</button>
                        <button id="Rectangle">Rectangle</button>
                        <button id="Circle">Circle</button>
                        <button id="Ellipse">Ellipse</button>
                        <button id="Polygon">Polygon</button>
                        <button id="FreehandPolygon">Freehand Polygon</button> 
                        <button id="clear-graphics" class="clearall">Clear All</button>
                    </div>
                <?php } ?>
            </div>
            <div data-dojo-type="dijit.layout.ContentPane" id="rightPane" data-dojo-props="region:'right', splitter:false" style="width: 300px;overflow:hidden;">
                <div id="cutoutselectorContainer" data-dojo-type="dijit.layout.TabContainer" style="display:block;height:100%"><!-- tabPosition="left-h" tabStrip="false"-->
                    <div data-dojo-type="dijit.layout.ContentPane" data-dojo-props="title:'Cutouts', selected:true" >

                        <div id="selectorDiv">
                            <div id="selectorTopDiv">
                                <label class="blue"><input type="radio" name="cutoutSelectorGroup" value="default" checked="checked"><span>Default</span></label>
                                <label class="green"><input type="radio" name="cutoutSelectorGroup" value="own"><span>Own</span></label>
                                <label class="yellow"><input type="radio" name="cutoutSelectorGroup" value="all"><span>All</span></label>
                                <label class="purple"><input type="radio" name="cutoutSelectorGroup" value="new"><span>New</span></label>
                            </div>
                            <div id="selectorContentDiv" >
                                <div id="cutoutSelGrpDefault" >
                                    No cutout found
                                       </div>
                                <div id="cutoutSelGrpOwn" style="display: none;">Own group</div>
                                <div id="cutoutSelGrpAll" style="display: none;">All group</div>
                                <div id="cutoutSelGrpNew" style="display: none;">
                                    <label for="newcutoutname">Cutout name:</label>
                                    <input type="text" id="newcutoutname" name="newcutoutname" />
                                    <br/>
                                    <label><input type="checkbox" class="radio" name="cutoutSelTool" value="Rectangle">Rectangle</label><br/>
                                    <label><input type="checkbox" class="radio" name="cutoutSelTool" value="Multipoint">Multipoint</label><br/>
                                    <br/>
                                    <button id="clear-graphics" class="clearall" value="clear-graphics">Clear All</button>
                                    <button id="submit-graphics" class="clearall" value="submit-graphics">Submit</button>
                                </div>
                            </div>
                        </div>
                        <div id="cutoutInfoDiv">Info</div>
                    </div>

                    <div data-dojo-type="dijit.layout.ContentPane" data-dojo-props="title:'Maps',showTitle:true" >
                        <div id="basemapGallery" ></div>
                    </div>

                </div>
                <div id="capacitymapContainer" data-dojo-type="dijit.layout.TabContainer" style="display:none;height:100%"><!-- tabPosition="left-h" tabStrip="false"-->
                    <div data-dojo-type="dijit.layout.ContentPane" data-dojo-props="title:'Solar tech', selected:true" >
                        
                    </div>

                    <div data-dojo-type="dijit.layout.ContentPane" data-dojo-props="title:'Wind tech',showTitle:true" >
                        
                    </div>
                     <div data-dojo-type="dijit.layout.ContentPane" data-dojo-props="title:'Color',showTitle:true" >
                       
                    </div>
                </div>
            </div>
            <div id="footer" data-dojo-type="dijit.layout.ContentPane" data-dojo-props="region:'bottom'">
                © 2013-2014 Aarhus University - au.dk
            </div>

        </div>
        <div class="info">VERSION:</div>
    </body>

</html>
