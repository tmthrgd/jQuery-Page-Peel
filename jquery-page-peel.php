<?php
/*
Plugin Name: jQuery Page Peel
Plugin URI: http://www.tom-thorogood.com/wordpress-plugins/jquery-page-peel/
Description: Adds a page peel affect to the top right corner of the page using jQuery.
Version: 1.1
Author: Tom Thorogood
Author URI: http://www.tom-thorogood.com/
*/

// Pre-2.6 compatibility
if (!defined('WP_CONTENT_URL'))
      define('WP_CONTENT_URL',get_option('siteurl').'/wp-content');
if (!defined('WP_CONTENT_DIR'))
      define('WP_CONTENT_DIR',str_replace('/','\\',ABSPATH).'wp-content');
if (!defined('WP_PLUGIN_URL'))
      define('WP_PLUGIN_URL',WP_CONTENT_URL.'/plugins');
if (!defined('WP_PLUGIN_DIR'))
      define('WP_PLUGIN_DIR',WP_CONTENT_DIR.'\\plugins');

define('PLUGIN_URL',WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)));
define('PLUGIN_DIR',str_replace('/','\\',WP_PLUGIN_DIR).'\\'.basename(dirname(__FILE__)));

$jquery_page_peel_defaults=Array('link'=>'/feed/','target'=>'_blank');

if (!get_option('jquery-page-peel'))
	update_option('jquery-page-peel',serialize($jquery_page_peel_defaults));

$jquery_page_peel_values=unserialize(get_option('jquery-page-peel'));

function jquery_page_peel_addOptions()
{
	global $jquery_page_peel_values;
	
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
		<p>Upload a png image (<code>image/png</code>) to <code><?php echo PLUGIN_DIR; ?>\img.png</code> (Use the current image as an example.)</p>
		
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
	?>
<script type="text/javascript" src="<?php echo PLUGIN_URL; ?>/jquery-page-peel.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo PLUGIN_URL; ?>/jquery-page-peel.css" />
	<?php
}

function jquery_page_peel_addFooter()
{
	global $jquery_page_peel_values;
	echo "\n";
	?>
<div id="pageflip">
	<a href="<?php echo $jquery_page_peel_values['link']; ?>" target="<?php echo $jquery_page_peel_values['target']; ?>">
		<img src="<?php echo PLUGIN_URL; ?>/overlay.png" alt="" />
		<span class="msg_block"></span>
	</a>
</div>
	<?php
}

wp_enqueue_script('jquery');
add_action('admin_menu','jquery_page_peel_queOptions');
add_action('wp_head','jquery_page_peel_addHeader');
add_action('wp_footer','jquery_page_peel_addFooter');
