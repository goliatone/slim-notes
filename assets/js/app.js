define(['jquery'], function($) {
	'use strict';
	//lets take care of stuff here :)
	var stickyHeaderTop = $('.top-bar').offset().top;
	var h = $('.top-bar').height()+ 30;
	$('.top-bar-pad').height(h);
	$('.top-bar-pad').css('display', 'none');
	$(window).scroll(function(){
		if($(window).scrollTop() > stickyHeaderTop){
			$('.top-bar').css({position: 'fixed', top: '0px', 'z-index':9999});
			//we use an empty div to actually compensate the heigth of
			//the header, when we take make it fixed we need to add its
			//height.
			$('.top-bar-pad').css('display', 'block');
        } else {
            $('.top-bar').css({position: 'static', top: '0px'});
            $('.top-bar-pad').css('display', 'none');
        }
	});



  return 'Hello from Yeoman!';

});