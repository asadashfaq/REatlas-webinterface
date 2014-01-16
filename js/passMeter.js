/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$.fn.passwordStrength = function( options ){
	return this.each(function(){
		var that = this;that.opts = {};
		that.opts = $.extend({}, $.fn.passwordStrength.defaults, options);
		
		that.div = $(that.opts.targetDiv);
		that.defaultClass = that.div.attr('class');
		that.generatePass = $(that.opts.generatePass);
                
		that.percents = (that.opts.classes.length) ? 100 / that.opts.classes.length : 100;

		 v = $(this)
		.keyup(function(){
			if( typeof el == "undefined" )
				this.el = $(this);
			var s = getPasswordStrength (this.value);
			var p = this.percents;
			var t = Math.floor( s / p );
			
			if( 100 <= s )
				t = this.opts.classes.length - 1;
				
			this.div
				.removeAttr('class')
				.addClass( this.defaultClass )
				.addClass( this.opts.classes[ t ] );
				
		})
		.after(function() {
                        if($(that.generatePass) == "true")
                            return '<a href="#"><br/>Generate Password</a>';
                        else
                            return '';
                      })
		.next()
		.click(function(){
			$(this).prev().val( randomPassword() ).trigger('keyup');
			return false;
		});
	});

	function getPasswordStrength(H){
		var D=(H.length);
		if(D>5){
			D=5
		}
		var F=H.replace(/[0-9]/g,"");
		var G=(H.length-F.length);
		if(G>3){G=3}
		var A=H.replace(/\W/g,"");
		var C=(H.length-A.length);
		if(C>3){C=3}
		var B=H.replace(/[A-Z]/g,"");
		var I=(H.length-B.length);
		if(I>3){I=3}
		var E=((D*10)-20)+(G*10)+(C*15)+(I*10);
		if(E<0){E=0}
		if(E>100){E=100}
		return E
	}

	function randomPassword() {
		var chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$_+";
		var size = 10;
		var i = 1;
		var ret = ""
		while ( i <= size ) {
			$max = chars.length-1;
			$num = Math.floor(Math.random()*$max);
			$temp = chars.substr($num, 1);
			ret += $temp;
			i++;
		}
		return ret;
	}

};
	
$.fn.passwordStrength.defaults = {
	classes : Array('is10','is20','is30','is40','is50','is60','is70','is80','is90','is100'),
	targetDiv : '#passwordStrengthDiv',
        generatePass: false,
	cache : {}
}

$.fn.passwordMatch = function( options ){
                var that = this;
                that.opts = {};
		that.opts = $.extend({}, $.fn.passwordMatch.defaults, options);
		
		that.div = $(that.opts.targetDiv);
                that.srcDiv = $(that.opts.sourceDiv);
                 console.log(that.srcDiv);
                v = $(this)
		.keyup(function(){
			if( typeof el == "undefined" )
				this.el = $(this);
                            
			var pass = $(that.srcDiv).val();
                        console.log("pass:"+pass);
			if(pass == this.value) {
                            //    $(that.div).text("valid"); 
                                that.div
				.removeAttr('class')
				.addClass('check');
                             }
                             else {
                             //    $(that.div).text("invalid"); 
                                 that.div
				.removeAttr('class')
				.addClass('cross');
                             }
                             if(pass == "" || pass == "undefined") {
                                 that.div
				.removeAttr('class');
                    }
				
		});
        }

$.fn.passwordMatch.defaults = {
        sourceDiv: '#password',
	targetDiv : '#passwordMatchDiv',
	cache : {}
}
/*
$(document)
.ready(function(){
	$('input[name="password"]').passwordStrength();
        $('input[name="password2"]').passwordMatch();
	//$('input[name="password2"]').passwordStrength({targetDiv: '#passwordStrengthDiv2',classes : Array('is10','is20','is30','is40')});

});
*/