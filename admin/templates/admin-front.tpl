<script src="js/jquery/ui/jquery-ui.js"></script>
<link rel="stylesheet" href="css/jquery/ui/themes/smoothness/jquery-ui-1.10.3.custom.css" />
<link rel="stylesheet" href="css/admin-front-grid.css" />

    <div class="column">
    {$runningJobsBlock}
    {$newUpdatesBlock}
    </div>
    <div class="column">
    {$serverStatusBlock}
    {$trackingBlock}
    </div>
    <div class="column">
    {$onlineUsersBlock}
    {$userUpdatesBlock}
    </div>
<script>
$(function() {
  $( ".column" ).sortable({
    connectWith: ".column"
  });

  $( ".portlet" ).addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
    .find( ".portlet-header" )
      .addClass( "ui-widget-header ui-corner-all" )
      .prepend( "<span class=\'ui-icon ui-icon-minusthick\'></span>")
      .end()
    .find( ".portlet-content" );

  $( ".portlet-header .ui-icon" ).click(function() {
    $( this ).toggleClass( "ui-icon-minusthick" ).toggleClass( "ui-icon-plusthick" );
    $( this ).parents( ".portlet:first" ).find( ".portlet-content" ).toggle();
  });

  $( ".column" ).disableSelection();
});
</script>