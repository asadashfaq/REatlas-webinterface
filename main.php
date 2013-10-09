<?php
require_once 'configurations.php';
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
        <script src="js/jquery-1.10.2.js"></script>
        <script>
<?php
if (SINGLE_SELECT) {
    echo "var singleSelect=true;\n";
} else {
    echo "var singleSelect=false;\n";
}
?>
            function fetchCutoutList(userName, divID) {
                require(["dojo/_base/xhr"], function(xhr) {
                    // get some data, convert to JSON
                    xhr.post({
                        url: "cutoutlist_ajax.php",
                        handleAs: "json",
                        timeout: 3000, // give up after 3 seconds
                        content: {user: userName}, // creates ?part=one&another=part with GET, Sent as POST data when using xhrPost
                        load: function(data) {
                            if ($("#" + divID).html() == "Loading...")
                                $("#" + divID).html('');
                            for (var i in data) {
                                 if (data[i].cutout != userName)
                                    $("#" + divID).append("<label><input type=\"radio\" class=\"radio\" name=\"cutoutSelGrpDefault\" value=\"" + data[i].cutoutId + "\">" + data[i].cutout + "</label><br/>");
                            }

                            if ($(data).size() == 0 && $("#" + divID).html() == '')
                                $("#" + divID).html('No cutout found');


                        }
                    });
                });
            }


            var map, tb, tbForNew, editToolbar, ctxMenuForGraphics, ctxMenuForMap;
            var selected, currentLocation;
            var lastCreatedGraphics;

            require([
                "esri/map",
                "esri/geometry/Point",
                "esri/geometry/Polygon",
                "esri/geometry/Extent",
                "esri/toolbars/draw",
                "esri/toolbars/edit",
                "esri/symbols/SimpleMarkerSymbol",
                "esri/symbols/SimpleLineSymbol",
                "esri/symbols/PictureFillSymbol",
                "esri/symbols/CartographicLineSymbol",
                "esri/graphic",
                "esri/geometry/jsonUtils",
                "dojo/_base/Color",
                "dojo/_base/connect",
                "dojo/dom",
                "dojo/on",
                "dojo/parser",
                "dijit/Menu",
                "dijit/MenuItem",
                "dijit/MenuSeparator",
                "dijit/registry",
                "esri/dijit/HomeButton",
                "esri/dijit/LocateButton",
                "esri/dijit/BasemapGallery",
                "dijit/form/Button",
                "dijit/layout/BorderContainer",
                "dijit/layout/ContentPane",
                "dijit/layout/TabContainer",
                "esri/dijit/Legend",
                "dojo/domReady!"
            ], function(
                    Map, Point, Polygon, Extent,
                    Draw, Edit,
                    SimpleMarkerSymbol, SimpleLineSymbol,
                    PictureFillSymbol, CartographicLineSymbol,
                    Graphic, geometryJsonUtils,
                    Color, connect, dom, on, parser, Menu, MenuItem, MenuSeparator,
                    registry, HomeButton, LocateButton, BasemapGallery
                    ) {
                // parser.parse();

                map = new Map("mapDiv", {
                    center: [-85.772, 38.255],
                    zoom: 10,
                    basemap: "topo"
                });
                /*
                 var basemapUrl = "http://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer";
                 var dynamicUrl = "http://sampleserver1.arcgisonline.com/ArcGIS/rest/services/PublicSafety/PublicSafetyHazardsandRisks/MapServer";
                 var referenceUrl = "http://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer";
             
                 var basemap = new esri.layers.ArcGISTiledMapServiceLayer(basemapUrl);
                 var dynamicLayer = new esri.layers.ArcGISDynamicMapServiceLayer(dynamicUrl,{opacity:0.45});
                 var referenceLayer = new esri.layers.ArcGISTiledMapServiceLayer(referenceUrl);
             
                 dojo.connect(map, "onLayersAddResult", function(results) {
             
                 });
             
                 // map.addLayer(basemap);
                 // map.addLayers([dynamicLayer,referenceLayer]);
                 */
<?php if (SELECTION_TOOLBAR) { ?>
                    map.on("load", initToolbar);
<?php } ?>
                map.on("load", createToolbarAndContextMenu);
                map.on("load", initNewCutoutOptions);

                function createToolbarAndContextMenu() {
                    // Add some graphics to the map
                    //  addGraphics();

                    // Create and setup editing tools
                    editToolbar = new Edit(map);

                    map.on("click", function(evt) {
                        editToolbar.deactivate();
                    });

                    createMapMenu();
                    createGraphicsMenu();
                }

                function createMapMenu() {
                    // Creates right-click context menu for map
                    ctxMenuForMap = new Menu({
                        onOpen: function(box) {
                            // Lets calculate the map coordinates where user right clicked.
                            // We'll use this to create the graphic when the user clicks
                            // on the menu item to "Add Point"
                            currentLocation = getMapPointFromMenuPosition(box);
                            editToolbar.deactivate();
                        }
                    });
                    /*
                     ctxMenuForMap.addChild(new MenuItem({ 
                     label: "Add Point",
                     onClick: function(evt) {
                     var symbol = new SimpleMarkerSymbol(
                     SimpleMarkerSymbol.STYLE_SQUARE, 
                     30, 
                     new SimpleLineSymbol(
                     SimpleLineSymbol.STYLE_SOLID, 
                     new Color([200,235, 254, 0.9]), 
                     2
                     ), new Color([200, 235, 254, 0.5]));
                     var graphic = new Graphic(geometryJsonUtils.fromJson(currentLocation.toJson()), symbol);
                     map.graphics.add(graphic);
                     }
                     }));
                     */
                    ctxMenuForMap.startup();
                    ctxMenuForMap.bindDomNode(map.container);
                }

                function createGraphicsMenu() {
                    // Creates right-click context menu for GRAPHICS
                    ctxMenuForGraphics = new Menu({});
                    /*
                     ctxMenuForGraphics.addChild(new MenuItem({ 
                     label: "Edit",
                     onClick: function() {
                     if ( selected.geometry.type !== "point" ) {
                     editToolbar.activate(Edit.EDIT_VERTICES, selected);
                     } else {
                     alert("Not implemented");
                     }
                     } 
                     }));
                     */
                    ctxMenuForGraphics.addChild(new MenuItem({
                        label: "Move",
                        onClick: function() {
                            editToolbar.activate(Edit.MOVE, selected);
                        }
                    }));

                    ctxMenuForGraphics.addChild(new MenuItem({
                        label: "Rotate/Scale",
                        onClick: function() {
                            if (selected.geometry.type !== "point") {
                                editToolbar.activate(Edit.ROTATE | Edit.SCALE, selected);
                            } else {
                                alert("Not implemented");
                            }
                        }
                    }));
                    /*
                     ctxMenuForGraphics.addChild(new MenuItem({ 
                     label: "Style",
                     onClick: function() {
                     alert("Not implemented");
                     }
                     }));
                     */
                    ctxMenuForGraphics.addChild(new MenuSeparator());
                    ctxMenuForGraphics.addChild(new MenuItem({
                        label: "Delete",
                        onClick: function() {
                            lastCreatedGraphics = null;
                            map.graphics.remove(selected);
                        }
                    }));

                    ctxMenuForGraphics.startup();

                    map.graphics.on("mouse-over", function(evt) {
                        // We'll use this "selected" graphic to enable editing tools
                        // on this graphic when the user click on one of the tools
                        // listed in the menu.
                        selected = evt.graphic;

                        // Let's bind to the graphic underneath the mouse cursor           
                        ctxMenuForGraphics.bindDomNode(evt.graphic.getDojoShape().getNode());
                    });

                    map.graphics.on("mouse-out", function(evt) {
                        ctxMenuForGraphics.unBindDomNode(evt.graphic.getDojoShape().getNode());
                    });
                }

                // Helper Methods
                function getMapPointFromMenuPosition(box) {
                    var x = box.x, y = box.y;
                    switch (box.corner) {
                        case "TR":
                            x += box.w;
                            break;
                        case "BL":
                            y += box.h;
                            break;
                        case "BR":
                            x += box.w;
                            y += box.h;
                            break;
                    }

                    var screenPoint = new Point(x - map.position.x, y - map.position.y);
                    return map.toMap(screenPoint);
                }

                // markerSymbol is used for point and multipoint, see http://raphaeljs.com/icons/#talkq for more examples
                var markerSymbol = new SimpleMarkerSymbol();
                markerSymbol.setPath("M16,4.938c-7.732,0-14,4.701-14,10.5c0,1.981,0.741,3.833,2.016,5.414L2,25.272l5.613-1.44c2.339,1.316,5.237,2.106,8.387,2.106c7.732,0,14-4.701,14-10.5S23.732,4.938,16,4.938zM16.868,21.375h-1.969v-1.889h1.969V21.375zM16.772,18.094h-1.777l-0.176-8.083h2.113L16.772,18.094z");
                markerSymbol.setColor(new Color("#00FFFF"));

                // lineSymbol used for freehand polyline, polyline and line. 
                var lineSymbol = new CartographicLineSymbol(
                        CartographicLineSymbol.STYLE_SOLID,
                        new Color([255, 0, 0]), 10,
                        CartographicLineSymbol.CAP_ROUND,
                        CartographicLineSymbol.JOIN_MITER, 5
                        );
                // fill symbol used for extent, polygon and freehand polygon, use a picture fill symbol
                // the images folder contains additional fill images, other options: sand.png, swamp.png or stiple.png
                var fillSymbol = new PictureFillSymbol(
                        "images/mangrove.png",
                        new SimpleLineSymbol(
                                SimpleLineSymbol.STYLE_SOLID,
                                new Color('#000'),
                                1
                                ),
                        42,
                        42
                        );

                var home = new HomeButton({
                    map: map
                }, "HomeButton");
                home.startup();

                geoLocate = new LocateButton({
                    map: map
                }, "LocateButton");
                geoLocate.startup();

                //add the basemap gallery, in this case we'll display maps from ArcGIS.com including bing maps
                var basemapGallery = new BasemapGallery({
                    showArcGISBasemaps: true,
                    map: map
                }, "basemapGallery");
                basemapGallery.startup();

                basemapGallery.on("error", function(msg) {
                    console.log("basemap gallery error:  ", msg);
                });

                basemapGallery.on('load', function() {
                    array.forEach(basemapGallery.basemaps, function(basemap) {
                        if (basemap.title == "topo")
                            basemapGallery.select(basemap.id);
                    });
                });

                var selectedTool = null;

                function initToolbar() {
                    tb = new Draw(map);
                    tb.on("draw-end", dojo.partial(addGraphic, tb));


                    // event delegation so a click handler is not
                    // needed for each individual button
                    on(dom.byId("header-tool"), "click", function(evt) {
                        if (evt.target.id === "header-tool") {
                            return;
                        } else if (evt.target.id === "clear-graphics") {
                            map.graphics.clear();
                            return;
                        }
                        if (lastCreatedGraphics)
                            map.graphics.clear();

                        selectedTool = evt.target;
                        $(evt.target).toggleClass("down");

                        var tool = evt.target.id.toLowerCase();
                        map.disableMapNavigation();
                        tb.activate(tool);
                    });

                }


                function initNewCutoutOptions() {
                    tbForNew = new Draw(map);
                    tbForNew.on("draw-end", dojo.partial(addGraphic, tbForNew));

                    // 
                    // event delegation so a click handler is not
                    // needed for each individual button
                    on(dom.byId("cutoutSelGrpNew"), "click", function(evt) {

                        if (evt.target.value === "undefined") {
                            return;
                        } else if (evt.target.value === "clear-graphics") {
                            map.graphics.clear();
                            lastCreatedGraphics = null;
                            return;
                        }

                        if (lastCreatedGraphics) {
                            var didConfirm = confirm("Redraw will remove previous drawing. Are you sure for new selection?");
                            if (didConfirm == true) {
                                map.graphics.clear();
                                lastCreatedGraphics = null;
                               
                            } else {
                                var group = "input:checkbox[name='cutoutSelTool']";
                                $(group).prop("checked", false);
                                return;
                            }

                        }

                        selectedTool = evt.target;
                        var tool = evt.target.value.toLowerCase();

                        map.disableMapNavigation();
                        tbForNew.activate(tool);
                    });

                }

                function addGraphic(parentToolbar, evt) {
                    //deactivate the toolbar and clear existing graphics 
                    parentToolbar.deactivate();
                    map.enableMapNavigation();
                    // figure out which symbol to use
                    var symbol;
                    if (evt.geometry.type === "point" || evt.geometry.type === "multipoint") {
                        symbol = markerSymbol;
                    } else if (evt.geometry.type === "line" || evt.geometry.type === "polyline") {
                        symbol = lineSymbol;
                    }
                    else {
                        symbol = fillSymbol;
                    }

                    lastCreatedGraphics = new Graphic(evt.geometry, symbol);

                    map.graphics.add(lastCreatedGraphics);
                    $(selectedTool).toggleClass("down");
                    var group = "input:checkbox[name='cutoutSelTool']";
                    $(group).prop("checked", false);
                }
            });

            dojo.ready(function() {
                dojo.query(".info").attr("innerHTML", dojo.version);

            });
            fetchCutoutList('<?php echo DEFAULT_USER_GROUP; ?>', 'cutoutSelGrpDefault');

        </script>
        <script type="text/javascript">
            $(document).ready(function() {
                // Handler for .ready() called.
                $('input[name="cutoutSelectorGroup"]:radio').change(
                        function() {
                           
                            if ($(this).val() == "default") {
                                $('#cutoutSelGrpDefault').css('display', 'block');
                                $('#cutoutSelGrpOwn').css('display', 'none');
                                $('#cutoutSelGrpAll').css('display', 'none');
                                $('#cutoutSelGrpNew').css('display', 'none');
                                $('#cutoutSelGrpDefault').html('Loading...');
                                fetchCutoutList('<?php echo DEFAULT_USER_GROUP; ?>', 'cutoutSelGrpDefault');
                            } else if ($(this).val() == "own") {
                                $('#cutoutSelGrpDefault').css('display', 'none');
                                $('#cutoutSelGrpOwn').css('display', 'block');
                                $('#cutoutSelGrpAll').css('display', 'none');
                                $('#cutoutSelGrpNew').css('display', 'none');
                                $('#cutoutSelGrpOwn').html('Loading...');
                                fetchCutoutList('<?php echo DEFAULT_USER; ?>', 'cutoutSelGrpOwn');
                            } else if ($(this).val() == "all") {
                                $('#cutoutSelGrpDefault').css('display', 'none');
                                $('#cutoutSelGrpOwn').css('display', 'none');
                                $('#cutoutSelGrpAll').css('display', 'block');
                                $('#cutoutSelGrpNew').css('display', 'none');
                                $('#cutoutSelGrpAll').html('Loading...');
                                fetchCutoutList('<?php echo DEFAULT_USER_GROUP; ?>', 'cutoutSelGrpAll');
                                fetchCutoutList('<?php echo DEFAULT_USER; ?>', 'cutoutSelGrpAll');
                            } else if ($(this).val() == "new") {
                                $('#cutoutSelGrpDefault').css('display', 'none');
                                $('#cutoutSelGrpOwn').css('display', 'none');
                                $('#cutoutSelGrpAll').css('display', 'none');
                                $('#cutoutSelGrpNew').css('display', 'block');
                            }
                        }
                );

                $("input:checkbox").click(function() {
                    var group = "input:checkbox[name='" + $(this).prop("name") + "']";
                    $(group).prop("checked", false);
                    $(this).prop("checked", true);
                });
                
                var group = "input:radio[name='cutoutSelectorGroup']";
                    $(group).prop("checked", false);
                    
                var group = "input:checkbox[name='cutoutSelTool']";
                    $(group).prop("checked", false);
            });
        </script>
    </head>

    <body class="claro">
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
                <div id="headerRight">Welcome, Manila<br/><a href="index.php">Logout</a></div>
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
                <div data-dojo-type="dijit.layout.TabContainer" ><!-- tabPosition="left-h" tabStrip="false"-->
                    <div data-dojo-type="dijit.layout.ContentPane" data-dojo-props="title:'Cutouts', selected:true">

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
                                    <!--label><input type="radio" class="radio" name="cutoutSelGrpDefault" value="europe">Europe</label><br/>
                                    <label><input type="radio" class="radio" name="cutoutSelGrpDefault" value="usa">USA</label><br/>
                                    <label><input type="radio" class="radio" name="cutoutSelGrpDefault" value="india">India</label><br/>
                                    <label><input type="radio" class="radio" name="cutoutSelGrpDefault" value="n-africa">North Africa</label><br/>
                                    <label><input type="radio" class="radio" name="cutoutSelGrpDefault" value="createNew">Create new</label><br/-->
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
                                </div>
                            </div>
                        </div>
                        <div id="cutoutInfoDiv">iuyoiyioyoyoi</div>
                    </div>

                    <div data-dojo-type="dijit.layout.ContentPane" data-dojo-props="title:'Maps',showTitle:true" >
                        <div id="basemapGallery" ></div>
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
