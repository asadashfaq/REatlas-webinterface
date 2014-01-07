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
        <link rel="stylesheet" href="http://js.arcgis.com/3.7/js/dojo/dojox/grid/resources/claroGrid.css">
        <link rel="stylesheet" href="http://js.arcgis.com/3.7/js/dojo/dojox/widget/Calendar/Calendar.css">

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
                                    <input type="text" id="newcutoutname" name="newcutoutname" data-dojo-type="dijit/form/TextBox"/>
                                    <br/>
                                    <label><input type="checkbox" class="radio" name="cutoutSelTool" value="Rectangle" data-dojo-type="dijit/form/CheckBox">Rectangle</label><br/>
                                    <label><input type="checkbox" class="radio" name="cutoutSelTool" value="Multipoint" data-dojo-type="dijit/form/CheckBox">Multipoint</label><br/>
                                    <label>Start Month-Year:</label> <input type="text" name="cutoutStartDate" id="cutoutStartDate" value="11/2013" data-dojo-type="dojox/form/DateTextBox" data-dojo-props="constraints:{datePattern: 'MM-yyyy'}, popupClass:'dojox.widget.MonthAndYearlyCalendar'" />
                                    <br/>
                                    <label>End Month-Year:</label> <input type="text" name="cutoutEndDate" id="cutoutEndDate" value="11/2013" data-dojo-type="dojox/form/DateTextBox" data-dojo-props="constraints:{datePattern: 'MM-yyyy'}, popupClass:'dojox.widget.MonthAndYearlyCalendar'" />
                                    <br/>
                                    <br/>
                                    <button id="clear-graphics" class="clearall" value="clear-graphics" data-dojo-type="dijit/form/Button">Clear All</button>
                                    <button id="submit-graphics" class="clearall" value="submit-graphics" data-dojo-type="dijit/form/Button">Submit</button>
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
                    <div data-dojo-type="dijit.layout.ContentPane" data-dojo-props="title:'Layout', selected:true" 
                         onShow="fetchCapacityList(this)" id="Layout" >
                        <div class="listContentDiv" id="LayoutList">
                            <!-- Add here extra-->
                            <div id="LayoutSubList">Loading...</div>
                        </div>
                        <div id="LayoutInfoDiv" class="capacityInfoDiv">
                            <div id="LayoutInfoSubDiv">&nbsp;</div>
                            
                        </div>
                        
                    </div>
                     
                  
                    <div data-dojo-type="dijit.layout.ContentPane" data-dojo-props="title:'Wind',showTitle:true" onShow="fetchCapacityList(this)" id="Wind" >
                        <div class="listContentDiv" id="WindList">
                            <!-- Add here extra-->
                            <div id="WindSubList">Loading...</div>
                        </div>
                        <div id="WindInfoDiv" class="capacityInfoDiv">
                            <div id="WindInfoSubDiv">
                                &nbsp;
                            </div>
                            <br/>
                         <button id="convertWind" class="clearall" value="convertWind" data-dojo-type="dijit/form/Button">Convert</button>
                        </div>
                    </div>
                      
                    
                                   
                     <div data-dojo-type="dijit.layout.ContentPane" data-dojo-props="title:'Solar',showTitle:true" onShow="fetchCapacityList(this)" id="Solar" >
                         <div class="listContentDiv" id="SolarList"> 
                             <!-- Add here extra-->
                        <div id="SolarSubList">Loading...</div>
                     </div>
                         <div id="SolarInfoDiv" class="capacityInfoDiv">
                             <div id="SolarInfoSubDiv">
                                 &nbsp;
                             </div>
                            <label><input type="radio" class="radio" name="capacitySolarOption" value="FixedOrientation" >Fixed Orientation</label><br/>
                            <div id="fixedOrientationGrp" style="display: none;">
                            <label for="solarAngle1">Angle1</label>
                            <input type="text" id="solarAngle1" class="hidden" name="enterAngle1" data-dojo-type="dijit/form/NumberTextBox"/>
                                    <br/>
                              <label for="solarAngle2">Angle2</label>
                            <input type="text" id="solarAngle2" class="hidden" name="enterAngle2" data-dojo-type="dijit/form/NumberTextBox"/>
                            </div>
                            <br/>  
                            
                            <label><input type="radio" id="fullTracking" class="radio" name="capacitySolarOption" value="FullTracking" >Full tracking</label><br/>
                             <br/>
                            <button id="convertSolar" class="clearall" value="convertSolar" data-dojo-type="dijit/form/Button">Convert</button>
                                 
                        </div>
                       
                </div>
              </div>
            </div>
            <div id="footer" data-dojo-type="dijit.layout.ContentPane" data-dojo-props="region:'bottom'">
                © 2013-2014 Aarhus University - au.dk
            </div>

        </div>
        <div id="graphView" class="hidden">
            <a href="#" id="slide" style="float: right; margin-right: 10px;clear: both;">Hide</a>
              <div id="graphViewContent">
                  POWER CURVE
                  <div id="capacityChart" style="width: 450px; height: 200px; margin: 5px auto 0px auto;"></div>
              </div>
        </div>
        <div class="info">VERSION:</div>
    </body>

</html>
