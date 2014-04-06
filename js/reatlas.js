/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


require([
    "esri/map",
    "esri/geometry/Point",
    "esri/toolbars/draw",
    "esri/toolbars/edit",
    "esri/symbols/SimpleMarkerSymbol",
    "esri/symbols/SimpleLineSymbol",
    "esri/symbols/PictureFillSymbol",
    "esri/symbols/CartographicLineSymbol",
    "esri/graphic",
    "dojo/_base/Color",
    "dojo/dom",
    "dojo/on",
    "dojo/_base/event",
    "dijit/Menu",
    "dijit/MenuItem",
    "dijit/MenuSeparator",
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
        Map, Point,
        Draw, Edit,
        SimpleMarkerSymbol, SimpleLineSymbol,
        PictureFillSymbol, CartographicLineSymbol,
        Graphic,
        Color, dom, on, Event, Menu, MenuItem, MenuSeparator,
        HomeButton, LocateButton, BasemapGallery
        ) {

    var reatlasFunctions = dojo.getObject('reatlasFunctions', true);
            
    _map = new Map("mapDiv", {
        center: [13.406091199999991000, 52.519171000000000000],
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
    // _map.on("load", createToolbarAndContextMenu);
    // _map.on("load", initNewCutoutOptions);

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
                editToolbar.on("graphic-move-stop", dojo.partial(refreshCutoutData, editToolbar));
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
                lastDrawnGraphics = null;
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
        $("#top-tool").find(".down").each(function() {
            $(this).removeClass("down");
        });

        if (evt.target.id === "top-tool") {
            return;
        } else if (evt.target.id === "cutoutselectorBtn") {
            if(_map.getLayer('pointGraphicsLayer'))
             _map.removeLayer(_map.getLayer('pointGraphicsLayer'));
            toggleGraphView(true);
            $(evt.target).show();
            $("#capacitymapContainer").hide();
            dijit.byId("cutoutselectorContainer").domNode.style.display = 'block';
            dijit.byId("cutoutselectorContainer").resize();
            $(evt.target).toggleClass("down");
            // Restore previous graphics on map
            if (lastDrawnGraphics)
                _map.graphics.add(lastDrawnGraphics);
            selectorMode = "cutout";
        } else if (evt.target.id === "capacitymapBtn") {
            if(_map.getLayer('rectGraphicsLayer'))
             _map.removeLayer(_map.getLayer('rectGraphicsLayer'));
            toggleGraphView(true);
            $(evt.target).show();
            $("#cutoutselectorContainer").hide();
            fetchGridDataSync(selectedCutoutID);
            dijit.byId("capacitymapContainer").domNode.style.display = 'block';
            dijit.byId("capacitymapContainer").resize();
            $(evt.target).toggleClass("down");
            selectorMode = "capacity";
            fetchLayoutList(selectedCutoutID);
        }

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
            if (lastDrawnGraphics)
                _map.graphics.clear();

            selectedTool = evt.target;
            $(evt.target).toggleClass("down");

            var tool = evt.target.id.toLowerCase();
            _map.disableMapNavigation();
            tb.activate(tool);
        });

    }

    function initNewCutoutOptions() {
       
        if (lastDrawnGraphics) {
            _map.graphics.clear();
            lastDrawnGraphics = null;
        }

        tbForNew = new Draw(_map);
        tbForNew.on("draw-end", dojo.partial(addGraphic, tbForNew));

        // 
        // event delegation so a click handler is not
        // needed for each individual button
        on(dom.byId("cutoutSelGrpNew"), "click", function(evt) {
            if (evt.target.value === undefined ||
                    evt.target.id === "newcutoutname" ||
                    evt.target.id === "cutoutStartDate" ||
                    evt.target.id === "cutoutEndDate") {
                return;
            }

            if (evt.target.value === "clear-graphics") {
                if (lastDrawnGraphics) {
                    var didConfirm = confirm("Clear will remove previous drawing.\nAre you sure for new selection?\n\n(You can edit existing selection using right click on selection.)");
                    if (didConfirm == true) {
                        $("#cutoutInfoDiv").html(""); // clear new cutout details
                        $("#cutoutStartDate").val("");                         
                        _map.graphics.clear();
                        lastDrawnGraphics = null;
                    } else {
                        Event.stop(evt);
                        return false;
                    }
                } else {
                    $("#cutoutInfoDiv").html(""); // clear new cutout details
                    $("#cutoutStartDate").val("");
                    _map.graphics.clear();
                    lastDrawnGraphics = null;
                }
                return;
            } else if (evt.target.value === "submit-graphics") {
                if ($("#newcutoutname").val() === "") {
                    alert("Please provide cutout name");
                    return false;
                }
                submitGraphicsForMapLayerGen();
                return;
            }

            if (lastDrawnGraphics) {
                var didConfirm = confirm("Redraw will remove previous drawing.\nAre you sure for new selection?\n\n(You can edit existing selection using right click on selection.)");
                if (didConfirm == true) {
                    _map.graphics.clear();
                    lastDrawnGraphics = null;

                } else {
                    var group = "input:checkbox[name='cutoutSelTool']";
                    $(group).prop("checked", false);
                    return;
                }

            }

            _map.graphics.clear();
            selectedTool = evt.target;
            var tool = evt.target.value.toLowerCase();
            
            if(tool == "rectangle" || tool == "polygon") { 
                // disable map movement only for rectangle 
                // otherwise drawing will be impossible
                _map.disableMapNavigation(); 
            }
            
            tbForNew.activate(tool);
        });

    }

    function newCutoutDetails(cutoutData)
    {
        
        var cutout_type = $("input:radio[name='cutoutSelTool']:checked").val();
        var infoDetails = "<div>";
        infoDetails = '<h4>Name of the selected cutout:</h4>' + cutoutData.cutoutName + '<br/>';
        infoDetails += '<h4>Type of the selected cutout:</h4>' + cutout_type + '<br/>';
        if (cutout_type == "Rectangle") {
            infoDetails += '<h4>Coordinates:</h4>'
                    +'&nbsp;&nbsp;Left Bottom: ' + Number(cutoutData.geometry_data['southwest_latitude']).toFixed(2) + '<br/>'
                    + '&nbsp;&nbsp;Left Top: '+Number(cutoutData.geometry_data['southwest_longitude']).toFixed(2) + '<br/>'
                    + '&nbsp;&nbsp;Right Bottom: '+Number(cutoutData.geometry_data['northeast_latitude']).toFixed(2) + '<br/>'
                    +'&nbsp;&nbsp;Right Top: '+ Number(cutoutData.geometry_data['northeast_longitude']).toFixed(2) + '<br/>';
                            
        } else if (cutout_type == "Multipoint") {
            
            infoDetails += '<h4>Points:</h4>'
            for (key in cutoutData.points) {
                infoDetails += '&nbsp;&nbsp;(' + Number(cutoutData.points[key][0]).toFixed(2) + ', ' + Number(cutoutData.points[key][1]).toFixed(2) + ')<br/>';
            }            
            infoDetails += '<br/>';
        }
        infoDetails += "</div>";
        $("#cutoutInfoDiv").html(infoDetails);
    }

    function refreshCutoutData(currentAction,evt)
    {
    /* collect data for server request*/
        graphicsDataForMapLayer.cutoutName = $("#newcutoutname").val();
        graphicsDataForMapLayer.geometry_type = evt.graphic.geometry.type;
         var cutout_type = $("input:radio[name='cutoutSelTool']:checked").val();
        if (cutout_type == "Rectangle") {
            var coordinates = evt.graphic.geometry.getExtent();

            var southWest = esri.geometry.xyToLngLat(coordinates.xmin, coordinates.ymin);
            var northEast = esri.geometry.xyToLngLat(coordinates.xmax, coordinates.ymax);
            graphicsDataForMapLayer.geometry_data = {};
            graphicsDataForMapLayer.geometry_data['southwest_latitude'] = southWest[1];
            graphicsDataForMapLayer.geometry_data['southwest_longitude'] = southWest[0];
            graphicsDataForMapLayer.geometry_data['northeast_latitude'] = northEast[1];
            graphicsDataForMapLayer.geometry_data['northeast_longitude'] = northEast[0];
        }else  if (cutout_type == "Multipoint") {
            if(currentAction == "vertex-delete"){
                if(evt.vertexinfo.graphic.geometry.type == "point"){
                 var point_tmp = pointForDelete?pointForDelete:evt.vertexinfo.graphic.geometry;
                 var point =new esri.geometry.Point([point_tmp.x,point_tmp.y],new esri.SpatialReference({ wkid:4326 }));
                 var pointLatLang = esri.geometry.xyToLngLat(point.x,point.y);
                 if (graphicsDataForMapLayer.points.contains([pointLatLang[0],pointLatLang[1]]))
                  graphicsDataForMapLayer.points.remove([pointLatLang[0],pointLatLang[1]]);
                }

                }else{
                    for (var itemidx in evt.graphic.geometry.points){
                        var point = new esri.geometry.Point([evt.graphic.geometry.points[itemidx][0],evt.graphic.geometry.points[itemidx][1]],new esri.SpatialReference({ wkid:4326 }));
                        var pointLatLang = esri.geometry.xyToLngLat(point.x,point.y);
                        if (!graphicsDataForMapLayer.points.hasOwnProperty(pointLatLang))
                          graphicsDataForMapLayer.points.push(pointLatLang);
                        }
              }

        }
        newCutoutDetails(graphicsDataForMapLayer);
    }
    function addGraphic(parentToolbar, evt) {

        // figure out which symbol to use
        var symbol;
        if (evt.geometry.type === "point" || evt.geometry.type === "multipoint") {
            symbol = markerSymbol;
        } else if (evt.geometry.type === "line" || evt.geometry.type === "polyline") {
            symbol = lineSymbol;
        }
        else {
            //deactivate the toolbar and clear existing graphics 
           parentToolbar.deactivate();
           _map.enableMapNavigation();
            symbol = fillSymbol;
        }

        lastDrawnGraphics = new Graphic(evt.geometry, symbol);
        _map.graphics.add(lastDrawnGraphics);

        editToolbar = new Edit(_map);
        if (lastDrawnGraphics.geometry.type === "multipoint" ||
                lastDrawnGraphics.geometry.type === "point") {
           editToolbar.activate(Edit.EDIT_VERTICES, lastDrawnGraphics);
          //  editToolbar.on("vertex-move-stop", dojo.partial(refreshCutoutData, editToolbar));
          editToolbar.on("vertex-delete",  dojo.partial(refreshCutoutData,"vertex-delete"));
          editToolbar.on("vertex-first-move",  function(evt){ pointForDelete = evt.vertexinfo.graphic.geometry;});
        
        } else if (lastDrawnGraphics.geometry.type === "rectangle" ||
                lastDrawnGraphics.geometry.type === "polygon"){
            //editToolbar.activate(Edit.MOVE |Edit.ROTATE | Edit.SCALE, lastDrawnGraphics);
            editToolbar.activate(Edit.MOVE | Edit.SCALE, lastDrawnGraphics);
            //editToolbar.on("rotate-stop", dojo.partial(refreshCutoutData, editToolbar));
            editToolbar.on("scale-stop",  dojo.partial(refreshCutoutData, "scale-stop"));
            editToolbar.on("graphic-move-stop",  dojo.partial(refreshCutoutData, "graphic-move-stop"));

        }

        $(selectedTool).toggleClass("down");
        var group = "input:checkbox[name='cutoutSelTool']";
        $(group).prop("checked", false);

        /* collect data for server request*/
        graphicsDataForMapLayer.cutoutName = $("#newcutoutname").val();
        graphicsDataForMapLayer.geometry_type = evt.geometry.type;
        
        if (lastDrawnGraphics.geometry.type === "multipoint" ||
                lastDrawnGraphics.geometry.type === "point") {
            
            if(!graphicsDataForMapLayer.points)
                graphicsDataForMapLayer.points = [];

            for (var itemidx in evt.geometry.points){
                var point = new esri.geometry.Point([evt.geometry.points[itemidx][0],evt.geometry.points[itemidx][1]],new esri.SpatialReference({ wkid:4326 }));
                var pointLatLang = esri.geometry.xyToLngLat(point.x,point.y);
                if (!graphicsDataForMapLayer.points.hasOwnProperty(pointLatLang))
                  graphicsDataForMapLayer.points.push(pointLatLang);
              }
              

        } else if (lastDrawnGraphics.geometry.type === "rectangle" ||
                lastDrawnGraphics.geometry.type === "polygon"){
            var coordinates = evt.geometry.getExtent();
            var southWest = esri.geometry.xyToLngLat(coordinates.xmin, coordinates.ymin);
            var northEast = esri.geometry.xyToLngLat(coordinates.xmax, coordinates.ymax);
            graphicsDataForMapLayer.geometry_data = {};
            graphicsDataForMapLayer.geometry_data['southwest_latitude'] = southWest[1];
            graphicsDataForMapLayer.geometry_data['southwest_longitude'] = southWest[0];
            graphicsDataForMapLayer.geometry_data['northeast_latitude'] = northEast[1];
            graphicsDataForMapLayer.geometry_data['northeast_longitude'] = northEast[0];
        }
        newCutoutDetails(graphicsDataForMapLayer);
    } // addGraphic end

    $('input[name="cutoutSelectorGroup"]:radio').change(
            function() {
                reatlasFunctions.cutoutSelectorChange(this);
            });
    
    reatlasFunctions.cutoutSelectorChange = function (selectedRadio){
        
        // initilize default value
        selectedRadio = typeof selectedRadio !== 'undefined' ? selectedRadio :
            (typeof selectedRadio !== 'undefined' ? selectedRadio : 
                    $('input[name="cutoutSelectorGroup"]:radio:checked'));
            
        
               
                // disable capacity button
                $("#capacitymapBtn").attr('disabled', 'disabled');

                $("#cutoutInfoDiv").html("");

                $("[id^=cutoutSelGrp]").each(function() {
                    $(this).css('display', 'none');
                    if ($(this).prop("id") != "cutoutSelGrpNew")
                        $(this).html('No CutOut found');
                });
                if ($(selectedRadio).val() == "default") {
                    $('#cutoutSelGrpDefault').css('display', 'block');
                    $('#cutoutSelGrpDefault').html('Loading...');
                    fetchCutoutList(defaultUserGroup, 'cutoutSelGrpDefault');

                } else if ($(selectedRadio).val() == "own") {
                    $('#cutoutSelGrpOwn').css('display', 'block');
                    $('#cutoutSelGrpOwn').html('Loading...');
                    fetchCutoutList(currentUserName, 'cutoutSelGrpOwn');
                } else if ($(selectedRadio).val() == "all") {
                    $('#cutoutSelGrpAll').css('display', 'block');
                    $('#cutoutSelGrpAll').html('Loading...');
                    fetchCutoutList(defaultUserGroup, 'cutoutSelGrpAll');
                    fetchCutoutList(currentUserName, 'cutoutSelGrpAll');
                } else if ($(selectedRadio).val() == "new") {
                    initNewCutoutOptions();
                    
                    if(_map.getLayer('rectGraphicsLayer'))
                        _map.removeLayer(_map.getLayer('rectGraphicsLayer'));
                    if(_map.getLayer('pointGraphicsLayer'))
                        _map.removeLayer(_map.getLayer('pointGraphicsLayer'));
            
                    $('#cutoutSelGrpNew').css('display', 'block');
                }
            }
       
            
    $('input[name="layoutSelectorGroup"]:radio').change(
            function() {

                if ($(this).val() == "old") {
                    $('#layoutSelGrpOld').html('Loading...');
                    fetchLayoutList(selectedCutoutID);
                } else if ($(this).val() == "new") {
                    /* Remove all changed data in cache then redraw*/
                    currentCapacityData = [];
                    drawGridPointsOnMap(originalCapacityData);
                    var $radios = $('input[name="layoutSelectorGroup"]:radio');
                    if ($radios.is(':checked') === false) {

                        $radios.filter('[value=new]').prop('checked', true);
                    }
                }
            }
    );

});

dojo.ready(function() {
    dojo.query(".info").attr("innerHTML", dojo.version);

});


