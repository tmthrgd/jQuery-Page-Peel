/*
* NOTICE:
* If you notice any issues or bugs in the plugin please email them to tom.thorogood@ymail.com
* If you make any revisions to and/or re-release this plugin please notify tom.thorogood@ymail.com
*/

/*
* Copyright © 2010 Tom Thorogood (email: tom.thorogood@ymail.com)
* 
* This file is part of "jQuery Page Peel" Wordpress Plugin.
* 
* "jQuery Page Peel" is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
* 
* "jQuery Page Peel" is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
* 
* You should have received a copy of the GNU General Public License
* along with "jQuery Page Peel". If not, see <http://www.gnu.org/licenses/>.
*/

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