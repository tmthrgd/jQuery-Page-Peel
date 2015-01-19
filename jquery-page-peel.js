jQuery(document).ready(function(){
	jQuery("#pageflip").hover(function() {
		jQuery("#pageflip img , .msg_block").stop()
			.animate({
				width: '307px', 
				height: '319px'
			}, 500); 
		} , function() {
		jQuery("#pageflip img").stop() 
			.animate({
				width: '50px', 
				height: '52px'
			}, 220);
		jQuery(".msg_block").stop() 
			.animate({
				width: '50px', 
				height: '50px'
			}, 200);
	});
});