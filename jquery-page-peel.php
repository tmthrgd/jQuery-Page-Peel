<?php
/*
Author: Tom Thorogood
Author URI: http://tom-thorogood.gotdns.com/
Description: Adds a page peel affect to the top right corner of the page using only jQuery no buggy, hard to customize flash!
Disclaimer: Use at your own risk. No warranty expressed or implied is provided. The author will never be liable for any loss of profit, physical or psychical damage, legal problems. The author disclaims any responsibility for any action of final users. It is the final user's responsibility to obey all applicable local, state, and federal laws.
Plugin Name: jQuery Page Peel
Plugin URI: http://tom-thorogood.gotdns.com/wordpress-plugins/jquery-page-peel/
Version: 1.2
*/

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

$jquery_page_peel_url=WP_PLUGIN_URL.'/'.basename(dirname(__FILE__));
$jquery_page_peel_dir=str_replace('/','\\',WP_PLUGIN_DIR).'\\'.basename(dirname(__FILE__));

$jquery_page_peel_defaults=Array('link'=>'/feed/','target'=>'_blank');

if (!get_option('jquery-page-peel'))
	update_option('jquery-page-peel',serialize($jquery_page_peel_defaults));

$jquery_page_peel_values=unserialize(get_option('jquery-page-peel'));

function jquery_page_peel_addOptions()
{
	global $jquery_page_peel_values, $jquery_page_peel_dir;
	
	if (isset($_POST['jquery-page-peel_update']))
	{
		if (isset($_POST['jquery-page-peel_link'])&&!empty($_POST['jquery-page-peel_link']))
			$jquery_page_peel_values['link']=$_POST['jquery-page-peel_link'];
		
		if (isset($_POST['jquery-page-peel_target'])&&!empty($_POST['jquery-page-peel_target']))
			$jquery_page_peel_values['target']=$_POST['jquery-page-peel_target'];
		
		update_option('jquery-page-peel',serialize($jquery_page_peel_values));
	}
	
	$link=$jquery_page_peel_values['link'];
	$__target=$jquery_page_peel_values['target'];
	$_blank=($__target==='_blank');
	$_top=($__target==='_top');
	$_none=($__target==='');
	?>
<div style="position: relative; top: 20px; left: 5px;">
	<span style="font-size: large;">jQuery Page Peel</span>
	
	<div style="position: relative; top: 20px; left: 5px;">
		<p>Upload a png image (<code>image/png</code>) to <code><?php echo $jquery_page_peel_dir; ?>\img.png</code> (Use the current image as an example.)</p>
		
		<form method="post">
<div class="inside">
<fieldset>
<span>Link: </span>
<p><input name="jquery-page-peel_link" type="text" value="<?php echo $link; ?>" style="width: 450px;" /></p>
</fieldset>
</div>

	
<br />
<div class="inside">
<fieldset>
<span>Target: </span>

<p><label for="link_target_blank" class="selectit">
<input id="link_target_blank" name="jquery-page-peel_target" value="_blank" type="radio"<?php if ($_blank) { ?> checked="checked"<?php } ?> />
<code>_blank</code> - new window or tab.</label></p>

<p><label for="link_target_top" class="selectit">
<input id="link_target_top" name="jquery-page-peel_target" value="_top" type="radio"<?php if ($_top) { ?> checked="checked"<?php } ?> />
<code>_top</code> - current window or tab, with no frames.</label></p>

<p><label for="link_target_none" class="selectit">
<input id="link_target_none" name="jquery-page-peel_target" value="" type="radio"<?php if ($_none) { ?> checked="checked"<?php } ?> />
<code>_none</code> - same window or tab.</label></p>

</fieldset>
</div>

<br />
<input name="jquery-page-peel_update" type="submit" value="Update Options" />
		</form>
	</div>
</div>
	<?php
}

function jquery_page_peel_queOptions()
{
	add_options_page('jQuery Page Peel','jQuery Page Peel','manage_options',__FILE__,'jquery_page_peel_addOptions');
}

function jquery_page_peel_addHeader()
{
	global $jquery_page_peel_url;
	?>
<script type="text/javascript" src="<?php echo $jquery_page_peel_url; ?>/jquery-page-peel.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo $jquery_page_peel_url; ?>/jquery-page-peel.css" />
	<?php
}

function jquery_page_peel_addFooter()
{
	global $jquery_page_peel_values, $jquery_page_peel_url;
	echo "\n";
	?>
<div id="pageflip">
	<a href="<?php echo $jquery_page_peel_values['link']; ?>" target="<?php echo $jquery_page_peel_values['target']; ?>">
		<img src="<?php echo $jquery_page_peel_url; ?>/overlay.png" alt="" />
		<span class="msg_block"></span>
	</a>
</div>
	<?php
}

wp_enqueue_script('jquery');
add_action('admin_menu','jquery_page_peel_queOptions');
add_action('wp_head','jquery_page_peel_addHeader');
add_action('wp_footer','jquery_page_peel_addFooter');
