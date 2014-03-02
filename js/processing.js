/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function openProcessing() {
        $oLay = $('#processing-inAbox');
        
        if ($('#processing-shade').length == 0)
            $('body').prepend('<div id="processing-shade"></div>');

        $('#processing-shade').fadeTo(300, 0.6, function() {
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

    function closeProcessing() {
        $('.processing').animate({
            top : '-=100',
            opacity : 0
        }, 400, function() {
            $('#processing-shade').fadeOut(300);
            $(this).css('display','none');
        });
    }
   
   // add "#processing-shade, " next to '.processing a' if you want to close popup on click backside
   
    $('body').on('click','.processing a', function(e) { 
        closeProcessing();
        if ($(this).attr('href') == '#') e.preventDefault();
    });
    
    
    // Usage
    $('#processinglaunch-inAbox').click(function(e) {
       openProcessing();
       e.preventDefault();
    });
    
  //  openProcessing();
    
