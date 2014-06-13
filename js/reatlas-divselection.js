/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {
    /* Handler for .ready() called.*/
    
    $("#cutoutselectorContainer input:checkbox").click(function() {
        var group = "input:checkbox[name='" + $(this).prop("name") + "']";
        $(group).prop("checked", false);
        $(this).prop("checked", true);
    });
/*
    var group = "input:radio[name='cutoutSelectorGroup']";
    $(group).prop("checked", false);
*/
    var group = "input:radio[name='layoutSelectorGroup']";
    $(group).prop("checked", false);


    var group = "input:checkbox[name='cutoutSelTool']";
    $(group).prop("checked", false);

    /**
     * Load Default group cutouts
     */
    $('#cutoutSelGrpDefault').html('Loading...');
    fetchCutoutList(defaultUserGroup, 'cutoutSelGrpDefault');


    $('#ModalDialogOpenLink').click(function() {
        $('#ModalDialogDiv').load(this.href, function() {
            $(this).dialog();
        });
        return false;
    });

});

$(function() {
    $('.date-picker').datepicker({
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        dateFormat: 'MM yy',
        onClose: function(dateText, inst) {
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).datepicker('setDate', new Date(year, month, 1));
        }
    });
});

$(document).ready(function() {
    var chartIsDisplayed;

    $('#mapDiv').append($('#graphView'));
    $('#graphView').width($('#mapDiv').width());

    $('#slide').click(function() {

        $('#graphView').width($('#mapDiv').width());

        toggleGraphView(true);
    });



    $('input[name="capacitySolarOption"]').click(function() {

        if (!$('input[name="capacitySolar"]').is(':checked'))
        {
            alert('Please select Solar capacity above');
            return false;
        }
        $('input[name="capacitySolarOption"]').each(function() {

            $('#' + $(this).val() + 'Grp').hide();
        });
        var optionVal = $(this).val();
        convertOptionsSel.orientation = optionVal;
        if (optionVal == "FixedOrientation")
        {
            $('#FixedOrientationGrp').show(100);
            $('#FixedOrientationGrp input[type="text"]').val('');
            convertOptionsSel.FixedOrientationSlope = $("#FixedOrientationSlope").val();
            convertOptionsSel.FixedOrientationAzimuth = $("#FixedOrientationAzimuth").val();
        } else if (optionVal == "VerticalTracking")
        {
            $('#VerticalTrackingGrp').show(100);
            $('#VerticalTrackingGrp input[type="text"]').val('');
            convertOptionsSel.VerticalTrackingAzimuth = $("#VerticalTrackingAzimuth").val();
        }
        else if (optionVal == "HorizontalTracking")
        {
            $('#HorizontalTrackingGrp').show(100);
            $('#HorizontalTrackingGrp input[type="text"]').val('');
            convertOptionsSel.HorizontalTrackingSlope = $("#HorizontalTrackingSlope").val();
        } else {
            convertOptionsSel.FullTracking = $("#FullTracking").val();
        }
    });

    // disable capacity button
    $("#capacitymapBtn").attr('disabled', 'disabled');
    

    // Top menu click event handler
    $(document).on("click", ".disabled-detector", function(e) {
        var chk = $(this).parent().children('button').get(0);
        if (chk && chk != "undefined")
        {
            if ($(chk).is(":disabled"))
                topmenuButtonClickHandler.call(chk, e);
            else
                $(chk).click();
        }
    });

    function topmenuButtonClickHandler(evt) {
        if (this.id == "capacitymapBtn") {
            alert('Please select a cutout first');
        }
    }


    $('input[name="capacityWindType"]:radio').change(
            function() {
                $("#WindSubList input:radio").attr("checked", false);
                toggleGraphView(true);

                convertOptionsSel.type = $(this).val();

                $("#WindSubList input:radio").filter('[value="' + convertOptionsSel[convertOptionsSel.type + "WindVal"] + '"]').prop('checked', true);

                /* Scroll to the selected radio */
                if (convertOptionsSel[convertOptionsSel.type + "WindId"])
                    $('#WindSubList').scrollTo('#' + convertOptionsSel[convertOptionsSel.type + "WindId"]);

                if ($(this).val() == "onshore") {
                    $('#capacityWindOnshore').css('display', 'block');
                    $('#capacityWindOnshore').html('Loading...');
                    /*var covertOperationHtml ='<div class="bold">Selected option for convert operation:</div> <br/>';
                     covertOperationHtml +='<div class="roundcorner withborder"><span class="bold-red">OnShore: Please select turbine name</span><br/>';
                     var targetNode = WindInfoDiv;
                     targetNode.innerHTML = covertOperationHtml;*/

                } else if ($(this).val() == "offshore") {
                    $('#capacityWindOffshore').css('display', 'block');
                    $('#capacityWindOffshore').html('Loading...');
                    /*var covertOperationHtml ='<div class="bold">Selected option for convert operation:</div> <br/>';
                     covertOperationHtml +='<div class="roundcorner withborder"><span class="bold-red">OffShore: Please select turbine name</span><br/>';
                     var targetNode = WindInfoDiv;
                     targetNode.innerHTML = covertOperationHtml;*/


                }
            }
    );

    $('input[name="capacityWindType"]:radio').filter('[value="onshore"]').attr('checked', true);
    $('input[name="capacityWindType"]:radio').filter('[value="onshore"]').trigger('change');

/* Conversion result radio change event */
$('input[name="joblist_conversion"]:radio').change(
            function() {
            var className = $(this).attr('class');
            var isDownloadable = false;
            var statusClass=className.split(" ")[1];
            switch(statusClass){
              case 'success':
                    isDownloadable = true;
                    break;
                case 'failure':
                    isDownloadable = false;
                    break;
                case 'waiting':
                    isDownloadable = false;
                    break;
                case 'error':
                default:
                    isDownloadable = false;
                    break;
              }
             dijit.byId("job_download").setAttribute('disabled', !isDownloadable);
            });
 
            

});

