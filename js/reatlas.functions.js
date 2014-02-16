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

dojo.require("dojox.widget.MonthAndYearlyCalendar");
dojo.require("dojox.form.DateTextBox");
dojo.require("dojox.widget.Calendar");


function toggleGraphView(hide)
    {
        if(hide){
            var hidden = $('.hidden');
            if (hidden.hasClass('visible')){
                hidden.animate({"bottom":"-251px"}, "slow").removeClass('visible');
            }
        }else {
             var hidden = $('.hidden');
            if (!hidden.hasClass('visible')){
                hidden.animate({"bottom":"0px"}, "slow").addClass('visible');
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
        if(contaxtMouseOver)
            contaxtMouseOver.remove();
        if(contaxtMouseOut)
        contaxtMouseOut.remove();

    }
}
function  initializeEvents(divID) {
    $("#" + divID + " input[name^='cutoutSelGrp']:radio").change(
            function() {

                // Enable capacity button
                $("#capacitymapBtn").removeAttr("disabled");
                selectedCutoutID = $(this).val();
                var data = $(this).val().split('/');

                require(["dojo/_base/xhr"], function(xhr) {
                    var cutoutName = data[1];
                    var userName = data[0];

                    var targetNode = dojo.byId("cutoutInfoDiv");
                    targetNode.innerHTML = "Loading...";
                    // get some data, convert to JSON
                    xhr.post({
                        url: "commands/cutoutDetails_ajax.php",
                        handleAs: "json",
                        timeout: 300000, // give up after 3 seconds
                        content: {currentUserID: currentUserID, user: userName, cutout: cutoutName}, // creates ?part=one&another=part with GET, Sent as POST data when using xhrPost
                        load: function(dataJson) {

                            if (targetNode.innerHTML === "Loading..." ||
                                    targetNode.innerHTML === "No details found")
                                targetNode.innerHTML = '';

                            if ($(dataJson).size() === 0 && targetNode.innerHTML === '') {
                                targetNode.innerHTML = 'No details found';
                                return;
                            }

                            if ($(dataJson).size() !== 0) {
                                if (dataJson.type == "Success") {
                                    var data = dataJson.summary;
                                    targetNode.innerHTML = '<h4>Name of the selected cutout:</h4>' + cutoutName + '<br/>';
                                    targetNode.innerHTML += '<h4>Type of the selected cutout:</h4>' + data.cutout_type + '<br/>';
                                    if (data.cutout_type == "Rectangle") {
                                        targetNode.innerHTML += '<h4>Coordinates:</h4>(<br/>(' + Number(data.min_latitude).toFixed(2) + ',' + Number(data.min_longitude).toFixed(2) + '),(' + Number(data.max_latitude).toFixed(2) + ',' + Number(data.max_longitude).toFixed(2) + ')<br/>)<br/>';
                                    } else if (data.cutout_type == "MultiPoint") {
                                        targetNode.innerHTML += '<h4>Points:</h4>(<br/>'
                                        for (key in data.points) {
                                            targetNode.innerHTML += '(' + Number(data.points[key].latitude).toFixed(2) + ',' + Number(data.points[key].longitude).toFixed(2) + ')';
                                        }
                                        targetNode.innerHTML += '<br/>)<br/>';
                                    }

                                    drawGraphicslayerOnMap(data);
                                } else if (dataJson.type == "Error") {
                                    targetNode.innerHTML = "<div  class=\"form-error alert alert-danger\"><h4>" + dataJson.text + "</h4><span>" + dataJson.desc + "</span></div>";
                                }
                            }
                        }
                    });
                });

            });
}
function  initializeCapacityEvents(divID) {
    $("#" + divID + "List input:radio").change(
            function() {

                var cfgName = $(this).val();

                require(["dojo/_base/xhr"], function(xhr) {

                    var targetNode = dojo.byId(divID + "InfoSubDiv");
                    var targetNode1 = dojo.byId(divID + "InfoSubDiv" + "hubHeight");
                    targetNode.innerHTML = "Loading...";
                    // get some data, convert to JSON
                    xhr.post({
                        url: "commands/capacityDetails_ajax.php",
                        handleAs: "json",
                        timeout: 300000, // give up after 3 seconds
                        content: {capacityType: divID, cfgName: cfgName, targetNode: targetNode}, // creates ?part=one&another=part with GET, Sent as POST data when using xhrPost
                        load: function(dataJSON) {

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
                                        targetNode.innerHTML = '<b>Hub height of the selected turbine:</b><br/>';
                                        $("#windhubheight").val(parseInt(data.HUB_HEIGHT));

                                        //    $("#" + targetNode.id).append("<label>height<input type=\"text\" class=\"text\" name=\"capacity"+divID+"\" value=\""+data.HUB_HEIGHT" + "</label><br/>");
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
                                    targetNode.innerHTML = "<div  class=\"form-error alert alert-danger\"><h4>" + dataJSON.text + "</h4><span>" + dataJSON.desc + "</span></div>";
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

            var esriExtent = new esri.geometry.Extent(data.min_longitude, data.min_latitude, data.max_longitude, data.max_latitude, new esri.SpatialReference({wkid: 4326}));
            _map.setExtent(esri.geometry.geographicToWebMercator(esriExtent));

            var myPolygonCenterLatLon = esriExtent.getCenter()
            zoomTo(parseFloat(myPolygonCenterLatLon.y), parseFloat(myPolygonCenterLatLon.x));
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
            zoomTo(parseFloat(tmp_pt.y), parseFloat(tmp_pt.x));
        }
        
        if(lastDrawnGraphics)
        _map.graphics.add(lastDrawnGraphics);
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
        targetNode.innerHTML = "Loading...";
        // get some data, convert to JSON
        xhr.post({
            url: "commands/cutoutlist_ajax.php",
            handleAs: "json",
            timeout: 3000, // give up after 3 seconds
            content: {currentUserID: currentUserID, user: userName}, // creates ?part=one&another=part with GET, Sent as POST data when using xhrPost
            load: function(dataJSON) {
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
                        targetNode.innerHTML = "<div  class=\"form-error alert alert-danger\"><h4>" + dataJSON.text + "</h4><span>" + dataJSON.desc + "</span></div>";
                    }
                }
            }
        });
    });
}


function submitGraphicsForMapLayerGen() {

    if (!graphicsDataForMapLayer.hasOwnProperty('geomatry_data')) {
        alert("Please draw layer before submitting");
        return false;
    }
    graphicsDataForMapLayer.cutoutName = $("#newcutoutname").val();
    graphicsDataForMapLayer.cutoutStartDate = $("#cutoutStartDate").val();
    graphicsDataForMapLayer.cutoutEndDate = $("#cutoutEndDate").val();
    console.log(graphicsDataForMapLayer)
    require(["dojo/_base/xhr"], function(xhr) {

        //   {"cutoutName":"rrg","geomatry_type":"polygon","geomatry_data":{"southwest_latitude":-72.23684375000217,"southwest_longitude":31.259294953114185,"northeast_latitude":-108.44778124999252,"northeast_longitude":47.79802337889069}}
        // get some data, convert to JSON
        xhr.post({
            url: "commands/submitGraphicsForMapLayerGen_ajax.php",
            handleAs: "json",
            timeout: 300000, // give up after 3 seconds
            content: {"cutoutName": graphicsDataForMapLayer.cutoutName, "geomatry_type": graphicsDataForMapLayer.geomatry_type, "geomatry_data": JSON.stringify(graphicsDataForMapLayer.geomatry_data), "cutoutStartDate": graphicsDataForMapLayer.cutoutStartDate, "cutoutEndDate": graphicsDataForMapLayer.cutoutEndDate}, // creates ?part=one&another=part with GET, Sent as POST data when using xhrPost
            load: function(data) {
                if (data.type == "Error")
                {
                    $("#cutoutInfoDiv").append("<div  class=\"form-error alert alert-danger\"><h4>" + data.text + "</h4><span>" + data.desc + "</span></div>");
                } else if (data.type == "Success")
                {
                    $("#cutoutInfoDiv").append("<div><h4>" + data.text + "</h4><span>" + data.desc + "</span></div>");
                }
            }
        });
    });
}


function fetchCapacityList(targetDiv) {

// Hide chart div
    toggleGraphView(true);

    require(["dojo/_base/xhr"], function(xhr) {

        var divID = targetDiv.id;
        var targetNode = dojo.byId(divID + 'SubList');
        targetNode.innerHTML = "Loading...";
        // get some data, convert to JSON
        xhr.post({
            url: "commands/listCapacity_ajax.php",
            handleAs: "json",
            timeout: 3000, // give up after 3 seconds
            content: {capacityType: divID}, // creates ?part=one&another=part with GET, Sent as POST data when using xhrPost
            load: function(dataJSON) {

                if (targetNode.innerHTML === "Loading..." ||
                        targetNode.innerHTML === "No item found")
                    targetNode.innerHTML = '';

                if ($(dataJSON).size() === 0 && targetNode.innerHTML === '') {
                    targetNode.innerHTML = 'No item found';
                    return;
                }
                if ($(dataJSON).size() !== 0) {
                    targetNode.innerHTML = '';
                    if (dataJSON.type == "Success") {
                        var data = dataJSON.data;
                        for (var i in data) {
                            $("#" + targetNode.id).append("<label><input type=\"radio\" class=\"radio\" name=\"capacity" + divID + "\" value=\"" + data[i].id + "\">" + data[i].name + "</label><br/>");

                        }
                        initializeCapacityEvents(divID);
                    } else if (dataJSON.type == "Error") {
                                    targetNode.innerHTML = "<div  class=\"form-error alert alert-danger\"><h4>" + dataJSON.text + "</h4><span>" + dataJSON.desc + "</span></div>";
                                }
                }
            }
        });
    });
}

function  fetchGridData(cutoutID) {

    var data = cutoutID.split('/');

    require(["dojo/_base/xhr"], function(xhr) {
        var cutoutName = data[1];
        var userName = data[0];

        var targetNode = dojo.byId("LayoutInfoDiv");
        targetNode.innerHTML = "Loading...";
        // get some data, convert to JSON
        xhr.post({
            url: "commands/cutoutDetails_ajax.php",
            handleAs: "json",
            timeout: 300000, // give up after 3 seconds
            content: {currentUserID: currentUserID, user: userName, cutout: cutoutName,withdata:true}, // creates ?part=one&another=part with GET, Sent as POST data when using xhrPost
            load: function(dataJson) {
                console.log(dataJson);
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
                            targetNode.innerHTML += '<h4>Coordinates:</h4>(<br/>(' + Number(dataJson.summary.min_latitude).toFixed(2) + ',' + Number(dataJson.summary.min_longitude).toFixed(2) + '),(' + Number(dataJson.summary.max_latitude).toFixed(2) + ',' + Number(dataJson.summary.max_longitude).toFixed(2) + ')<br/>)<br/>';
                        } else if (dataJson.summary.cutout_type == "MultiPoint") {
                            targetNode.innerHTML += '<h4>Points:</h4>(<br/>'
                            for (key in data.points) {
                                targetNode.innerHTML += '(' + Number(dataJson.summary.points[key].latitude).toFixed(2) + ',' + Number(dataJson.summary.points[key].longitude).toFixed(2) + ')';
                            }
                            targetNode.innerHTML += '<br/>)<br/>';
                        }

                        drawGridPointsOnMap(data);
                    } else if (dataJson.type == "Error") {
                        targetNode.innerHTML = "<div  class=\"form-error alert alert-danger\"><h4>" + dataJson.text + "</h4><span>" + dataJson.desc + "</span></div>";
                    }
                }
            }
        });
    });
}
function drawGridPointsOnMap(points)
{
    require([
        "esri/geometry/Point",
        "esri/graphic",
        "dojo/_base/Color",
        "esri/symbols/SimpleFillSymbol",
        "esri/symbols/SimpleLineSymbol",
        "esri/symbols/SimpleMarkerSymbol",
        "esri/geometry/webMercatorUtils",
        "esri/InfoTemplate",
        "dojo/domReady!"
    ], function(
            Point, 
            Graphic, Color,SimpleFillSymbol, SimpleLineSymbol, SimpleMarkerSymbol,
            webMercatorUtils,InfoTemplate

            ) {
        if (!_map || _map == 'undefined')
            return;

        _map.graphics.clear();
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
        var tmp_pt, webMercator_pt;
        for (rowpoints in points) {
            for (key in points[rowpoints]) {
                var symbol = new SimpleMarkerSymbol().setStyle("circle").setSize(7);
                tmp_pt = new Point(parseFloat(points[rowpoints][key].longitude), 
                        parseFloat(points[rowpoints][key].latitude), 
                        _map.spatialReference);
                webMercator_pt = webMercatorUtils.geographicToWebMercator(tmp_pt);
                if(points[rowpoints][key].onshore == 1){
                    console.log("Setting red");
                    symbol.setColor("red");
                }else {
                     symbol.setColor("blue");
                }
                    
               var graphic = new Graphic(webMercator_pt, symbol);
                graphic.setInfoTemplate(new InfoTemplate("Coordinates",
              "<span>LATITUDE:</span> " + points[rowpoints][key].latitude.toFixed(4) + "<br>" + 
              "<span>LONGITUDE:</span> " + points[rowpoints][key].longitude.toFixed(4) + "<br>" + 
              "<span>ONSHORE:</span> " + points[rowpoints][key].onshore + "<br>" + 
              "<span>HEIGHT:</span> " + points[rowpoints][key].height.toFixed() + "<br>" + 
              "<div id='latlong'></div>"));
              if(graphic)
                _map.graphics.add(graphic);
            }
        }

    });
}