/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var _map, tb, tbForNew, editToolbar, ctxMenuForGraphics, ctxMenuForMap;
var lastDrawnGraphics;
var selected, currentLocation;
var contaxtMouseOver, contaxtMouseOut;
var graphicsDataForMapLayer = {};
var capacityChart = null;
var selectedCutoutID = null;
var currentExtentHaldle = null;
var xhr_post_loading = null;
var selectorMode = "cutout";
var convertOptionsSel = new Object();
var currentCapacityData = [];
var originalCapacityData = null;
var selectedLayout = null;
var loadedLayoutList = null;
var selectedCapacityType;
var pointForDelete = null;

dojo.require("dojox.widget.MonthAndYearlyCalendar");
dojo.require("dojox.form.DateTextBox");
dojo.require("dojox.widget.Calendar");
dojo.require("dijit.form.Button");
dojo.require("dijit.form.ValidationTextBox");
dojo.require("dijit.Tooltip");


function toggleGraphView(hide)
{
    if (hide) {
        var hidden = $('.hidden');
        if (hidden.hasClass('visible')) {
            hidden.animate({"bottom": "-251px"}, "slow").removeClass('visible');
        }
    } else {
        var hidden = $('.hidden');
        if (!hidden.hasClass('visible')) {
            hidden.animate({"bottom": "0px"}, "slow").addClass('visible');
        }
    }
}

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
        if (contaxtMouseOver)
            contaxtMouseOver.remove();
        if (contaxtMouseOut)
            contaxtMouseOut.remove();

    }
}

function fetchCutoutSummary()
{
                if (xhr_post_loading){
                    xhr_post_loading.cancel();
                    xhr_post_loading = null;
                    }
           
                // Enable capacity button
               // $("#capacitymapBtn").removeAttr("disabled");
                
                dijit.byId("capacitymapBtn").setAttribute('disabled', false);
                 
                $("#layout_name").val('');
                var data =selectedCutoutID.split('/');

                require(["dojo/request/xhr",
                    "dojo/request/notify"], function(xhr, notify) {
                    // Listen for events from request providers
                    notify("start", function() {
                        openProcessing();
                    });
                    notify("done", function(data) {
                        closeProcessing();
                    });
                    notify("stop", function() {
                        closeProcessing();
                    });
                    var cutoutName = data[1];
                    var userName = data[0];

                    var targetNode = dojo.byId("cutoutInfoDiv");
                    targetNode.innerHTML = "Loading...";
                    // get some data, convert to JSON
                    xhr("commands/cutoutDetails_ajax.php", {
                        sync: true,
                        method:"POST",
                        handleAs: "json",
                        timeout: 300000, // give up after 3 seconds
                        data: {currentUserID: currentUserID, user: userName, cutout: cutoutName}
                    }).then(function(dataJson) {

                        if (targetNode.innerHTML === "Loading..." ||
                                targetNode.innerHTML === "No details found")
                            targetNode.innerHTML = '';

                        if ($(dataJson).size() === 0 && targetNode.innerHTML === '') {
                            targetNode.innerHTML = 'No details found';
                            closeProcessing();
                            return;
                        }


                        if ($(dataJson).size() !== 0) {
                            if (dataJson.type == "Success") {
                                var data = dataJson.summary;
                                targetNode.innerHTML = '<h4>Name of the selected cutout:</h4>&nbsp;&nbsp;' + cutoutName + '<br/>';
                                targetNode.innerHTML += '<h4>Type of the selected cutout:</h4>&nbsp;&nbsp;' + data.cutout_type + '<br/>';
                                if (data.cutout_type == "Rectangle") {
                                    targetNode.innerHTML += '<h4>Coordinates:</h4>'+
                                            '&nbsp;&nbsp;Left Bottom: ' + Number(data.min_latitude).toFixed(2) + '<br/>' + 
                                            '&nbsp;&nbsp;Left Top: '+Number(data.min_longitude).toFixed(2) + '<br/>' + 
                                            '&nbsp;&nbsp;Right Bottom: '+Number(data.max_latitude).toFixed(2) + '<br/>' + 
                                            '&nbsp;&nbsp;Right Top: '+Number(data.max_longitude).toFixed(2) + '<br/>';
                                } else if (data.cutout_type == "MultiPoint") {
                                    targetNode.innerHTML += '<h4>Points:</h4>'
                                    for (key in data.points) {
                                        targetNode.innerHTML += '&nbsp;&nbsp;' + Number(data.points[key].latitude).toFixed(2) + ',' + Number(data.points[key].longitude).toFixed(2) + '<br/>';
                                    }
                                    targetNode.innerHTML += '<br/>';
                                }

                                // Draw ractangle layer on map
                                drawGraphicslayerOnMap(data);
                                   
                            } else if (dataJson.type == "Error") {
                                alert(dataJson.text + "\n" + dataJson.desc);
                                targetNode.innerHTML = "<div  class=\"form-error alert alert-danger\"><h4>" + nl2br(dataJson.text) + "</h4><span>" + nl2br(dataJson.desc) + "</span></div>";
                            }

                        }

                    });
                });
}
function  initializeEvents(divID) {
    $("#" + divID + " input[name^='cutoutSelGrp']:radio").change(
            function() {
              selectedCutoutID = $(this).val();
               fetchCutoutSummary();
              // Draw grid data and display as point
               fetchGridDataSync(selectedCutoutID, false);
               
            });
}

function generateConvertOptionHTML()
{
    /* Display conver option selected data */
    var covertOperationHtml = '<div class="bold">Selected option for convert operation:</div> <br/>';
    if (convertOptionsSel.selectedlayout)
        covertOperationHtml += '<span class="bold">Layout:</span> ' + convertOptionsSel.selectedlayout + '<br/>';
    else
        covertOperationHtml += '<span class="bold-red">Please select a layout from Layout tab.</span> ' + '<br/>';
    if (convertOptionsSel.onshoreWindName)
        covertOperationHtml += '<div class="roundcorner withborder"><span class="bold">OnShore:</span> ' + convertOptionsSel.onshoreWindName + '<br/>';
    if (convertOptionsSel.onshoreWindHubheight)
        covertOperationHtml += '<span class="bold">Hub height:</span> ' + convertOptionsSel.onshoreWindHubheight + '<br/>';
    if (convertOptionsSel.onshoreWindName)
        covertOperationHtml += '</div>';
    if (convertOptionsSel.offshoreWindName)
        covertOperationHtml += '<div class="roundcorner withborder"><span class="bold">OffShore:</span> ' + convertOptionsSel.offshoreWindName + '<br/>';
    if (convertOptionsSel.offshoreWindHubheight)
        covertOperationHtml += '<span class="bold">Hub height:</span> ' + convertOptionsSel.offshoreWindHubheight + '<br/>';
    if (convertOptionsSel.offshoreWindName)
        covertOperationHtml += '</div>';

    return covertOperationHtml;
}
function  initializeCapacityEvents(divID) {

    $("#" + divID + "SubList input:radio").change(
            function() {

                if (typeof convertOptionsSel.type === 'undefined')
                {
                    alert("Please selet turbine type OnShore/OffShore");
                    return;
                }
                /* Save Values needed for Convert operation */
                if (convertOptionsSel.type) {
                    convertOptionsSel[convertOptionsSel.type + divID + "Id"] = $(this).attr('id');
                    convertOptionsSel[convertOptionsSel.type + divID + "Val"] = $(this).val();
                    convertOptionsSel[convertOptionsSel.type + divID + "Name"] = $(this).next('label:first').html();
                }
                if (xhr_post_loading){
                    xhr_post_loading.cancel();
                  xhr_post_loading = null;
                  }
                var cfgName = $(this).val();

                require(["dojo/request",
                    "dojo/request/notify"], function(request, notify) {
                    // Listen for events from request providers
                    notify("start", function() {
                        openProcessing();
                    });
                    notify("done", function(data) {
                        closeProcessing();
                    });
                    notify("stop", function() {
                        closeProcessing();
                    });
                    var targetNode = dojo.byId(divID + "InfoSubDiv");
                    targetNode.innerHTML = "Loading...";
                    // get some data, convert to JSON
                    request.post("commands/capacityDetails_ajax.php", {
                        handleAs: "json",
                        timeout: 300000, // give up after 3 seconds
                        data: {capacityType: divID, cfgName: cfgName, targetNode: targetNode}
                    }).then(function(dataJSON) {

                        if (targetNode.innerHTML === "Loading..." ||
                                targetNode.innerHTML === "No details found")
                            targetNode.innerHTML = '';

                        if ($(dataJSON).size() === 0 && targetNode.innerHTML === '') {
                            targetNode.innerHTML = 'No details found';
                            return;
                        }

                        if ($(dataJSON).size() !== 0) {
                            if (dataJSON.type == "Success") {
                                var data = dataJSON.data;
                                if (divID == "Wind") {

                                    /* Save Values needed for Convert operation */
                                    if (convertOptionsSel.type) {
                                        convertOptionsSel[convertOptionsSel.type + divID + "Hubheight"] = data.HUB_HEIGHT;
                                    }

                                    targetNode.innerHTML = generateConvertOptionHTML();

                                    /* Enable disable convert button */
                                    if (convertOptionsSel.onshoreWindName && convertOptionsSel.offshoreWindName)
                                        dijit.byId("convertWind").setAttribute('disabled', false);
                                    else
                                        dijit.byId("convertWind").setAttribute('disabled', true);

                                    //$("#windhubheight").val(parseInt(data.HUB_HEIGHT));
                                    $('#graphView').width($('#mapDiv').width());

                                    drawChart(data);

                                    // Show chart div
                                    toggleGraphView(false);

                                } else if (divID == "Solar") {
                                    $('input[name="capacitySolarOption"]').prop('checked', false);
                                    $('#fixedOrientationGrp').hide(100);
                                    $('#verticaltrackingGrp').hide(100);
                                    $('#horizontaltrackingGrp').hide(100);

                                    // hide graph view
                                    toggleGraphView(true);
                                }
                            } else if (dataJSON.type == "Error") {
                                alert(dataJSON.text + "\n" + dataJSON.desc);
                                targetNode.innerHTML = "<div  class=\"form-error alert alert-danger\"><h4>" + nl2br(dataJSON.text) + "</h4><span>" + nl2br(dataJSON.desc) + "</span></div>";
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
            function(Chart, Default, Lines, Theme, ready) {

                var gradient = Theme.generateGradient;
                /* fill settings for gradation */
                defaultFill = {type: "linear", space: "shape", x1: 0, y1: 0, x2: 0, y2: 100};

                var chartVelocity = data.V.split(',');
                var chartPower = data.POW.split(',');
                var chartData = [];
                for (var index = 0; index < chartVelocity.length; index++)
                {
                    var item = {};
                    item['x'] = chartVelocity[index];
                    item['y'] = chartPower[index];
                    chartData[index] = item;
                }

                makeCharts = function() {
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
                            stroke: {color: "#333"},
                            pageStyle: {
                                backgroundColor: "#000",
                                color: "#fff"
                            }
                        },
                        /* plotarea definition */
                        plotarea: {fill: "#000"},
                        /* axis definition */
                        axis: {
                            stroke: {// the axis itself
                                color: "#fff",
                                width: 1
                            },
                            tick: {// used as a foundation for all ticks
                                color: "#fff",
                                position: "center",
                                font: "normal normal normal 7pt Helvetica, Arial, sans-serif", // labels on axis
                                fontColor: "#fff" // color of labels
                            }
                        },
                        /* series definition */
                        series: {
                            stroke: {width: 2.5, color: "#fff"},
                            outline: null,
                            font: "normal normal normal 8pt Helvetica, Arial, sans-serif",
                            fontColor: "#fff"
                        },
                        /* marker definition */
                        marker: {
                            stroke: {width: 1.25, color: "#fff"},
                            outline: {width: 1.25, color: "#fff"},
                            font: "normal normal normal 8pt Helvetica, Arial, sans-serif",
                            fontColor: "#fff"
                        },
                        /* series theme with gradations! */
                        //light => dark
                        //defaultFill object holds all of our gradation settings
                        seriesThemes: [
                            {fill: gradient(defaultFill, "#fff", "#f2f2f2")},
                            {fill: gradient(defaultFill, "#d5ecf3", "#bed3d9")},
                            {fill: gradient(defaultFill, "#9ff275", "#7fc25d")},
                            {fill: gradient(defaultFill, "#81ee3b", "#60b32b")},
                            {fill: gradient(defaultFill, "#4dcff4", "#277085")},
                            {fill: gradient(defaultFill, "#666", "#333")}
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
                    if (!capacityChart) {
                        capacityChart = new Chart("capacityChart");
                        capacityChart.addPlot("default", {type: Lines, hAxis: "x", vAxis: "y"});
                        capacityChart.addAxis("x", {title: "Wind speed [m/s]", titleOrientation: "away", titleFontColor: "#FFF", min: 0, max: 30, vertical: false, fixLower: "minor", fixUpper: "minor"});
                        capacityChart.addAxis("y", {title: "Power [MW]", titleFontColor: "#FFF", vertical: true, fixLower: "major", fixUpper: "major"});
                        capacityChart.addSeries("Capacity", chartData);
                    } else
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
        "esri/layers/GraphicsLayer",
        "esri/graphic",
        "dojo/_base/Color",
        "esri/symbols/SimpleLineSymbol",
        "esri/symbols/SimpleFillSymbol",
        "esri/symbols/SimpleMarkerSymbol",
        "esri/SpatialReference",
        "esri/geometry/webMercatorUtils",
        "dojo/domReady!"
    ], function(
            Point, Polygon,GraphicsLayer,
            Graphic, Color, SimpleLineSymbol, SimpleFillSymbol, SimpleMarkerSymbol,
            SpatialReference, webMercatorUtils

            ) {
        if (!_map || _map == 'undefined')
            return;

        _map.graphics.clear();
        if(_map.getLayer('rectGraphicsLayer'))
        _map.removeLayer(_map.getLayer('rectGraphicsLayer'));
        
        activateContaxtMenuForGraphics(false);

        var rectGraphicsLayer = new GraphicsLayer();
        rectGraphicsLayer.id = 'rectGraphicsLayer';               
        
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

            var esriExtent = new esri.geometry.Extent(data.min_longitude, data.min_latitude, data.max_longitude, data.max_latitude, new esri.SpatialReference({wkid: 4326}));
            _map.setExtent(esri.geometry.geographicToWebMercator(esriExtent));
           /*
            var myPolygonCenterLatLon = esriExtent.getCenter()
               zoomTo(parseFloat(myPolygonCenterLatLon.y), parseFloat(myPolygonCenterLatLon.x));
           */
            lastDrawnGraphics = new Graphic(rectangle, polygonSymbol);

        } else if (data.cutout_type == "MultiPoint") {
            //create a random color for the symbols
            var r = Math.floor(Math.random() * 255);
            var g = Math.floor(Math.random() * 255);
            var b = Math.floor(Math.random() * 255);
            var symbol, tmp_pt, webMercator_pt;
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

                lastDrawnGraphics = new Graphic(webMercator_pt, symbol);


            }
            //zoomTo(parseFloat(tmp_pt.y), parseFloat(tmp_pt.x));
        }

        if (lastDrawnGraphics){
            rectGraphicsLayer.add(lastDrawnGraphics);
            _map.addLayer(rectGraphicsLayer);
        }
        

    });
}


function zoomTo(lat, lon) {
    debugFunctionNameWithLine();
    //Set default zoom level
    _map.setLevel(defaultZoomLevel);

    var point = new esri.geometry.Point(lon, lat, {
        wkid: "4326"
    });
    var wmpoint = esri.geometry.geographicToWebMercator(point);
    _map.centerAt(wmpoint);

}

function fetchCutoutList(userName, divID) {

    require(["dojo/request",
        "dojo/request/notify"], function(request, notify) {
        // Listen for events from request providers
        notify("start", function() {
            openProcessing();
        });
        notify("done", function(data) {
            closeProcessing();
        });
        notify("stop", function() {
            closeProcessing();
        });
        var targetNode = dojo.byId(divID);
        targetNode.innerHTML = "Loading...";
        // get some data, convert to JSON
        request.post("commands/cutoutlist_ajax.php", {
            handleAs: "json",
            timeout: 3000, // give up after 3 seconds
            data: {currentUserID: currentUserID, user: userName}
        }).then(function(dataJSON) {
            if (targetNode.innerHTML === "Loading..." ||
                    targetNode.innerHTML === "No cutout found")
                targetNode.innerHTML = '';

            if ($(dataJSON).size() === 0 && targetNode.innerHTML === '') {
                targetNode.innerHTML = 'No cutout found';
                return;
            }
            if ($(dataJSON).size() !== 0) {
                if (dataJSON.type == "Success") {
                    var data = dataJSON.data;
                    for (var i in data) {
                        if (data[i].cutout !== userName)
                            $("#" + divID).append("<label><input type=\"radio\" class=\"radio\" name=\"cutoutSelGrpDefault\" value=\"" + userName + "/" + data[i].cutout + "\">" + data[i].cutout + "</label><br/>");
                    }

                    initializeEvents(divID);
                } else if (dataJSON.type == "Error") {
                    alert(dataJSON.text + "\n" + dataJSON.desc);
                    targetNode.innerHTML = "<div  class=\"form-error alert alert-danger\"><h4>" + nl2br(dataJSON.text) + "</h4><span>" + nl2br(dataJSON.desc) + "</span></div>";
                }
            }

        }
        );
    });
}


function submitGraphicsForMapLayerGen() {

    if (!graphicsDataForMapLayer.hasOwnProperty('geometry_data') &&
        !graphicsDataForMapLayer.hasOwnProperty('points')) {
        alert("Please draw layer before submitting");
        return false;
    }
    graphicsDataForMapLayer.cutoutName = $("#newcutoutname").val();
    graphicsDataForMapLayer.cutoutStartDate = $("#cutoutStartDate").val();
    graphicsDataForMapLayer.cutoutEndDate = $("#cutoutEndDate").val();

    require(["dojo/request",
        "dojo/request/notify"], function(request, notify) {
        // Listen for events from request providers
        notify("start", function() {
            openProcessing();
        });
        notify("done", function(data) {
            closeProcessing();
        });

        notify("stop", function() {
            closeProcessing();
        });
        //   {"cutoutName":"rrg","geometry_type":"polygon","geometry_data":{"southwest_latitude":-72.23684375000217,"southwest_longitude":31.259294953114185,"northeast_latitude":-108.44778124999252,"northeast_longitude":47.79802337889069}}
        // get some data, convert to JSON
        var geometry_data = null;
        
        if(graphicsDataForMapLayer.geometry_type == "rectangle"|| graphicsDataForMapLayer.geometry_type == "polygon")
            geometry_data = JSON.stringify(graphicsDataForMapLayer.geometry_data)
        else if(graphicsDataForMapLayer.geometry_type === "multipoint"
                ||graphicsDataForMapLayer.geometry_type === "point")
            geometry_data = JSON.stringify(graphicsDataForMapLayer.points)
        
        request.post("commands/submitGraphicsForMapLayerGen_ajax.php", {
            handleAs: "json",
            timeout: 300000, // give up after 3 seconds
            data: {"cutoutName": graphicsDataForMapLayer.cutoutName, "geometry_type": graphicsDataForMapLayer.geometry_type, "geometry_data": geometry_data, "cutoutStartDate": graphicsDataForMapLayer.cutoutStartDate, "cutoutEndDate": graphicsDataForMapLayer.cutoutEndDate}
        }).then(function(data) {
            if (data.type == "Error")
            {
                alert(data.text + "\n" + data.desc);
                $("#cutoutInfoDiv").append("<div  class=\"form-error alert alert-danger\"><h4>" + nl2br(data.text) + "</h4><span>" + nl2br(data.desc) + "</span></div>");
            } else if (data.type == "Success")
            {
                alert(data.text + "\n" + data.desc);
                $("#cutoutInfoDiv").append("<div><h4>" + nl2br(data.text) + "</h4><span>" + nl2br(data.desc) + "</span></div>");
            }

        });
    });
}


function fetchCapacityList(targetDiv) {

// Hide chart div
    toggleGraphView(true);
    require(["dojo/request",
        "dojo/request/notify"], function(request, notify) {
        // Listen for events from request providers
        notify("start", function() {
            openProcessing();
        });
        notify("done", function(data) {
            closeProcessing();
        });
        notify("stop", function() {
            closeProcessing();
        });
        var divID = null;
        if (typeof targetDiv == 'string' || targetDiv instanceof String)
            divID = targetDiv;
        else
            divID = targetDiv.id;

        if (divID == null)
            return;

        var targetNode = dojo.byId(divID + 'SubList');
        targetNode.innerHTML = "Loading...";
        // get some data, convert to JSON
        request.post("commands/listCapacity_ajax.php", {
            handleAs: "json",
            timeout: 3000, // give up after 3 seconds
            data: {capacityType: divID}
        }).then(function(dataJSON) {

            if (targetNode.innerHTML === "Loading..." ||
                    targetNode.innerHTML === "No item found")
                targetNode.innerHTML = '';

            if ($(dataJSON).size() === 0 && targetNode.innerHTML === '') {
                targetNode.innerHTML = 'No item found';
                closeProcessing();
                return;
            }
            if ($(dataJSON).size() !== 0) {
                targetNode.innerHTML = '';
                if (dataJSON.type == "Success") {
                    var data = dataJSON.data;
                    for (var i in data) {
                        $("#" + targetNode.id).append("<input type=\"radio\" class=\"radio\" name=\"capacity" + divID + "\" id=\"capacity" + divID + "_" + i + "\" value=\"" + data[i].id + "\"><label for=\"capacity" + divID + "_" + i + "\">" + "&nbsp&nbsp&nbsp" + data[i].name + "</label><br/>");
                        //$("#" + divID).append("<label><input type=\"radio\" class=\"radio\" name=\"cutoutSelGrpDefault\" value=\"" + userName + "/" + data[i].cutout + "\">" + data[i].cutout + "</label><br/>");
                    }
                    if (divID == "Solar") {
                        $('input[name="capacitySolar"]:radio').click(function() {
                            convertOptionsSel.panelconf = $(this).val();
                            convertOptionsSel.panelName = $(this).next('label:first').html();

                        });
                    } else {        
                        initializeCapacityEvents(divID);
                    }

                } else if (dataJSON.type == "Error") {
                    alert(dataJSON.text + "\n" + dataJSON.desc);
                    targetNode.innerHTML = "<div  class=\"form-error alert alert-danger\"><h4>" + nl2br(dataJSON.text) + "</h4><span>" + nl2br(dataJSON.desc) + "</span></div>";
                }
            }

        });
    });
}


function  fetchGridDataSync(cutoutID, clearOld) {

    // initilize default value
    clearOld = typeof clearOld !== 'undefined' ? clearOld :
            (typeof clearOld !== 'undefined' ? clearOld : true);

    if (xhr_post_loading) {
         xhr_post_loading.cancel();
         xhr_post_loading = null;
        fetchGridData(cutoutID, clearOld);
        //return;
    } else {
    //Set a zoom level in order to avoid 
    /*
    if(!savedZoomLevel)
        _map.setLevel(8);
    */
        setTimeout(function() {
            fetchGridData(cutoutID, clearOld);
        }, 2000);

    }
}

function  fetchGridData(cutoutID, clearOld) {
   
    var data = cutoutID.split('/');

    require(["dojo/request/xhr",
        "dojo/request/notify",
        "esri/geometry/webMercatorUtils"], function(xhr, notify, webMercatorUtils) {
        // Listen for events from request providers
        notify("start", function() {
            openProcessing();
        });
        notify("done", function(data) {
            closeProcessing();
        });
        notify("stop", function() {
            closeProcessing();
        });
        var cutoutName = data[1];
        var userName = data[0];

        var targetNode = dojo.byId("LayoutInfoSubDiv");
        targetNode.innerHTML = "Loading...";

        // get current display area extent latitudes and logitudes
        // To void browser hang, we are going to fetch only data in this area. 
        // Data will be fetched again when map is dragged
        var limitLongLat = webMercatorUtils.webMercatorToGeographic(_map.extent);
        var extentArr = {};
        extentArr["xmin"] = limitLongLat.xmin;
        extentArr["ymin"] = limitLongLat.ymin;
        extentArr["xmax"] = limitLongLat.xmax;
        extentArr["ymax"] = limitLongLat.ymax;

        $("#layout_name").val('');

        xhr_post_loading = xhr("commands/cutoutDetails_ajax.php", {
            sync: true,
            method:"POST",
            handleAs: "json",
            timeout: 1000000, // give up after 10 seconds
            data: {currentUserID: currentUserID, user: userName, cutout: cutoutName, withdata: true, limit: JSON.stringify(extentArr)}
        }).then(function(dataJson) {

            if (targetNode.innerHTML === "Loading..." ||
                    targetNode.innerHTML === "No details found")
                targetNode.innerHTML = '';

            if ($(dataJson).size() === 0 && targetNode.innerHTML === '') {
                targetNode.innerHTML = 'No details found';
                return;
            }


            if ($(dataJson).size() !== 0) {
                if (dataJson.type == "Success") {
                    var data = dataJson.data;
                    targetNode.innerHTML = '<h4>Name of the selected cutout:</h4>' + cutoutName + '<br/>';
                    targetNode.innerHTML += '<h4>Type of the selected cutout:</h4>' + dataJson.summary.cutout_type + '<br/>';
                    if (dataJson.summary.cutout_type == "Rectangle") {
                         targetNode.innerHTML += '<h4>Coordinates:</h4>'+
                                            '&nbsp;&nbsp;Left Bottom: ' + Number(dataJson.summary.min_latitude).toFixed(2) + '<br/>' + 
                                            '&nbsp;&nbsp;Left Top: '+Number(dataJson.summary.min_longitude).toFixed(2) + '<br/>' + 
                                            '&nbsp;&nbsp;Right Bottom: '+Number(dataJson.summary.max_latitude).toFixed(2) + '<br/>' + 
                                            '&nbsp;&nbsp;Right Top: '+Number(dataJson.summary.max_longitude).toFixed(2) + '<br/>';
                          
                    } else if (dataJson.summary.cutout_type == "MultiPoint") {
                        targetNode.innerHTML += '<h4>Points:</h4>'
                        for (key in data.points) {
                            targetNode.innerHTML += '&nbsp;&nbsp;' + Number(dataJson.summary.points[key].latitude).toFixed(2) + ',' + Number(dataJson.summary.points[key].longitude).toFixed(2) + '<br/>';
                        }
                        targetNode.innerHTML += '<br/>';
                    }

                    originalCapacityData = data;
                    var capacitySize = JSON.parse(dataJson.size);
                    
                   /* Display points if less than 585 in total */
                    if ((capacitySize.rows * capacitySize.columns) < 600){
                   
                         if (selectorMode == "capacity"){
                            if(_map.getLayer('rectGraphicsLayer')){
                                _map.removeLayer(_map.getLayer('rectGraphicsLayer'));
                           }
                        }
                        drawGridPointsOnMap(data, undefined, clearOld);
                       
                    }
                    else {
                            if(_map.getLayer('pointGraphicsLayer')){
                                _map.removeLayer(_map.getLayer('pointGraphicsLayer'));
                           }
                            if(!_map.getLayer('rectGraphicsLayer'))
                                fetchCutoutSummary();
                    }
                } else if (dataJson.type == "Error") {
                    alert(dataJson.text + "\n" + dataJson.desc);
                    targetNode.innerHTML = "<div  class=\"form-error alert alert-danger\"><h4>" + nl2br(dataJson.text) + "</h4><span>" + nl2br(dataJson.desc) + "</span></div>";
                }
            }

        });
    });
}
function drawGridPointsOnMap(points, capacityType, clearOld)
{
    // initilize default value
    capacityType = typeof capacityType !== 'undefined' ? capacityType :
            (typeof selectedCapacityType !== 'undefined' ? selectedCapacityType : "OnOffshore");

    // initilize default value
    clearOld = typeof clearOld !== 'undefined' ? clearOld :
            (typeof clearOld !== 'undefined' ? clearOld : true);

    require([
        "esri/geometry/Point",
         "esri/layers/GraphicsLayer",
        "esri/graphic",
        "esri/symbols/SimpleMarkerSymbol",
        "esri/symbols/PictureMarkerSymbol",
        "esri/geometry/webMercatorUtils",
        "esri/InfoTemplate",
        "dojo/domReady!"
    ], function(
            Point,GraphicsLayer,
            Graphic, SimpleMarkerSymbol, PictureMarkerSymbol,
            webMercatorUtils, InfoTemplate

            ) {
        if (!_map || _map == 'undefined')
            return;
        
        if (clearOld){
            if(_map.getLayer('pointGraphicsLayer')){
                _map.removeLayer(_map.getLayer('pointGraphicsLayer'));
           }
        }
            

        activateContaxtMenuForGraphics(false);

        //create a random color for the symbols
        var r = Math.floor(Math.random() * 255);
        var g = Math.floor(Math.random() * 255);
        var b = Math.floor(Math.random() * 255);
        // var symbol = new SimpleMarkerSymbol().setStyle("circle").setSize(7);
        /*  symbol = new SimpleFillSymbol(SimpleFillSymbol.STYLE_SOLID, 
         new SimpleLineSymbol(SimpleLineSymbol.STYLE_SOLID, 
         new Color([112, 112, 112]), 1), 
         new Color([136, 136, 136, 0.25]));*/
        //     symbol = new SimpleMarkerSymbol(SimpleMarkerSymbol.STYLE_CIRCLE, 10, 0xFF0000, 0.5);
        var pointGraphicsLayer = new GraphicsLayer();
        pointGraphicsLayer.id = 'pointGraphicsLayer';               
        
        var tmp_pt, webMercator_pt;
        for (rowpoints in points) {
            for (key in points[rowpoints]) {
                var symbol = new SimpleMarkerSymbol().setStyle("circle").setSize(10);
                /*  var picSymbol =  new PictureMarkerSymbol({
                 "url":"images/windmill-icon.png",
                 "height":30,
                 "width":20,
                 "type":"esriPMS",
                 "angle": -30
                 });*/
                tmp_pt = new Point(parseFloat(points[rowpoints][key].longitude),
                        parseFloat(points[rowpoints][key].latitude),
                        _map.spatialReference);
                webMercator_pt = webMercatorUtils.geographicToWebMercator(tmp_pt);

                if (capacityType == "OnOffshore") {
                    if (points[rowpoints][key].onshore == 1) {
                        symbol.setColor("red");
                        // picSymbol.setUrl("images/windmill-icon-red.png");
                    } else {
                        symbol.setColor("blue");
                        //picSymbol.setUrl("images/windmill-icon-blue.png");
                    }
                } else if (capacityType == "InstalledCapacity") {
                    if (points[rowpoints][key].capacity > 0) {
                        symbol.setColor("red");
                        // picSymbol.setUrl("images/windmill-icon-red.png");
                    } else {
                        symbol.setColor("blue");
                        //picSymbol.setUrl("images/windmill-icon-blue.png");
                    }
                }

                var wind_capacity = points[rowpoints][key].capacity;
                if (typeof currentCapacityData[rowpoints] != "undefined")
                    if (typeof currentCapacityData[rowpoints][key] != "undefined")
                        /* if(typeof currentCapacityData[rowpoints][key].capacity_wind != "undefined")*/
                        wind_capacity = currentCapacityData[rowpoints][key];
                
                if (capacityType == "InstalledCapacity") {
                    if (wind_capacity > 0) {
                        symbol.setColor("red");
                        // picSymbol.setUrl("images/windmill-icon-red.png");
                    } else {
                        symbol.setColor("blue");
                        //picSymbol.setUrl("images/windmill-icon-blue.png");
                    }
                }
                /*if(points[rowpoints][key].wind_capacity == 0){
                 symbol.setColor("green");
                 // picSymbol.setUrl("images/windmill-icon-red.png");
                 }else {
                 symbol.setColor("purple");
                 //picSymbol.setUrl("images/windmill-icon-blue.png");
                 }*/
                var graphic = new Graphic(webMercator_pt, symbol);

                graphic.setInfoTemplate(new InfoTemplate("Coordinates",
                        "<span>LATITUDE:</span> " + points[rowpoints][key].latitude.toFixed(4) + "<br>" +
                        "<span>LONGITUDE:</span> " + points[rowpoints][key].longitude.toFixed(4) + "<br>" +
                        "<span>ONSHORE:</span> " + points[rowpoints][key].onshore + "<br>" +
                        "<span>HEIGHT:</span> " + points[rowpoints][key].height.toFixed() + "<br>" +
                        "<span>INSTALLED CAPACITY:</span>" +
                        "<input type=\"text\" id=\"capacity_wind_" + rowpoints + "_" + key + "\" class=\"capacityInputNum\" " +
                        " value=\"" + parseFloat(wind_capacity).toFixed() + "\">" + "<br>" +
                        "<a href=\"javascript:changeCapacity('wind'," + rowpoints + "," + key + ");\">Save</a><br>" +
                        "<a href=\"javascript:cancelGridPointEdit();\">Cancel</a><br>" +
                        "<div id='latlong'></div>"));

                if (graphic){
                    pointGraphicsLayer.add(graphic);
                }
             
            } // End loop points in row
        } //End loop rowpoint
  
        _map.addLayer(pointGraphicsLayer);
        /*_map.reorderLayer(pointGraphicsLayer,0);*/
               

/*
        if(currentExtentHaldle)
            dojo.disconnect(currentExtentHaldle);
        */
        if(!currentExtentHaldle)
            currentExtentHaldle = dojo.connect(_map, "onExtentChange", showExtent);
        // _map.setLevel(defaultZoomLevel);

    });
}
/*_map.on("extent-change", function(){console.log(_map.getLevel());})*/

function showExtent(extent) {
    /*    var s = "";
     s = "XMin: "+ extent.xmin.toFixed(2) 
     +"YMin: " + extent.ymin.toFixed(2)
     +"XMax: " + extent.xmax.toFixed(2)
     +"YMax: " + extent.ymax.toFixed(2);
     console.log(s);
     */
    /*
    if (selectorMode == "cutout")
        return;
*/
    fetchGridDataSync(selectedCutoutID);
    /*
     require(["esri/geometry/webMercatorUtils"], 
     function(webMercatorUtils) {
     var longLat= webMercatorUtils.webMercatorToGeographic(extent);
     var s = "";
     s = "XMin: "+ longLat.xmin.toFixed(2) 
     +"YMin: " + longLat.ymin.toFixed(2)
     +"XMax: " + longLat.xmax.toFixed(2)
     +"YMax: " + longLat.ymax.toFixed(2);
     //   console.log(s);
     
     });
     */
}
function changeCapacity(type, row, col) {

    if (typeof currentCapacityData[row] == "undefined")
        currentCapacityData[row] = [];
    /*
     if(typeof currentCapacityData[row][col] == "undefined")
     currentCapacityData[row][col]={};
     */
    currentCapacityData[row][col] = $("#capacity_" + type + "_" + row + "_" + col).val();
  
    _map.infoWindow.hide();
/*
    if (currentCapacityData[row][col] > 0) {
                        symbol.setColor("red");
                        // picSymbol.setUrl("images/windmill-icon-red.png");
                    }
*/
    drawGridPointsOnMap(originalCapacityData);
    
}
function cancelGridPointEdit() {

    _map.infoWindow.hide();

}
function checkLayoutNameExists(layout_name) {
    if (loadedLayoutList == null)
        return false;

    for (var i = 0; i < loadedLayoutList.length; i++) {
        if (layout_name == loadedLayoutList[i].layout)
            return true;
    }
    return false;
}
function  saveCapacityData() {

    var textBox = dijit.byId("layout_name");
    if(!textBox.validate()){
        dijit.showTooltip(
            textBox.get('invalidMessage'), 
            textBox.domNode, 
            textBox.get('tooltipPosition'),
            !textBox.isLeftToRight()
        );        
        return;
    }
    if (currentCapacityData.length <= 0) {
        alert("Please make some changes in grid capacity");
        return;
    }
    if ($("#layout_name").val() == "") {
        alert("Please provide your layout name");
        return;
    }
    /*var str = $("#layout_name").val();
   if(/^[a-zA-Z0-9_]*$/.test(str) == false) {
    alert('Your search string contains illegal characters or space');
    return;
}*/

   /* if ($("#layout_name").val().index("") == -1) {
        alert("bad input");
        return;
    }*/
    if (selectedLayout == $("#layout_name").val() || checkLayoutNameExists($("#layout_name").val()))
    {
        if (!confirm("Saving layout with existing name \nwill overwrite existing layout.\nDo you want to continue?")) {
            return;
        }
    }
    //Set a zoom level in order to avoid 

    var data = selectedCutoutID.split('/');

    require(["dojo/request",
        "dojo/request/notify"], function(request, notify) {
        // Listen for events from request providers
        notify("start", function() {
            openProcessing();
        });
        notify("done", function(data) {
            closeProcessing();
        });
        notify("stop", function() {
            closeProcessing();
        });
        var cutoutName = data[1];
        var userName = data[0];

        var targetNode = dojo.byId("LayoutInfoSubDiv");
        targetNode.innerHTML = "Loading...";

        if (xhr_post_loading) {
            xhr_post_loading.cancel();

            //return;
        }


        request.post("commands/save_layout_ajax.php", {
            handleAs: "json",
            timeout: 300000, // give up after 3 seconds
            data: {currentUserID: currentUserID, user: userName, cutout: cutoutName, layoutName: $("#layout_name").val(), layoutdata: JSON.stringify(currentCapacityData)}
        }).then(function(dataJson) {

            if (targetNode.innerHTML === "Loading..." ||
                    targetNode.innerHTML === "No details found")
                targetNode.innerHTML = '';

            if ($(dataJson).size() === 0 && targetNode.innerHTML === '') {
                targetNode.innerHTML = 'No details found';
                return;
            }

            if ($(dataJson).size() !== 0) {
                if (dataJson.type == "Success") {
                    alert(dataJson.text + "\n" + dataJson.desc);
                    fetchLayoutList(selectedCutoutID);

                } else if (dataJson.type == "Error") {
                    alert(dataJson.text + "\n" + dataJson.desc);
                    targetNode.innerHTML = "<div  class=\"form-error alert alert-danger\"><h4>" + nl2br(dataJson.text) + "</h4><span>" + nl2br(dataJson.desc) + "</span></div>";
                }
            }

        });
    });
}


function fetchLayoutList(layoutID) {
    var data = layoutID.split('/');
    var cutoutName = data[1];
    var userName = data[0];
    var divID = "layoutSelGrpOld";
    $("#layoutDelete").addClass("inactive");
    $("#layout_name").val('');
    require(["dojo/request",
        "dojo/request/notify"], function(request, notify) {
        // Listen for events from request providers
        notify("start", function() {
            openProcessing();
        });
        notify("done", function(data) {
            closeProcessing();
        });
        notify("stop", function() {
            closeProcessing();
        });
        var targetNode = dojo.byId(divID);
        targetNode.innerHTML = "Loading...";
        // get some data, convert to JSON
        request.post("commands/layoutlist_ajax.php", {
            handleAs: "json",
            timeout: 3000, // give up after 3 seconds
            data: {currentUserID: currentUserID, cutout: cutoutName, user: userName}
        }).then(function(dataJSON) {
            if (targetNode.innerHTML === "Loading..." ||
                    targetNode.innerHTML === "No layout found")
                targetNode.innerHTML = '';

            if ($(dataJSON).size() === 0 && targetNode.innerHTML === '') {
                targetNode.innerHTML = 'No layout found';
                return;
            }
            if ($(dataJSON).size() !== 0) {
                if (dataJSON.type == "Success") {
                    var data = dataJSON.data;
                    loadedLayoutList = data;
                    for (var i in data) {
                        if (data[i].layout !== userName)
                            $("#" + divID).append("<label><input type=\"radio\" class=\"radio\" name=\"layoutSelGrpOld\" value=\"" + data[i].layout + "\">" + data[i].layout + "</label><br/>");
                    }

                    $("#" + divID).show();
                    initializelayoutSelectEvents();
                } else if (dataJSON.type == "Error") {
                    alert(dataJSON.text + "\n" + dataJSON.desc);
                    targetNode.innerHTML = "<div  class=\"form-error alert alert-danger\"><h4>" + nl2br(dataJSON.text) + "</h4><span>" + nl2br(dataJSON.desc) + "</span></div>";
                }
            } else
            {
                targetNode.innerHTML = "No layout found";
            }

        }
        );
    });
}
function  initializelayoutSelectEvents() {
    $("#layoutSelGrpOld input[name^='layoutSelGrpOld']:radio").change(
            function() {

                $("#layoutDelete").removeClass("inactive");
                if (xhr_post_loading){
                    xhr_post_loading.cancel();
                  xhr_post_loading = null;
                  }

                var data = selectedCutoutID.split('/');
                var cfgName = $(this).val();

                convertOptionsSel.selectedlayout = $(this).val();

                require(["dojo/request",
                    "dojo/request/notify"], function(request, notify) {
                    // Listen for events from request providers
                    notify("start", function() {
                        openProcessing();
                    });
                    notify("done", function(data) {
                        closeProcessing();
                    });
                    notify("stop", function() {
                        closeProcessing();
                    });
                    var cutoutName = data[1];
                    var userName = data[0];

                    var targetNode = dojo.byId("cutoutInfoDiv");
                    targetNode.innerHTML = "Loading...";
                    // get some data, convert to JSON
                    request.post("commands/layoutDetails_ajax.php", {
                        handleAs: "json",
                        timeout: 300000, // give up after 3 seconds
                        data: {currentUserID: currentUserID, user: userName, cutout: cutoutName, cfgName: cfgName}
                    }).then(function(dataJson) {

                        if (targetNode.innerHTML === "Loading..." ||
                                targetNode.innerHTML === "No details found")
                            targetNode.innerHTML = '';

                        if ($(dataJson).size() === 0 && targetNode.innerHTML === '') {
                            targetNode.innerHTML = 'No details found';
                            return;
                        }


                        if ($(dataJson).size() !== 0) {
                            if (dataJson.type == "Success") {
                                selectedLayout = cfgName;
                                $("#layout_name").val(cfgName);
                                currentCapacityData = JSON.parse(dataJson.data);
                                _map.infoWindow.hide();
                                drawGridPointsOnMap(originalCapacityData);
                                // currentCapacityData = [];
                            } else if (dataJson.type == "Error") {
                                alert(dataJson.text + "\n" + dataJson.desc);
                                targetNode.innerHTML = "<div  class=\"form-error alert alert-danger\"><h4>" + nl2br(dataJson.text) + "</h4><span>" + nl2br(dataJson.desc) + "</span></div>";
                            }

                        }

                    });
                });

            });

    $('#capacityLayoutTypeList input[name^=layoutTypeSelect]:radio').change(
            function() {
                selectedCapacityType = $(this).val();
                drawGridPointsOnMap(originalCapacityData, $(this).val());
            });
}


function  convertSolar() {

    var textBox = dijit.byId("solarconvert_name");
    if(!textBox.validate()){
        dijit.showTooltip(
            textBox.get('invalidMessage'), 
            textBox.domNode, 
            textBox.get('tooltipPosition'),
            !textBox.isLeftToRight()
        );        
        return;
    }
    
    if (!convertOptionsSel.panelconf
            || !convertOptionsSel.orientation)
    {
        alert("Please select Panel / Orientation config");
        return;
    }

    if (!convertOptionsSel.selectedlayout)
    {
        alert("Please select a layout for conversion");
        return;
    }
    var slope, azimuth;
    var orientationVal = convertOptionsSel.orientation;
    if (orientationVal == "FixedOrientation")
    {
        slope = $("#FixedOrientationSlope").val();
        azimuth = $("#FixedOrientationAzimuth").val();
    } else if (orientationVal == "VerticalTracking")
    {
        azimuth = $("#VerticalTrackingAzimuth").val();
    }
    else if (orientationVal == "HorizontalTracking")
    {
        slope = $("#HorizontalTrackingSlope").val();
    }

    var data = selectedCutoutID.split('/');

    require(["dojo/request",
        "dojo/request/notify"], function(request, notify) {
        // Listen for events from request providers
        notify("start", function() {
            openProcessing();
        });
        notify("done", function(data) {
            closeProcessing();
        });
        notify("stop", function() {
            closeProcessing();
        });
        var cutoutName = data[1];
        var userName = data[0];

        var targetNode = $('#convertSolarStatus');
        $(targetNode).hide();


        request.post("commands/convert_and_aggregate_PV.php", {
            handleAs: "json",
            timeout: 300000, // give up after 3 seconds
            data: {currentUserID: currentUserID,
                user: userName,
                cutout: cutoutName,
                conversionName: $("#solarconvert_name").val(),
                panelconf: convertOptionsSel.panelconf,
                orientation: orientationVal,
                capacitylayout: convertOptionsSel.selectedlayout,
                slope: slope,
                azimuth: azimuth
            }
        }).then(function(dataJson) {

            if ($(dataJson).size() !== 0) {
                if (dataJson.type == "Success") {
                    alert(dataJson.text + "\n" + dataJson.desc );
                    targetNode.html(nl2br(dataJson.text) + "<br/>" + nl2br(dataJson.desc) + "<br/>");
                } else if (dataJson.type == "Error") {
                    alert(dataJson.text + "\n" + dataJson.desc);
                    targetNode.html("<div  class=\"form-error alert alert-danger\"><h4>" + nl2br(dataJson.text) + "</h4><span>" + nl2br(dataJson.desc) + "</span></div>");
                }
            } else {
                alert("Technical Error.");
                targetNode.html("<div  class=\"form-error alert alert-danger\"><h4>Error:</h4><span>Technical error</span></div>");
            }
            $(targetNode).show();

        });
    });
}


function  convertWind() {

 var textBox = dijit.byId("windconvert_name");
    if(!textBox.validate()){
        dijit.showTooltip(
            textBox.get('invalidMessage'), 
            textBox.domNode, 
            textBox.get('tooltipPosition'),
            !textBox.isLeftToRight()
        );        
        return;
    }
    if (!convertOptionsSel.onshoreWindName
            || !convertOptionsSel.offshoreWindName)
    {
        alert("Please select OnShore/OffShore Wind config");
        return;
    }

    if (!convertOptionsSel.selectedlayout)
    {
        alert("Please select a layout for conversion");
        return;
    }

    var data = selectedCutoutID.split('/');

    require(["dojo/request",
        "dojo/request/notify"], function(request, notify) {
        // Listen for events from request providers
        notify("start", function() {
            openProcessing();
        });
        notify("done", function(data) {
            closeProcessing();
        });
        notify("stop", function() {
            closeProcessing();
        });
        var cutoutName = data[1];
        var userName = data[0];

        var targetNode = $('#convertWindStatus');
        $(targetNode).hide();

        request.post("commands/convert_and_aggregate_wind.php", {
            handleAs: "json",
            timeout: 300000, // give up after 3 seconds
            data: {currentUserID: currentUserID,
                user: userName,
                cutout: cutoutName,
                conversionName: $("#windconvert_name").val(),
                offshoreconfig: convertOptionsSel.offshoreWindVal,
                onshoreconfig: convertOptionsSel.onshoreWindVal,
                capacitylayout: convertOptionsSel.selectedlayout
            }
        }).then(function(dataJson) {

            if ($(dataJson).size() !== 0) {
                if (dataJson.type == "Success") {
                    alert(dataJson.text + "\n" + dataJson.desc);
                    targetNode.html(nl2br(dataJson.text) + "<br/>" + nl2br(dataJson.desc) + "<br/>");
                } else if (dataJson.type == "Error") {
                    alert(dataJson.text + "\n" + dataJson.desc);
                    targetNode.html("<div  class=\"form-error alert alert-danger\"><h4>" + nl2br(dataJson.text) + "</h4><span>" + nl2br(dataJson.desc) + "</span></div>");
                }
            } else {
                alert("Technical error.");
                targetNode.html("<div  class=\"form-error alert alert-danger\"><h4>Error:</h4><span>Technical error</span></div>");
            }
            $(targetNode).show();

        });
    });
}

function resetCapacityData()
{
    if (confirm('Are you sure you want to reset all local changes?')) {
        /* Remove all changed data in cache then redraw*/
        currentCapacityData = [];
        drawGridPointsOnMap(originalCapacityData);
        fetchLayoutList(selectedCutoutID);
    } else {
        // Do nothing!
    }

}
function deleteLayout()
{
    if (selectedLayout == null) {
        alert("Please select a layout to delete.");
        return;
    } else {
        if (confirm('Are you sure you want to delete selected layout?')) {
            var data = selectedCutoutID.split('/');
            require(["dojo/request",
                "dojo/request/notify"], function(request, notify) {
                // Listen for events from request providers
                notify("start", function() {
                    openProcessing();
                });
                notify("done", function(data) {
                    closeProcessing();
                });
                notify("stop", function() {
                    closeProcessing();
                });
                var cutoutName = data[1];
                var userName = data[0];

                $("#layout_name").val('');

                request.post("commands/layoutRemove_ajax.php", {
                    handleAs: "json",
                    timeout: 300000, // give up after 3 seconds
                    data: {user: userName, cutout: cutoutName, layoutName: selectedLayout}
                }).then(function(dataJson) {

                    if ($(dataJson).size() === 0 && targetNode.innerHTML === '') {
                        targetNode.innerHTML = 'No details found';
                        return;
                    }

                    if ($(dataJson).size() !== 0) {
                        if (dataJson.type == "Success") {
                            fetchLayoutList(selectedCutoutID);
                            alert("layout deleted");
                        } else if (dataJson.type == "Error") {
                            alert(dataJson.text + "\n" + dataJson.desc);
                        }
                    }

                });
            });
        } else {
            // Do nothing!
        }
    }
}

function toggleTopTool(val){
      
        var target = this;
        if (target.id === "cutoutselectorBtn") {
            if(selectorMode == "cutout")
                return;
            
            if(_map.getLayer('pointGraphicsLayer'))
             _map.removeLayer(_map.getLayer('pointGraphicsLayer'));
         
            toggleGraphView(true);
            $(target).show();
            $("#capacitymapContainer").hide();
            dijit.byId("cutoutselectorContainer").domNode.style.display = 'block';
            dijit.byId("cutoutselectorContainer").resize();
           
            // Restore previous graphics on map
            if(_map.getLayer('rectGraphicsLayer'))
             _map.removeLayer(_map.getLayer('rectGraphicsLayer'));
            var rectGraphicsLayer = new esri.layers.GraphicsLayer();
            rectGraphicsLayer.id = 'rectGraphicsLayer';               
            if (lastDrawnGraphics){
                rectGraphicsLayer.add(lastDrawnGraphics);
                _map.addLayer(rectGraphicsLayer);
            }
            /*
            if (lastDrawnGraphics) {
                _map.graphics.clear();
                _map.graphics.add(lastDrawnGraphics);
            }*/
            
            selectorMode = "cutout";
            fetchGridDataSync(selectedCutoutID);
        } else if (target.id === "capacitymapBtn") {
            if(selectorMode == "capacity")
                return;
            
            if(_map.getLayer('rectGraphicsLayer'))
             _map.removeLayer(_map.getLayer('rectGraphicsLayer'));
         
            toggleGraphView(true);
            $(target).show();
            $("#cutoutselectorContainer").hide();
            fetchGridDataSync(selectedCutoutID);
            
            dijit.byId("capacitymapContainer").domNode.style.display = 'block';
            dijit.byId("capacitymapContainer").resize();
            selectorMode = "capacity";
            
            fetchLayoutList(selectedCutoutID);
        }
    }
     
require(["dojo/ready", "dojo/parser", "dijit/Toolbar", "dijit/form/ToggleButton", "dojo/query", "dojo/dom-class", "dojo/on", "dojo/domReady!"], function (ready, parser, ToolBar, ToggleButton, query, domClass, on) {
    ready(function() {
        on(query(".dijitToggleButton"), "click", function (e) {
            query(".dijitToggleButton").forEach(function (node) {
               domClass.remove(node, "dijitToggleButtonChecked dijitToggleButtonRtlChecked dijitRtlChecked dijitChecked button_down");
            });
            domClass.add(this, "dijitToggleButtonChecked dijitToggleButtonRtlChecked dijitRtlChecked dijitChecked button_down");
        });
    }); 
});

function triggerOnClick(targetID){
    var target = dojo.byId(targetID);
    
    // IE does things differently
    if (dojo.isIE)
    {
        target.fireEvent("onclick");
    }
    else
    { // Not IE
        var event = document.createEvent("HTMLEvents");
        event.initEvent("click", false, true);
        target.dispatchEvent(event);
    }
}

function downloadSelectedJob(){
    var selectedJob=$('input[name="joblist_conversion"]:radio:checked');
    var selectedJobId =selectedJob.attr('id').split('_')[2];
    var selectedJobName = selectedJob.val();
    var downloadtype = $('input[name="downloadtype"]:radio:checked').val();
    $("#secretDownloadIFrame").attr("src","commands/getresult.php?job_id="+selectedJobId+"&job_name="+selectedJobName+"&downloadtype="+downloadtype);
}