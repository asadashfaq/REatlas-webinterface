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

        <link href="css/login.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="http://js.arcgis.com/3.8/js/dojo/dijit/themes/claro/claro.css">
        <link rel="stylesheet" type="text/css" href="http://js.arcgis.com/3.7/js/esri/css/esri.css">
        <link rel="stylesheet" href="css/layout.css"/> 
        <link rel="stylesheet" href="http://js.arcgis.com/3.8/js/dojo/dojox/grid/resources/claroGrid.css">
        <link rel="stylesheet" href="http://js.arcgis.com/3.8/js/dojo/dojox/widget/Calendar/Calendar.css">
        
        <script>var dojoConfig = {parseOnLoad: true};</script>
        <script src="http://js.arcgis.com/3.8/"></script>
        <script src="js/jquery/jquery-1.10.2.min.js"></script>
        <script src="js/jquery/ui/jquery-ui.js"></script>
        <script src="js/jquery/jquery-scrollto.js"></script>
        <script src="js/jquery/jquery-timing.min.js"></script>
        <script src="js/tools.js"></script>
        
        <script>

<?php if (SELECTION_TOOLBAR) { ?>
                var showToolbar=1;
<?php } else {?>
            var showToolbar=0;
  <?php } ?>  
        var defaultUserGroup = '<?php echo Configurations::getConfiguration('PEPSI_DEFAULT_USER_GROUP'); ?>';
        var defaultUser = '<?php echo Configurations::getConfiguration('PEPSI_ADMIN_USER'); ?>';
        var currentUser = '<?php echo $session->aulogin; ?>';
        var currentUserID ='<?php echo $session->userid;?>';
        var currentUserName = '<?php echo $session->username; ?>';
        var defaultZoomLevel = '<?php echo Configurations::getConfiguration('DEFAULT_ZOOM_LEVEL'); ?>'; 
    
        </script>  
        
        <link rel="stylesheet" href="css/processing.css"/> 
        <script src="js/processing.js"></script>
        
        <script src="js/reatlas.functions.js"></script>
        <script src="js/reatlas-divselection.js"></script>
        <script src="js/reatlas.js"></script>
        
        
      
    </head>

    <body class="claro">
      <?php
      
if (!$session->logged_in) {
    header ('Location: ./');
    die();
}

?>

 <div id="processing-inAbox" class="processing">
    <!--<div class="toolbar"><a class="close" href="#"><span>x</span> close</a></div>-->
    <div class="wrapper">
        <img src="images/Earth_Rotate.gif" width="100px"/>
    </div>
 </div>
        
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
                    <span>
                        <button id="cutoutselectorBtn" class="down">Cutout Selector</button>
                        <span class="disabled-detector"></span>
                    </span>
                    <span>
                        <button id="capacitymapBtn">Capacity Map</button>
                        <span class="disabled-detector"></span>
                    </span>
                    
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
                <div id="cutoutselectorContainer" data-dojo-type="dijit.layout.TabContainer" style="display:block;height:100%"
                     ><!-- tabPosition="left-h" tabStrip="false"-->
                    <div data-dojo-type="dijit.layout.ContentPane" data-dojo-props="title:'Cutouts', selected:true" 
                         >

                        <div id="selectorDiv">
                            <div id="selectorTopDiv">
                                <div id="selectorOptionDiv">
                                    <span id="cutoutDelete" class="delete inactive" onclick="deleteCutout()"></span>
                                    <span class="refresh" onclick="reatlasFunctions.cutoutSelectorChange()"></span>
                                </div>
                                <div style="float: left">
                                    <label class="blue"><input type="radio" name="cutoutSelectorGroup" value="default" checked="checked"><span>Default</span></label>
                                    <label class="green"><input type="radio" name="cutoutSelectorGroup" value="own"><span>Own</span></label>
                                    <label class="yellow"><input type="radio" name="cutoutSelectorGroup" value="all"><span>All</span></label>
                                    <label class="purple"><input type="radio" name="cutoutSelectorGroup" value="new"><span>New</span></label>
                                </div>
                            </div>
                            <div id="selectorContentDiv" >
                                <div id="cutoutSelGrpDefault" >
                                    No cutout found
                                </div>
                                <div id="cutoutSelGrpOwn" style="display: none;" class="listContentSubDiv">Own group</div>
                                <div id="cutoutSelGrpAll" style="display: none;" class="listContentSubDiv">All group</div>
                                <div id="cutoutSelGrpNew" style="display: none;" >
                                    <div data-dojo-type="dijit/form/Form" id="myForm" data-dojo-id="myForm"
                                         encType="multipart/form-data" action="" method="">
                                    <label for="newcutoutname">Cutout name:</label><br/>
                                    <input type="text" id="newcutoutname" name="newcutoutname" data-dojo-type="dijit/form/TextBox"/>
                                    <br/>
                                    <label><input type="radio" class="radio" name="cutoutSelTool" value="Rectangle" data-dojo-type="dijit/form/RadioButton">Rectangle</label><br/>
                                    <label><input type="radio" class="radio" name="cutoutSelTool" value="Multipoint" data-dojo-type="dijit/form/RadioButton">Point(s)</label><br/>
                                    <label>Start Month-Year:</label> 
                                    <input type="text" name="cutoutStartDate" id="cutoutStartDate" 
                                           data-dojo-type="dijit/form/DateTextBox" 
                                           data-dojo-props="constraints:{datePattern: 'MM-yyyy',min:(new Date(1979, 1, 1)),max:(new Date(2013, 11, 31))}, popupClass:'dojox.widget.MonthAndYearlyCalendar'" 
                                           onChange="if(arguments !=null)dijit.byId('cutoutEndDate').constraints.min =arguments[0];else dijit.byId('cutoutEndDate').constraints.min = -infinity;" />
                                    <br/>
                                    <label>End Month-Year:</label> 
                                    <input type="text" name="cutoutEndDate" id="cutoutEndDate"
                                           data-dojo-type="dijit/form/DateTextBox" 
                                           data-dojo-props="constraints:{datePattern: 'MM-yyyy',min:(new Date(1979, 1, 1)),max:(new Date(2013, 11, 31))}, popupClass:'dojox.widget.MonthAndYearlyCalendar'" 
                                           />
                                    <br/>
                                    <br/>
                                    <button type="reset" id="clear-graphics" class="clearall" value="clear-graphics" data-dojo-type="dijit/form/Button">Clear All</button>
                                    <button id="submit-graphics" class="clearall" value="submit-graphics" data-dojo-type="dijit/form/Button">Submit</button>
                                  </div>
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
                         >
                         <!--onShow="fetchCapacityList(this)" id="Layout" -->
                         <div id="layoutDiv">
                            <div id="layoutTopDiv">
                                <span id="layoutDelete" class="delete inactive" onclick="deleteLayout()"></span>
                                <span class="refresh" onclick="fetchLayoutList(selectedCutoutID)"></span>
                            </div>
                             <div id="layoutContentDiv" >
                                <div id="layoutSelGrpOld" style="display: none;" class="layoutContentSubDiv">Old group</div>
                                <div id="layoutSelGrpNew" style="display: none;" class="layoutContentSubDiv">New group</div>
                               </div>
                         <div class="colorLayoutDiv" id="capacityLayoutTypeList">
                            <div id="capacityLayoutTopDiv">
                                <label><input type="radio" class="radio" name="layoutTypeSelect" value="OnOffshore" checked="checked" data-dojo-type="dijit/form/RadioButton">Onshore/Offshore</label><br/>
                                <label><input type="radio" class="radio" name="layoutTypeSelect" value="InstalledCapacity" data-dojo-type="dijit/form/RadioButton">Installed Capacity</label><br/>
                                 
                            </div>
                            <!--<div id="LayoutSubList">Loading...</div>-->
                        </div>
                        <div class="colorLayoutDiv" id="colorLayoutList">
                            <div id="capacityLayoutTopDiv">
                                <label>Capacity layout Name:</label>
                                <input type="text" name="layout_name" id="layout_name">
                               
                                 <br/>
                                <button id="reset" value="reset" data-dojo-type="dijit/form/Button" onclick="resetCapacityData()">Reset</button>
                                <button id="saveCapacity" value="saveCapacity" data-dojo-type="dijit/form/Button" onclick="saveCapacityData()">Save</button>
                           
                            </div>
                            <!--<div id="LayoutSubList">Loading...</div>-->
                        </div>
                            
                        <div id="LayoutInfoDiv" class="capacityInfoDiv">
                            <div id="LayoutInfoSubDiv">&nbsp;</div>
                            </div>
                        
                    </div>
                    </div>     
                     
                  
                    <div data-dojo-type="dijit.layout.ContentPane" data-dojo-props="title:'Wind',showTitle:true" onShow="fetchCapacityList(this)" id="Wind" >
                        <div id="WindList" style="height: 68%">
                            <div id="capacityWindTopDiv">
                                <label class="blue"><input type="radio" name="capacityWindType" value="onshore" ><span>Onshore</span></label>
                                <label class="green"><input type="radio" name="capacityWindType" value="offshore"><span>Offshore</span></label>
                                   
                            </div>
                            <div id="capacityWindContentDiv" >
                             </div>   
                             <br/>
                            <div class="listContentDiv"  id="WindSubList">Loading...</div>
                        </div>
                        <div id="WindInfoDiv" class="capacityInfoDiv">
                            <div id="WindInfoSubDiv">
                                &nbsp;
                            </div>
                            <!--
                             <input type="text" id="windhubheight" class="hidden" name="hubheight" data-dojo-type="dijit/form/NumberTextBox"/>
                            -->
                            <br/>
                            <label>Wind Conversion layout Name:</label>
                                 <br/>
                                <input type="text" name="windconvert_name" id="windconvert_name">
                               
                                 <br/>
                            <button id="convertWind" value="convertWind" data-dojo-type="dijit/form/Button" disabled="disabled" onclick="convertWind();">Convert</button>
                            <div id="convertWindStatus" class="roundcorner withborder"  style="float: right;background-color: #D3D3D3;display:none;"></div>
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
                            <label><input type="radio" class="radio" name="capacitySolarOption" value="FixedOrientation" >&nbsp;&nbsp;Fixed Orientation</label><br/>
                            <div id="FixedOrientationGrp" style="display: none;">
                                <label for="FixedOrientationSlope" style="text-align: right;">Slope (0 - 90)</label>
                                <input type="text" id="FixedOrientationSlope" name="FixedOrientationSlope" class="solarInput" data-dojo-type="dijit/form/NumberTextBox"/>
                              <br/><br/>
                                <label for="FixedOrientationAzimuth" style="text-align: right;">Azimuth (-180 - 180)</label>
                                <input type="text" id="FixedOrientationAzimuth" name="FixedOrientationAzimuth" class="solarInput" data-dojo-type="dijit/form/NumberTextBox"/>
                            </div>
                            <br/> 
                            <label><input type="radio" class="radio" name="capacitySolarOption" value="VerticalTracking" >&nbsp;&nbsp;Vertical Tracking</label><br/>
                            <div id="VerticalTrackingGrp" style="display: none;">
                             <label for="VerticalTrackingAzimuth" style="text-align: right;">Azimuth (-180 - 180)</label>
                            <input type="text" id="VerticalTrackingAzimuth" name="VerticalTrackingAzimuth" class="solarInput" data-dojo-type="dijit/form/NumberTextBox"/>
                            </div>
                             <br/> 
                            <label><input type="radio" class="radio" name="capacitySolarOption" value="HorizontalTracking" >&nbsp;&nbsp;Horizontal Tracking</label><br/>
                            <div id="HorizontalTrackingGrp" style="display: none;">
                               <label for="HorizontalTrackingSlope" style="text-align: right;">Slope (0 - 90)</label>
                               <input type="text" id="HorizontalTrackingSlope" name="HorizontalTrackingSlope" class="solarInput" data-dojo-type="dijit/form/NumberTextBox"/>
                            </div>
                             <br/>
                            <label><input type="radio" id="fullTracking" class="radio" name="capacitySolarOption" value="FullTracking" >&nbsp;&nbsp;Full tracking</label><br/>
                             <br/>
                              <br/>
                            <label>Solar Conversion layout Name:</label>
                                 <br/>
                                <input type="text" name="solarconvert_name" id="solarconvert_name">
                            <button id="convertSolar" class="clearall" value="convertSolar" data-dojo-type="dijit/form/Button" onclick="convertSolar();">Convert</button>
                            <div id="convertSolarStatus" class="roundcorner withborder"  style="float: right;background-color: #D3D3D3;display:none;"></div>
                        </div>
                       
                </div>
                    <div data-dojo-type="dijit.layout.ContentPane" data-dojo-props="title:'Results',showTitle:true" onShow="" id="Results" >
                         <div class="listContentDiv" id="ResultsList"> 
                             <!-- Add here extra-->
                        <div id="ResultsSubList">Loading...</div>
                     </div>
                         <div id="ResultsInfoDiv" class="capacityInfoDiv">
                             <div id="ResultsInfoSubDiv">
                                 &nbsp;
                             </div>
                            
                            <button id="download" class="clearall" value="download" data-dojo-type="dijit/form/Button" onclick="">Download</button>
                            <div id="convertSolarStatus" class="roundcorner withborder"  style="float: right;background-color: #D3D3D3;display:none;"></div>
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
                  <div id="capacityChart" style="width: 450px; height: 200px;left: 30%;position: absolute; "></div>
              </div>
        </div>
        <div class="info">VERSION:</div>
    </body>

</html>
