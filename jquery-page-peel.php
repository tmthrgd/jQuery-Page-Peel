<?php
/*
Author: Tom Thorogood
Author URI: http://tom-thorogood.gotdns.com/
Description: Adds a page peel affect to the top right corner of the page using only jQuery no buggy, hard to customize flash!
Plugin Name: jQuery Page Peel
Plugin URI: http://tom-thorogood.gotdns.com/plugins/jquery-page-peel/
Version: 1.3.1
*/

/*
* NOTICE:
* If you notice any issues or bugs in the plugin please email them to tom.thorogood@ymail.com
* If you make any revisions to and/or re-release this plugin please notify tom.thorogood@ymail.com
*/

/*
* Copyright © 2010 Tom Thorogood (email: tom.thorogood@ymail.com)
* 
* Original design, Images, CSS and Javascript by: Soh Tanaka [@link http://www.sohtanaka.com/web-design/simple-page-peel-effect-with-jquery-css/]
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

/*
@access public
@since 1.3
@return undefined
*/
class jQuery_page_peel {
	/*
	@access public
	@since 1.3
	@return string plugin_version
	*/
	static function version() {
		return '1.3.1';
	}

	/*
	@access private
	@since 1.3
	@return undefined
	*/
	static function _init() {
		if (version_compare(self::get_option('version'), self::version(), '<')) {
			$old_options = get_option('jquery-page-peel');
			if ($old_options) {
				if (function_exists('maybe_unserialize')) { $old_options = maybe_unserialize($old_options); } else { $old_options = unserialize($old_options); }
				isset($old_options['link']) && self::set_option('link', $old_options['link']);
				isset($old_options['target']) && self::set_option('target', $old_options['target']);
				delete_option('jquery-page-peel');
				unset($old_options);
			}
			self::set_option('version', self::version());
		}
		self::add_option('version', self::version());
		self::add_option('link', self::default_option('link'));
		self::add_option('rel', self::default_option('rel'));
		self::add_option('target', self::default_option('target'));
		self::add_option('onclick', self::default_option('onclick'));
		self::add_option('z-index', self::default_option('z-index'));
		wp_enqueue_script('jquery');
		add_action('admin_head', array(__CLASS__, '_admin_head'));
		add_action('admin_menu', array(__CLASS__, '_admin_menu'));
		add_action('wp_head', array(__CLASS__, '_head'));
		add_action('wp_footer', array(__CLASS__, '_footer'));
	}
		
	/*
	@access private
	@since 1.3
	@param string $name option_name
	@param string $value option_value
	@return undefined
	*/
	private static function add_option($name, $value = '') {
		add_option('jQuery-page-peel-' . $name, $value);
	}

	/*
	@access private
	@since 1.3
	@param string $name option_name
	@param boolean $htmlentites run htmlentities on the option_value @since 1.3.1
	@return string option_value
	*/
	private static function get_option($name, $htmlentities = false) {
		$option = stripslashes(get_option('jQuery-page-peel-' . $name));
		$htmlentities && $option = htmlentities($option);
		return $option;

	}
	
	/*
	@access private
	@since 1.3
	@param string $name option_name
	@param string $value option_value
	@return undefined
	*/
	private static function set_option($name, $value = '') {
		update_option('jQuery-page-peel-' . $name, $value);
	}
	
	/*
	@access private
	@since 1.3.1
	@param string $name option_name
	@return string option_default_value
	*/
	private static function default_option($name) {
		$options = array(
			'link' => 'feed',
			'rel' => '',
			'target' => '_blank',
			'onclick' => '',
			'z-index' => '99'
			);
		return (isset($options[$name]) ? $options[$name] : '');
	}

	/*
	@access private
	@since 1.3
	@return string current_working_directory
	*/
	private static function cwd() {
		defined('WP_PLUGIN_DIR') && $dir = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, WP_PLUGIN_DIR . '/' . basename(dirname(__FILE__)));
		(isset($dir) && is_dir($dir)) || $dir = dirname(__FILE__);
		return $dir;
	}

	/*
	@access private
	@since 1.3
	@return string current_working_url
	*/
	private static function cwu() {
		defined('WP_PLUGIN_URL') && $url = WP_PLUGIN_URL . '/' . basename(dirname(__FILE__));
		isset($url) || $url = str_replace('\\', '/', ((strtolower($_SERVER['HTTPS']) == 'on') ? 'https://' : 'http://') . ((isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']) . ((strtolower($_SERVER['HTTPS']) == 'on' && $_SERVER['SERVER_PORT'] == 443 || strtolower($_SERVER['HTTPS']) != 'on' && $_SERVER['SERVER_PORT'] == 80) ? '' : ':'.$_SERVER['SERVER_PORT']) . substr(dirname(__FILE__), strlen($_SERVER['DOCUMENT_ROOT'])));
		return $url;
	}

	/*
	@access private
	@since 1.3
	@return boolean wheather were using a custom image
	*/
	private static function custom_image() {
		return file_exists(self::cwd() . '/custom_underlay.png');
	}

	/*
	@access private
	@since 1.3
	@param string file
	@param boolean $htmlentites run htmlentities on the url @since 1.3.1
	@return string full_url
	*/
	private static function url($file = '', $htmlentities = false) {
		switch (strtolower($file)) {
			case 'underlay.png':
				self::custom_image() && $file = 'custom_underlay.png';
				break;
		}
		empty($file) || $file = '/' . $file;
		$url = self::cwu() . $file;
		$htmlentities && $url = htmlentities($url);
		return $url;
	}
	
	/*
	@note WordPress < 2.9.0 will always return true
	@access public
	@since 1.3
	@return boolean $latest is_latest_version
	*/
	static function latest_version() {
		function_exists('get_site_transient') && $plugins = get_site_transient('update_plugins');
		$latest = !isset($plugins) || !isset($plugins->response) || !is_array($plugins->response) || !isset($plugins->response[basename(dirname(__FILE__)) . '/' . basename(__FILE__)]);
		return $latest;
	}

	/*
	@access private
	@since 1.3
	@return undefined
	*/
	static function _admin_menu() {
		if (function_exists('add_options_page')) {
			add_options_page('jQuery Page Peel', 'jQuery Page Peel', 'manage_options', __FILE__, array(__CLASS__, '_options_page'));
		}
	}

	/*
	@access private
	@since 1.3
	@return undefined
	*/
	static function _admin_head() {
?>
<!--jQuery Page Peel - <?php echo self::version(); ?>: http://tom-thorogood.gotdns.com/plugins/jquery-page-peel/-->
<style type="text/css">
/* <![CDATA[ */
.jQuery-page-peel .red {
	color:red;
}
.jQuery-page-peel .green {
	color:green;
}
.jQuery-page-peel table {
	width:100%;
}
.jQuery-page-peel th {
	width:15%;
	font-weight:normal;
	font-size:1.1em;
	vertical-align:top;
}
.jQuery-page-peel td {
	font-weight:normal;
	font-size:0.9em;
	vertical-align:top;
}
.jQuery-page-peel acronym, .jQuery-page-peel .dashed {
	border-bottom:1px dashed #999;
}
/* ]]> */
</style>
<!--/jQuery Page Peel-->
<?php
	}

	/*
	@access private
	@since 1.3
	@return undefined
	*/
	static function _options_page() {
		if (isset($_POST['jQuery-page-peel-reset'])) {
			if (!function_exists('check_admin_referer') || check_admin_referer(__FILE__ . self::version())) {
				self::set_option('link', self::default_option('link'));
				self::set_option('rel', self::default_option('rel'));
				self::set_option('target', self::default_option('target'));
				self::set_option('onclick', self::default_option('onclick'));
				self::set_option('z-index', self::default_option('z-index'));
				
				echo "\t" . '<div class="updated"><p>Options reset.</p></div>';
			}
		}

		if (isset($_POST['jQuery-page-peel-submit'])) {
			if (!function_exists('check_admin_referer') || check_admin_referer(__FILE__ . self::version())) {
				isset($_POST['jQuery-page-peel-link']) && self::set_option('link', $_POST['jQuery-page-peel-link']);
				isset($_POST['jQuery-page-peel-rel']) && self::set_option('rel', $_POST['jQuery-page-peel-rel']);
				isset($_POST['jQuery-page-peel-target']) && self::set_option('target', $_POST['jQuery-page-peel-target']);
				isset($_POST['jQuery-page-peel-onclick']) && self::set_option('onclick', $_POST['jQuery-page-peel-onclick']);
				isset($_POST['jQuery-page-peel-z-index']) && self::set_option('z-index', $_POST['jQuery-page-peel-z-index']);
				
				echo "\t" . '<div class="updated"><p>Options saved successfully.</p></div>';
			}
		}
?>
	<div class="wrap jQuery-page-peel">
		<h2>jQuery Page Peel</h2>
		<form method="post" action="">
			<fieldset class="options">
				<table class="editform">
					<tr>
						<th scope="row">Author:</th>
						<td><a href="http://tom-thorogood.gotdns.com" target="_blank">Tom Thorogood</a> | <a href="http://tom-thorogood.gotdns.com/plugins/" target="_blank">Other plugins by Tom Thorogood</a></td>
					</tr>
					<tr>
						<th scope="row">Credit:</th>
						<td>Original design, Images, CSS and Javascript: <a href="http://www.sohtanaka.com/web-design/simple-page-peel-effect-with-jquery-css/" target="_blank">Simple Page Peel Effect with jQuery &amp; CSS</a> by <a href="http://www.sohtanaka.com/about/" target="_blank">Soh Tanaka</a></td>
					</tr>
					<tr>
						<th scope="row">Version:</th>
						<td class="<?php if (self::latest_version()) { echo 'green'; } else { echo 'red'; } ?>"><span class="dashed" title="<?php if (self::latest_version()) { echo 'Latest version'; } else { echo 'Newer version avalible'; } ?>"><?php echo htmlentities(self::version()); ?></span></td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<th scope="row">href attribute:</th>
						<td><input name="jQuery-page-peel-link" type="text" value="<?php echo self::get_option('link', true); ?>" /> The <acronym title="Uniform Resource Locator">URL</acronym> to use in the link. <small>eg. http://tom-thorogood.gotdns.com, /feed/, #top</small> <a href="http://www.w3schools.com/tags/att_a_href.asp" target="_blank">#</a></td>
					</tr>
					<tr>
						<th scope="row">rel attribute:</th>
						<td><input name="jQuery-page-peel-rel" type="text" value="<?php echo self::get_option('rel', true); ?>" /> The relationship between this site and the link. <small>eg. nofollow, external,nofollow</small> <a href="http://www.w3schools.com/tags/att_a_rel.asp" target="_blank">#</a></td>
					</tr>
					<tr>
						<th scope="row">target attribute:</th>
						<td><input name="jQuery-page-peel-target" type="text" value="<?php echo self::get_option('target', true); ?>" /> The target of the link. <small>eg. _blank, _top</small> <a href="http://www.w3schools.com/tags/att_a_target.asp" target="_blank">#</a></td>
					</tr>
					<tr>
						<th scope="row">onclick attribute:</th>
						<td><input name="jQuery-page-peel-onclick" type="text" value="<?php echo self::get_option('onclick', true); ?>" /> Javascript to execute when the link is clicked. <small>eg. alert('Test');return false;</small> <a href="http://www.w3schools.com/tags/ref_eventattributes.asp" target="_blank">#</a></td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<th scope="row">z-index:</th>
						<td><input name="jQuery-page-peel-z-index" type="text" value="<?php echo self::get_option('z-index', true); ?>" /> The z-index of the page peel div, increase if the images go behind other site content. <small>Numeric values only.</small></td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<th scope="row">Image:</th>
						<td>
							To replace the default image upload a png <code>(image/png)</code> image <code>(307px x 308px)</code> to <code><?php echo htmlentities(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, self::cwd() . '/custom_underlay.png')); ?></code>.
							<p class="<?php if (self::custom_image()) { echo 'green">Currently using your custom image:'; } else { echo 'red">Currently using the default image:'; } ?><br /><img src="<?php echo self::url('underlay.png', true); ?>" alt="" /></p>
						</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<th><input type="submit" class="button-primary" name="jQuery-page-peel-submit" value="Save" /></th>
						<td><input type="submit" class="button-primary" name="jQuery-page-peel-reset" value="Reset" onclick="return confirm('WARNING: This will reset ALL options, are you sure want to continue?');" /></td>
					</tr>
					</table>
			</fieldset>
			<input name="_wpnonce" type="hidden" value="<?php echo (function_exists('wp_create_nonce') ? wp_create_nonce(__FILE__ . self::version()) : ''); ?>" />
		</form>
	</div>
<?php
	}

	/*
	@access private
	@since 1.3
	@return undefined
	*/
	static function _head() {
?>
<!--jQuery Page Peel - <?php echo self::version(); ?>: http://tom-thorogood.gotdns.com/plugins/jquery-page-peel/-->
<style type="text/css">
/* <![CDATA[ */
#jQuery-page-peel {
	position:fixed;
	right:0;
	top:0;
	z-index:<?php echo self::get_option('z-index'); ?>;
}
#jQuery-page-peel .overlay {
	width:50px;
	height:52px;
	z-index:2;
	position:absolute;
	top:0;
	right:0;
	border:none;
}
#jQuery-page-peel .underlay {
	width:50px;
	height:50px;
	overflow:hidden;
	position:absolute;
	top:0;
	right:0;
	z-index:1;
	border:none;
	background:url('<?php echo self::url('underlay.png', true); ?>') no-repeat right top;
}
/* ]]> */
</style>
<!--[if lte IE 6]><style type="text/css">#jQuery-page-peel { position:absolute; }</style><![endif]-->
<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function() {
	jQuery('#jQuery-page-peel').hover(function() {
		jQuery('#jQuery-page-peel .overlay, #jQuery-page-peel .underlay').stop().animate({
				width: '307px', 
				height: '319px'
		}, 500); 
	}, function() {
		jQuery('#jQuery-page-peel .overlay').stop().animate({
				width: '50px', 
				height: '52px'
		}, 220);
		jQuery('#jQuery-page-peel .underlay').stop().animate({
				width: '50px', 
				height: '50px'
		}, 200);
	});
});
/* ]]> */
</script>
<!--/jQuery Page Peel-->
<?php
	}

	/*
	@access private
	@since 1.3
	@return undefined
	*/
	static function _footer() {
?>
<!--jQuery Page Peel - <?php echo self::version(); ?>: http://tom-thorogood.gotdns.com/plugins/jquery-page-peel/-->
<div id="jQuery-page-peel">
	<a href="<?php echo self::get_option('link', true); ?>" target="<?php echo self::get_option('target', true); ?>" rel="<?php echo self::get_option('rel', true); ?>" onclick="<?php echo self::get_option('onclick', true); ?>">
		<img src="<?php echo self::url('overlay.png', true); ?>" class="overlay" width="307" height="319" alt="" />
		<span class="underlay"></span>
	</a>
</div>
<!--/jQuery Page Peel-->
<?php	
	}
}

jQuery_page_peel::_init();
?>