/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function() {

function openOverlay(olEl) {
        $oLay = $(olEl);
        
        if ($('#overlay-shade').length == 0)
            $('body').prepend('<div id="overlay-shade"></div>');

        $('#overlay-shade').fadeTo(300, 0.6, function() {
            var props = {
                oLayWidth       : $oLay.width(),
                scrTop          : $(window).scrollTop(),
                viewPortWidth   : $(window).width()
            };

            var leftPos = (props.viewPortWidth - props.oLayWidth) / 2;

            $oLay
                .css({
                    display : 'block',
                    opacity : 0,
                    top : '-=100',
                    left : leftPos+'px'
                })
                .animate({
                    top : props.scrTop + 240,
                    opacity : 1
                }, 600);
        });
    }

    function closeOverlay() {
        $('.overlay').animate({
            top : '-=100',
            opacity : 0
        }, 400, function() {
            $('#overlay-shade').fadeOut(300);
            $(this).css('display','none');
        });
    }
   
   // add "#overlay-shade, " next to '.overlay a' if you want to close popup on click backside
   
    $('body').on('click','.overlay a', function(e) { 
        closeOverlay();
        if ($(this).attr('href') == '#') e.preventDefault();
    });
    
    
    // Usage
    $('#overlaylaunch-inAbox').click(function(e) {
       openOverlay('#overlay-inAbox');
       e.preventDefault();
    });
    
    openOverlay('#overlay-inAbox');
    
    });