/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var _map, tb, tbForNew, editToolbar, ctxMenuForGraphics, ctxMenuForMap;
var selected, currentLocation;
var lastCreatedGraphics;
var contaxtMouseOver, contaxtMouseOut;
var graphicsDataForMapLayer = {};
var capacityChart = null;

dojo.require("dojox.widget.MonthAndYearlyCalendar");


function activateContaxtMenuForGraphics(enable)
{
    if (enable == true) {
        contaxtMouseOver = _map.graphics.on("mouse-over", function(evt) {
            // We'll use this "selected" graphic to enable editing tools
            // on this graphic when the user click on one of the tools
            // listed in the menu.
            selected = evt.graphic;

            // Let's bind to the graphic underneath the mouse cursor           
            ctxMenuForGraphics.bindDomNode(evt.graphic.getDojoShape().getNode());
        });

        contaxtMouseOut = _map.graphics.on("mouse-out", function(evt) {
            ctxMenuForGraphics.unBindDomNode(evt.graphic.getDojoShape().getNode());
        });
    } else
    {
        contaxtMouseOver.remove();
        contaxtMouseOut.remove();

    }
}
function  initializeEvents(divID) {
    $("#" + divID + " input[name^='cutoutSelGrp']:radio").change(
            function() {
                var data = $(this).val().split('/');

                require(["dojo/_base/xhr"], function(xhr) {
                    var cutoutName = data[1];
                    var userName = data[0];

                    var targetNode = dojo.byId("cutoutInfoDiv");
                    // get some data, convert to JSON
                    xhr.post({
                        url: "commands/cutoutDetails_ajax.php",
                        handleAs: "json",
                        timeout: 300000, // give up after 3 seconds
                        content: {currentUserID:currentUserID,user: userName, cutout: cutoutName}, // creates ?part=one&another=part with GET, Sent as POST data when using xhrPost
                        load: function(data) {
                             
                            if (targetNode.innerHTML === "Loading..." ||
                                    targetNode.innerHTML === "No details found")
                                targetNode.innerHTML = '';

                            if ($(data).size() === 0 && targetNode.innerHTML === '') {
                                targetNode.innerHTML = 'No details found';
                                return;
                            }

                            if ($(data).size() !== 0) {
                                targetNode.innerHTML = '<b>Name of the selected cutout:</b><br/>' + cutoutName + '<br/>';
                                targetNode.innerHTML += '<b>Type of the selected cutout:</b><br/>' + data.cutout_type + '<br/>';
                                if (data.cutout_type == "Rectangle") {
                                    targetNode.innerHTML += '<b>Coordinates:</b><br/>(<br/>(' + Number(data.min_latitude).toFixed(2) + ',' + Number(data.min_longitude).toFixed(2) + '),(' + Number(data.max_latitude).toFixed(2) + ',' + Number(data.max_longitude).toFixed(2) + ')<br/>)<br/>';
                                } else if (data.cutout_type == "MultiPoint") {
                                    targetNode.innerHTML += '<b>Points:</b><br/>(<br/>'
                                    for (key in data.points) {
                                        targetNode.innerHTML += '(' + Number(data.points[key].latitude).toFixed(2) + ',' + Number(data.points[key].longitude).toFixed(2) + ')';
                                    }
                                    targetNode.innerHTML += '<br/>)<br/>';
                                }

                                drawGraphicslayerOnMap(data);

                            }
                        }
                    });
                });

            });
}
function  initializeCapacityEvents(divID) {
            $("#" + divID + "List input:radio").change(
            function() {
           
            var cfgName =$(this).val();
            
                require(["dojo/_base/xhr"], function(xhr) {
             
                    var targetNode = dojo.byId(divID+"InfoSubDiv");
                    // get some data, convert to JSON
                    xhr.post({
                        url: "commands/capacityDetails_ajax.php",
                        handleAs: "json",
                        timeout: 300000, // give up after 3 seconds
                        content: {capacityType: divID,cfgName: cfgName}, // creates ?part=one&another=part with GET, Sent as POST data when using xhrPost
                        load: function(data) {
                             
                            if (targetNode.innerHTML === "Loading..." ||
                                    targetNode.innerHTML === "No details found")
                                targetNode.innerHTML = '';

                            if ($(data).size() === 0 && targetNode.innerHTML === '') {
                                targetNode.innerHTML = 'No details found';
                                return;
                            }

                            if ($(data).size() !== 0) {
                               if(divID == "Wind"){
                                targetNode.innerHTML = '<b>Hub height of the selected turbine:</b><br/>' + data.HUB_HEIGHT + '<br/>';
                               
                                 $('#graphView').width($('#mapDiv').width());
                                   
                                 drawChart(data);
                                 
                                 var hidden = $('.hidden');
                                 if (hidden.hasClass('visible')){
                                     /*
                                     hidden.animate({"bottom":"-251px"}, "slow").removeClass('visible');
                                     hidden.animate({"bottom":"0px"}, "slow").addClass('visible');
                                            */
                                 } else {
                                     hidden.animate({"bottom":"0px"}, "slow").addClass('visible');
                                 }
                                 
                               }else if(divID == "Solar") {
                                   $('input[name="capacitySolarOption"]').prop('checked', false);
                                   $('#fixedOrientationGrp').hide(100);
                                   
                                 if ($('.hidden').hasClass('visible')){
                                     hidden.animate({"bottom":"-251px"}, "slow").removeClass('visible');
                                   } 
                               }
                            }
                        }
                    });
                });

            });
}

function drawChart(data)
{
   
    require(["dojox/charting/Chart",
        "dojox/charting/axis2d/Default",
        "dojox/charting/plot2d/Lines",
        "dojox/charting/Theme",
        "dojo/ready",
    "dojox/gfx/gradutils"], 
    function(Chart,Default,Lines,Theme,ready) {
 
        var gradient = Theme.generateGradient;
     /* fill settings for gradation */
    defaultFill = {type: "linear", space: "shape", x1: 0, y1: 0, x2: 0, y2: 100};
 
     var chartVelocity = data.V.split(',');
     var chartPower = data.POW.split(',');
     var chartData = [];
         for (var index=0; index<chartVelocity.length; index++)
           {
            var item = {};
            item['x']= chartVelocity[index];
            item['y']= chartPower[index];
            chartData[index]=item;
           }

    makeCharts = function(){
           /* var chartDataLocal = [   
                {x:3, y:0},
                {x:4, y:0},
                {x:4, y:0.024},
                {x:5, y:0.069},
                {x:6, y:0.133},
                {x:7, y:0.219},
                {x:8, y:0.333},
                {x:9, y:0.468},
                {x:10, y:0.598},
                {x:11, y:0.730},
                {x:12, y:0.850},
                {x:13, y:0.928},
                {x:14, y:0.973},
                {x:15, y:0.990},
                {x:16, y:0.997},
                {x:17, y:0.999},
                {x:19, y:1.0},
                {x:25, y:1.0},
                {x:25, y:0.0}
            ];*/
        
        var myTheme = new Theme({
 
        /* customize the chart wrapper */
        chart: {
            fill: "#333",
            stroke: { color: "#333" },
            pageStyle: {
                backgroundColor: "#000",
                color: "#fff"
            }
        },
 
        /* plotarea definition */
        plotarea: { fill: "#000" },
 
        /* axis definition */
        axis:{
            stroke: { // the axis itself
                color: "#fff",
                width: 1
            },
            tick: { // used as a foundation for all ticks
                color: "#fff",
                position: "center",
                font: "normal normal normal 7pt Helvetica, Arial, sans-serif",  // labels on axis
                fontColor: "#fff" // color of labels
            }
        },
 
        /* series definition */
        series: {
            stroke: { width: 2.5, color: "#fff" },
            outline: null,
            font: "normal normal normal 8pt Helvetica, Arial, sans-serif",
            fontColor: "#fff"
        },
 
        /* marker definition */
        marker: {
            stroke: { width: 1.25, color: "#fff" },
            outline: { width: 1.25, color: "#fff" },
            font: "normal normal normal 8pt Helvetica, Arial, sans-serif",
            fontColor: "#fff"
        },
 
        /* series theme with gradations! */
        //light => dark
        //defaultFill object holds all of our gradation settings
        seriesThemes: [
            { fill: gradient(defaultFill, "#fff", "#f2f2f2") },
            { fill: gradient(defaultFill, "#d5ecf3", "#bed3d9") },
            { fill: gradient(defaultFill, "#9ff275", "#7fc25d") },
            { fill: gradient(defaultFill, "#81ee3b", "#60b32b") },
            { fill: gradient(defaultFill, "#4dcff4", "#277085") },
            { fill: gradient(defaultFill, "#666", "#333") }
        ],
 
        /* marker theme */
        markerThemes: [
            {fill: "#bf9e0a", stroke: {color: "#ecc20c"}},
            {fill: "#73b086", stroke: {color: "#95e5af"}},
            {fill: "#216071", stroke: {color: "#277084"}},
            {fill: "#c7212d", stroke: {color: "#ed2835"}},
            {fill: "#87ab41", stroke: {color: "#b6e557"}}
        ]
    });
            if(!capacityChart){
                capacityChart = new Chart("capacityChart");
                capacityChart.addPlot("default", {type: "Lines"});
                capacityChart.addAxis("x",{  vertical: false, fixLower: "minor", fixUpper: "minor" });
                capacityChart.addAxis("y", { vertical: true, fixLower: "major", fixUpper: "major" });
                capacityChart.addSeries("Capacity",  chartData);
            }else
                {
                    capacityChart.updateSeries("Capacity", chartData);
                }
            capacityChart.setTheme(myTheme);
        
            capacityChart.render();
     
        };
    dojo.addOnLoad(makeCharts);
     });                                
    
}
function drawGraphicslayerOnMap(data)
{
    require([
        "esri/geometry/Point",
        "esri/geometry/Polygon",
        "esri/graphic",
        "dojo/_base/Color",
        "esri/symbols/SimpleLineSymbol",
        "esri/symbols/SimpleFillSymbol",
        "esri/symbols/SimpleMarkerSymbol",
        "esri/SpatialReference",
        "esri/geometry/webMercatorUtils",
        "dojo/domReady!"
    ], function(
            Point, Polygon,
            Graphic, Color, SimpleLineSymbol, SimpleFillSymbol, SimpleMarkerSymbol,
            SpatialReference, webMercatorUtils

            ) {
        if (!_map || _map == 'undefined')
            return;

        _map.graphics.clear();
        activateContaxtMenuForGraphics(false);

        // Adds pre-defined geometries to map
        var polygonSymbol = new SimpleFillSymbol(
                SimpleFillSymbol.STYLE_SOLID,
                new SimpleLineSymbol(
                SimpleLineSymbol.STYLE_DOT,
                new Color([151, 249, 0, .80]),
                3
                ),
                new Color([151, 249, 0, 0.45])
                );

        var wgs = new SpatialReference({
            "wkid": 4326
        });
        if (data.cutout_type == "Rectangle") {
            var latlng_max = new Point(parseFloat(data.max_longitude), parseFloat(data.max_latitude), wgs);
            var webMercator_max = webMercatorUtils.geographicToWebMercator(latlng_max);
            var latlng_min = new Point(parseFloat(data.min_longitude), parseFloat(data.min_latitude), wgs);
            var webMercator_min = webMercatorUtils.geographicToWebMercator(latlng_min);


            var rectangle = new Polygon({
                "rings": [
                    [
                        [webMercator_max.x, webMercator_min.y],
                        [webMercator_max.x, webMercator_max.y],
                        [webMercator_min.x, webMercator_max.y],
                        [webMercator_min.x, webMercator_min.y],
                        [webMercator_max.x, webMercator_min.y]
                    ]
                ],
                "spatialReference": {
                    "wkid": 102100
                }
            });

            var esriExtent = new esri.geometry.Extent(data.min_longitude, data.min_latitude, data.max_longitude, data.max_latitude, new esri.SpatialReference({wkid:4326}));
          _map.setExtent(esri.geometry.geographicToWebMercator(esriExtent));
          
            var myPolygonCenterLatLon = esriExtent.getCenter()
          zoomTo(parseFloat(myPolygonCenterLatLon.y), parseFloat(myPolygonCenterLatLon.x));
            
            _map.graphics.add(new Graphic(rectangle, polygonSymbol));
        } else if (data.cutout_type == "MultiPoint") {
            //create a random color for the symbols
            var r = Math.floor(Math.random() * 255);
            var g = Math.floor(Math.random() * 255);
            var b = Math.floor(Math.random() * 255);
            var symbol,tmp_pt,webMercator_pt;
            for (key in data.points) {

                 tmp_pt = new Point(parseFloat(data.points[key].longitude), parseFloat(data.points[key].latitude), _map.spatialReference);
                 webMercator_pt = webMercatorUtils.geographicToWebMercator(tmp_pt);
                symbol = new SimpleMarkerSymbol(
                        SimpleMarkerSymbol.STYLE_CIRCLE,
                        10, new SimpleLineSymbol(
                        SimpleLineSymbol.STYLE_SOLID,
                        new Color([r, g, b, 0.5]),
                        5
                        ),
                        new Color([r, g, b, 0.9]));

                var graphic = new Graphic(webMercator_pt, symbol);
                _map.graphics.add(graphic);
                
            }
             zoomTo(parseFloat(tmp_pt.y), parseFloat(tmp_pt.x));
        
        }
    });
}


      function zoomTo(lat, lon) {
        var point = new esri.geometry.Point(lon, lat, {
          wkid: "4326"
        });
        var wmpoint = esri.geometry.geographicToWebMercator(point);
        _map.centerAt(wmpoint);
      }
      
function fetchCutoutList(userName, divID) {

    require(["dojo/_base/xhr"], function(xhr) {

        var targetNode = dojo.byId(divID);
        // get some data, convert to JSON
        xhr.post({
            url: "commands/cutoutlist_ajax.php",
            handleAs: "json",
            timeout: 3000, // give up after 3 seconds
            content: {currentUserID:currentUserID,user: userName}, // creates ?part=one&another=part with GET, Sent as POST data when using xhrPost
            load: function(data) {
                if (targetNode.innerHTML === "Loading..." ||
                        targetNode.innerHTML === "No cutout found")
                    targetNode.innerHTML = '';

                if ($(data).size() === 0 && targetNode.innerHTML === '') {
                    targetNode.innerHTML = 'No cutout found';
                    return;
                }

                if ($(data).size() !== 0) {
                      
                    for (var i in data) {
                        if (data[i].cutout !== userName)
                            $("#" + divID).append("<label><input type=\"radio\" class=\"radio\" name=\"cutoutSelGrpDefault\" value=\"" + userName + "/" + data[i].cutout + "\">" + data[i].cutout + "</label><br/>");
                    }

                    initializeEvents(divID);
                   
                }
            }
        });
    });
}

   
function submitGraphicsForMapLayerGen() {

if(!graphicsDataForMapLayer.hasOwnProperty('geomatry_data')) {
    alert("Please draw layer before submitting");
    return false;
}
graphicsDataForMapLayer.cutoutName =$("#newcutoutname").val();
graphicsDataForMapLayer.cutoutStartDate =$("#cutoutStartDate").val();
graphicsDataForMapLayer.cutoutEndDate =$("#cutoutEndDate").val();
console.log(graphicsDataForMapLayer)
    require(["dojo/_base/xhr"], function(xhr) {
       
     //   {"cutoutName":"rrg","geomatry_type":"polygon","geomatry_data":{"southwest_latitude":-72.23684375000217,"southwest_longitude":31.259294953114185,"northeast_latitude":-108.44778124999252,"northeast_longitude":47.79802337889069}}
        // get some data, convert to JSON
        xhr.post({
            url: "commands/submitGraphicsForMapLayerGen_ajax.php",
            handleAs: "json",
            timeout: 300000, // give up after 3 seconds
            content:{"cutoutName":graphicsDataForMapLayer.cutoutName,"geomatry_type":graphicsDataForMapLayer.geomatry_type,"geomatry_data":JSON.stringify(graphicsDataForMapLayer.geomatry_data),"cutoutStartDate":graphicsDataForMapLayer.cutoutStartDate,"cutoutEndDate":graphicsDataForMapLayer.cutoutEndDate}, // creates ?part=one&another=part with GET, Sent as POST data when using xhrPost
            load: function(data) {
               
            }
        });
    });
}

     
function fetchCapacityList(targetDiv) {

    require(["dojo/_base/xhr"], function(xhr) {

        var divID = targetDiv.id;
        var targetNode = dojo.byId(divID+'SubList');
        
        console.log(targetNode.innerHTML);
        
        // get some data, convert to JSON
        xhr.post({
            url: "commands/listCapacity_ajax.php",
            handleAs: "json",
            timeout: 3000, // give up after 3 seconds
            content: {capacityType:divID}, // creates ?part=one&another=part with GET, Sent as POST data when using xhrPost
            load: function(data) {
                console.log("loaded capacity");
                if (targetNode.innerHTML === "Loading..." ||
                        targetNode.innerHTML === "No item found")
                    targetNode.innerHTML = '';

                if ($(data).size() === 0 && targetNode.innerHTML === '') {
                    targetNode.innerHTML = 'No item found';
                    return;
                }
                if ($(data).size() !== 0) {
                    targetNode.innerHTML = '';
                    for (var i in data) {
                            $("#" + targetNode.id).append("<label><input type=\"radio\" class=\"radio\" name=\"capacity"+divID+"\" value=\"" + data[i].id + "\">" + data[i].name + "</label><br/>");
                            
                    }
                  
                  initializeCapacityEvents(divID);
                }
            }
        });
    });
}

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

    _map = new Map("mapDiv", {
        center: [13.406091199999991000,52.519171000000000000],
        zoom: 3,
        basemap: "topo"
    });
    /*
     var basemapUrl = "http://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer";
     var dynamicUrl = "http://sampleserver1.arcgisonline.com/ArcGIS/rest/services/PublicSafety/PublicSafetyHazardsandRisks/MapServer";
     var referenceUrl = "http://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer";
     
     var basemap = new esri.layers.ArcGISTiledMapServiceLayer(basemapUrl);
     var dynamicLayer = new esri.layers.ArcGISDynamicMapServiceLayer(dynamicUrl,{opacity:0.45});
     var referenceLayer = new esri.layers.ArcGISTiledMapServiceLayer(referenceUrl);
     
     dojo.connect(_map, "onLayersAddResult", function(results) {
     
     });
     
     // _map.addLayer(basemap);
     // _map.addLayers([dynamicLayer,referenceLayer]);
     */
    if (showToolbar) {
        _map.on("load", initToolbar);
    }
    _map.on("load", createToolbarAndContextMenu);
    _map.on("load", initNewCutoutOptions);

    function createToolbarAndContextMenu() {
        // Add some graphics to the _map
        //  addGraphics();

        // Create and setup editing tools
        editToolbar = new Edit(_map);

        _map.on("click", function(evt) {
            editToolbar.deactivate();
        });

        createMapMenu();
        createGraphicsMenu();
    }

    function createMapMenu() {
        // Creates right-click context menu for _map
        ctxMenuForMap = new Menu({
            onOpen: function(box) {
                // Lets calculate the _map coordinates where user right clicked.
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
         _map.graphics.add(graphic);
         }
         }));
         */
        ctxMenuForMap.startup();
        ctxMenuForMap.bindDomNode(_map.container);
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
              //   if (selected.style) selected.style.cursor="url('images/zoomin.cur'),crosshair";
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
                _map.graphics.remove(selected);
            }
        }));

        ctxMenuForGraphics.startup();
        activateContaxtMenuForGraphics(true);
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

        var screenPoint = new Point(x - _map.position.x, y - _map.position.y);
        return _map.toMap(screenPoint);
    }


    // Top level toolbar
    on(dom.byId("top-tool"), "click", function(evt) {
        if (evt.target.id === "top-tool") {
            return;
        } else if (evt.target.id === "cutoutselectorBtn") {
            $(evt.target).show();
            $("#capacitymapContainer").hide();
            dijit.byId("cutoutselectorContainer").domNode.style.display = 'block';
            dijit.byId("cutoutselectorContainer").resize();
        } else if (evt.target.id === "capacitymapBtn") {
            $(evt.target).show();
            $("#cutoutselectorContainer").hide();
            dijit.byId("capacitymapContainer").domNode.style.display = 'block';
            dijit.byId("capacitymapContainer").resize();
        }

        $("#top-tool").children().each(function() {
            $(this).removeClass("down");
        });
        $(evt.target).toggleClass("down");

    });


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
        map: _map
    }, "HomeButton");
    home.startup();

    geoLocate = new LocateButton({
        map: _map
    }, "LocateButton");
    geoLocate.startup();

    //add the basemap gallery, in this case we'll display maps from ArcGIS.com including bing maps
    var basemapGallery = new esri.dijit.BasemapGallery({
        showArcGISBasemaps: true,
        map: _map
    }, "basemapGallery");
    basemapGallery.startup();
    basemapGallery.on("error", function(msg) {
       // console.log("basemap gallery error:  ", msg);
    });

    basemapGallery.on('load', function() {
        array.forEach(basemapGallery.basemaps, function(basemap) {
            if (basemap.title == "topo")
                basemapGallery.select(basemap.id);
        });
    });

    var selectedTool = null;

    function initToolbar() {
        tb = new Draw(_map);
        tb.on("draw-end", dojo.partial(addGraphic, tb));


        // event delegation so a click handler is not
        // needed for each individual button
        on(dom.byId("header-tool"), "click", function(evt) {
            if (evt.target.id === "header-tool") {
                return;
            } else if (evt.target.id === "clear-graphics") {
                _map.graphics.clear();
                return;
            }
            if (lastCreatedGraphics)
                _map.graphics.clear();

            selectedTool = evt.target;
            $(evt.target).toggleClass("down");

            var tool = evt.target.id.toLowerCase();
            _map.disableMapNavigation();
            tb.activate(tool);
        });

    }

    function initNewCutoutOptions() {
        tbForNew = new Draw(_map);
        tbForNew.on("draw-end", dojo.partial(addGraphic, tbForNew));

        // 
        // event delegation so a click handler is not
        // needed for each individual button
        on(dom.byId("cutoutSelGrpNew"), "click", function(evt) {
            if ( evt.target.value === undefined ||
                    evt.target.id === "newcutoutname" ||
                    evt.target.id === "cutoutStartDate" ||
                    evt.target.id === "cutoutEndDate") {
                return;
            }  
            
            if (evt.target.value === "clear-graphics") {
                _map.graphics.clear();
                lastCreatedGraphics = null;
                return;
            }else if (evt.target.value === "submit-graphics") {
                if($("#newcutoutname").val() === "") {
                    alert("Please provide cutout name");
                    return false;
                }
                submitGraphicsForMapLayerGen();
                return;
            }

            if (lastCreatedGraphics) {
                var didConfirm = confirm("Redraw will remove previous drawing.\nAre you sure for new selection?\n\n(You can edit existing selection using right click on selection.)");
                if (didConfirm == true) {
                    _map.graphics.clear();
                    lastCreatedGraphics = null;

                } else {
                    var group = "input:checkbox[name='cutoutSelTool']";
                    $(group).prop("checked", false);
                    return;
                }

            }
            _map.graphics.clear();
            selectedTool = evt.target;
            var tool = evt.target.value.toLowerCase();

            _map.disableMapNavigation();
            tbForNew.activate(tool);
        });

    }


    function addGraphic(parentToolbar, evt) {
        //deactivate the toolbar and clear existing graphics 
        parentToolbar.deactivate();
        _map.enableMapNavigation();
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
        _map.graphics.add(lastCreatedGraphics);
        $(selectedTool).toggleClass("down");
        var group = "input:checkbox[name='cutoutSelTool']";
        $(group).prop("checked", false);
      
        /* collect data for server request*/
        graphicsDataForMapLayer.cutoutName =$("#newcutoutname").val();
        graphicsDataForMapLayer.geomatry_type = evt.geometry.type;
        var coordinates= evt.geometry.getExtent();
        var southWest = esri.geometry.xyToLngLat(coordinates.xmax, coordinates.ymin);
        var northEast = esri.geometry.xyToLngLat(coordinates.xmin, coordinates.ymax);
        graphicsDataForMapLayer.geomatry_data = {};
        graphicsDataForMapLayer.geomatry_data['southwest_latitude']= southWest[0];
        graphicsDataForMapLayer.geomatry_data['southwest_longitude']=southWest[1];
        graphicsDataForMapLayer.geomatry_data['northeast_latitude']=northEast[0];
        graphicsDataForMapLayer.geomatry_data['northeast_longitude']=northEast[1];
      
    }
});

dojo.ready(function() {
    dojo.query(".info").attr("innerHTML", dojo.version);

});


