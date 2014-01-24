/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {
    // Handler for .ready() called.
    $('input[name="cutoutSelectorGroup"]:radio').change(
            function() {
                // disable capacity button
                $("#capacitymapBtn").attr('disabled', 'disabled');
     
                $("#cutoutInfoDiv").html("");
                if ($(this).val() == "default") {
                    $('#cutoutSelGrpDefault').css('display', 'block');
                    $('#cutoutSelGrpOwn').css('display', 'none');
                    $('#cutoutSelGrpAll').css('display', 'none');
                    $('#cutoutSelGrpNew').css('display', 'none');
                    $('#cutoutSelGrpDefault').html('Loading...');
                     $('#cutoutSelGrpOwn').html('No cutout found');
                      $('#cutoutSelGrpAll').html('No cutout found');
                    fetchCutoutList(defaultUserGroup, 'cutoutSelGrpDefault');
                    
                } else if ($(this).val() == "own") {
                    $('#cutoutSelGrpDefault').css('display', 'none');
                    $('#cutoutSelGrpOwn').css('display', 'block');
                    $('#cutoutSelGrpAll').css('display', 'none');
                    $('#cutoutSelGrpNew').css('display', 'none');
                    $('#cutoutSelGrpDefault').html('No cutout found');
                    $('#cutoutSelGrpOwn').html('Loading...');
                    $('#cutoutSelGrpAll').html('No cutout found');
                      
                    fetchCutoutList(currentUserName, 'cutoutSelGrpOwn');
                } else if ($(this).val() == "all") {
                    $('#cutoutSelGrpDefault').css('display', 'none');
                    $('#cutoutSelGrpOwn').css('display', 'none');
                    $('#cutoutSelGrpAll').css('display', 'block');
                    $('#cutoutSelGrpNew').css('display', 'none');
                    $('#cutoutSelGrpDefault').html('No cutout found');
                    $('#cutoutSelGrpOwn').html('No cutout found');
                    $('#cutoutSelGrpAll').html('Loading...');
                    fetchCutoutList(defaultUserGroup, 'cutoutSelGrpAll');
                    fetchCutoutList(currentUserName, 'cutoutSelGrpAll');
                } else if ($(this).val() == "new") {
                    $('#cutoutSelGrpDefault').css('display', 'none');
                    $('#cutoutSelGrpOwn').css('display', 'none');
                    $('#cutoutSelGrpAll').css('display', 'none');
                    $('#cutoutSelGrpNew').css('display', 'block');
                }
            }
    );

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
         
    var hidden = $('.hidden');
    if (hidden.hasClass('visible')){
        hidden.animate({"bottom":"-251px"}, "slow").removeClass('visible');
    } else {
        hidden.animate({"bottom":"0px"}, "slow").addClass('visible');
    }
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
            }else if(optionVal =="FullTracking")
            {
             $('#fixedOrientationGrp').hide(100);   
            }
     });
     
     // disable capacity button
     $("#capacitymapBtn").attr('disabled', 'disabled');
});

