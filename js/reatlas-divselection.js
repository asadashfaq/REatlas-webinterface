/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {
    // Handler for .ready() called.
   $("#cutoutselectorContainer input:checkbox").click(function() {
        var group = "input:checkbox[name='" + $(this).prop("name") + "']";
        $(group).prop("checked", false);
        $(this).prop("checked", true);
    });

    $("#SolarInfoDiv input:radio").click(function() {
       if($('input:radio[name=capacitySolarOption]:checked').val() == "Fixed Orientation"){
       $("#solarAngle1").show();
        $("#solarAngle2").show();
         
        //$('#select-table > .roomNumber').attr('enabled',false);
    }
    });
   
    var group = "input:radio[name='cutoutSelectorGroup']";
    $(group).prop("checked", false);

    var group = "input:checkbox[name='cutoutSelTool']";
    $(group).prop("checked", false);
    
    /**
     * Load Default group cutouts
     */
    $('#cutoutSelGrpDefault').html('Loading...');
    fetchCutoutList(defaultUserGroup, 'cutoutSelGrpDefault');
  
  
      $('#ModalDialogOpenLink').click(function () {
            $('#ModalDialogDiv').load(this.href, function () {
                $(this).dialog();
            });
            return false;
        });

});

$(function() {
    $('.date-picker').datepicker( {
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

$(document).ready(function(){
    var chartIsDisplayed;
    
    $('#mapDiv').append($('#graphView'));
    $('#graphView').width($('#mapDiv').width());
    
    $('#slide').click(function(){
   
    $('#graphView').width($('#mapDiv').width());
         
    toggleGraphView(true);
    });
    
     $('input[name="capacitySolarOption"]').click(function(){
      
        if(!$('input[name="capacitySolar"]').is(':checked'))
            {
                alert('Please select Solar capacity above');
                return false;
            }
        var optionVal = $(this).val();
        if(optionVal =="FixedOrientation")
            {
             $('#fixedOrientationGrp').show(100); 
             $('#fixedOrientationGrp input[type="text"]').val('');
            }else if(optionVal =="VerticalTracking")
            {
             $('#verticaltrackingGrp').show(100);
             $('#fixedOrientationGrp').hide(100);
             $('#verticaltrackingGrp input[type="text"]').val('');
            }
            else if(optionVal =="HorizontalTracking")
            {
             $('#horizontaltrackingGrp').show(100);
             $('#fixedOrientationGrp').hide(100);
             $('#verticaltrackingGrp').hide(100);
             $('#horizontaltrackingGrp input[type="text"]').val('');
            }
            else
            {
                 $('#fixedOrientationGrp').hide(100);
                 $('#verticaltrackingGrp').hide(100);
                 $('#horizontaltrackingGrp').hide(100);
            }
     });
     
     // disable capacity button
     $("#capacitymapBtn").attr('disabled', 'disabled');
    
    // Top menu click event handler
     $(document).on("click", ".disabled-detector", function (e) {
        var chk = $(this).parent().children('button').get(0);
        if(chk && chk != "undefined")
        {
            if($(chk).is(":disabled"))
                topmenuButtonClickHandler.call(chk, e);
            else
                $(chk).click();
        }
    });
    
    function topmenuButtonClickHandler(evt) {
        if(this.id =="capacitymapBtn"){
             alert('Please select a cutout first');
        }
    }
     
     
});

