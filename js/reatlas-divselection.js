/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
                    fetchCutoutList(defaultUserGroup, 'cutoutSelGrpDefault');
                } else if ($(this).val() == "own") {
                    $('#cutoutSelGrpDefault').css('display', 'none');
                    $('#cutoutSelGrpOwn').css('display', 'block');
                    $('#cutoutSelGrpAll').css('display', 'none');
                    $('#cutoutSelGrpNew').css('display', 'none');
                    $('#cutoutSelGrpOwn').html('Loading...');
                    fetchCutoutList(defaultUser, 'cutoutSelGrpOwn');
                } else if ($(this).val() == "all") {
                    $('#cutoutSelGrpDefault').css('display', 'none');
                    $('#cutoutSelGrpOwn').css('display', 'none');
                    $('#cutoutSelGrpAll').css('display', 'block');
                    $('#cutoutSelGrpNew').css('display', 'none');
                    $('#cutoutSelGrpAll').html('Loading...');
                    fetchCutoutList(defaultUserGroup, 'cutoutSelGrpAll');
                    fetchCutoutList(defaultUser, 'cutoutSelGrpAll');
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

