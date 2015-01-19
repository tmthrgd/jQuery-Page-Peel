<?php
/**
 * Author: Tom Thorogood
 * Author URI: http://xenthrax.com
 * Description: Adds a page peel affect to the top right corner of the page using only jQuery no buggy, hard to customize flash!
 * Plugin Name: jQuery Page Peel
 * Plugin URI: http://xenthrax.com/wordpress/jquery-page-peel/
 * Version: 1.3.2b1
 *
 * Plugin Shortlink: http://xenthrax.com/jquery-page-peel/
 * Other Plugins: http://xenthrax.com/wordpress/
 * 
 * WordPress Plugin: https://wordpress.org/extend/plugins/jquery-page-peel/
 * GitHub Repo: https://github.com/TheTomThorogood/jQuery-Page-Peel
 */

/**
 * If you notice any issues or bugs in the plugin please contact me [@link http://xenthrax.com/contact]
 * If you make any revisions to and/or re-release this plugin please contact me [@link http://xenthrax.com/contact]
 */

/**
 * Copyright (c) 2010-2012 Tom Thorogood
 *
 * Original design, Images, CSS and Javascript by: Soh Tanaka [@link http://web.archive.org/web/20110423063645/http://www.sohtanaka.com/web-design/simple-page-peel-effect-with-jquery-css/]
 *
 * This file is part of "jQuery Page Peel" Wordpress Plugin.
 *
 * "jQuery Page Peel" is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation version 3.
 * 
 * You may NOT assume that you can use any other version of the GPL.
 *
 * "jQuery Page Peel" is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with "jQuery Page Peel". If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @package jQuery Page Peel
 * @since 1.3
 */
class jQuery_Page_Peel {
	/**
	 * @access private
	 * @since 1.3.2
	 * @var array
	 */
	private $notices = array();

	/**
	 * @access public
	 * @since 1.3
	 * @return string jQuery Page Peel version
	 */
	function version() {
		static $plugin_data;
		
		if (is_null($plugin_data))
			$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'));
		
		return $plugin_data['Version'];
	}

	/**
	 * @since 1.3.2
	 */
	function jQuery_page_peel() {
		$args = func_get_args();
		call_user_func_array(array(&$this, '__construct'), $args);
	}

	/**
	 * @since 1.3.2
	 */
	function __construct() {
		$this->add_option('version', $this->version());
		
		if (version_compare($this->get_option('version'), $this->version(), '<')) {
			$old_options = get_option('jquery-page-peel');
			
			if (!empty($old_options)) {
				if (function_exists('maybe_unserialize'))
					$old_options = maybe_unserialize($old_options);
				else
					$old_options = @unserialize($old_options);
				
				if (isset($old_options['link']))
					$this->set_option('link', $old_options['link']);
				
				if (isset($old_options['target']))
					$this->set_option('target', $old_options['target']);
				
				delete_option('jquery-page-peel');
			}
			
			$this->set_option('version', $this->version());
		}
		
		if (file_exists($this->cwd() . '/custom_underlay.png')) {
			@copy($this->cwd() . '/custom_underlay.png', ABSPATH . 'jquery-page-peel-underlay.png');
			
			if (file_exists(ABSPATH . 'jquery-page-peel-underlay.png')) {
				@unlink($this->cwd() . '/custom_underlay.png');
				$this->add_notice(htmlentities($this->cwd()) . '/custom_underlay.png has been moved to ' . htmlentities(ABSPATH) . 'jquery-page-peel-underlay.png.');
			}
		}
		
		foreach ($this->default_options() as $name => $value)
			$this->add_option($name, $value);
		
		add_action('admin_head-' . $this->slug(true), array(&$this, '_admin_head_options'));
		add_action('load-' . $this->slug(true), array(&$this, '_options_init'));
		add_action('admin_notices', array(&$this, '_admin_notices'));
		add_action('admin_menu', array(&$this, '_admin_menu'));
		add_action('wp_head', array(&$this, '_head'));
		add_action('wp_footer', array(&$this, '_footer'));
		add_action('wp_print_scripts', array(&$this, '_wp_print_scripts'));
		
		if (file_exists($this->cwd() . '/custom_underlay.png') && !file_exists(ABSPATH . 'jquery-page-peel-underlay.png'))
			$this->add_notice('Please move ' . htmlentities($this->cwd()) . '/custom_underlay.png to ' . htmlentities(ABSPATH) . 'jquery-page-peel-underlay.png and delete ' . htmlentities($this->cwd()) . '/custom_underlay.png.', 'error');
	}
	
	/**
	 * @access private
	 * @since 1.3
	 * @param string $name
	 * @param string $value Optional.
	 * @return void
	 */
	private function add_option($name, $value = '') {
		add_option('jQuery-page-peel-' . $name, $value);
	}
	
	/**
	 * @access private
	 * @since 1.3
	 * @param string $name
	 * @param boolean $htmlentites Optional.
	 * @return string Option value
	 */
	private function get_option($name, $htmlentities = false) {
		$option = get_option('jQuery-page-peel-' . $name);
		
		if ($htmlentities)
			$option = htmlentities($option);
		
		return $option;
	}
	
	/**
	 * @access private
	 * @since 1.3
	 * @param string $name
	 * @param string $value Optional.
	 * @return void
	 */
	private function set_option($name, $value = '') {
		update_option('jQuery-page-peel-' . $name, $value);
	}
	
	/**
	 * @access private
	 * @since 1.3.2
	 * @return array jQuery Page Peel default options
	 */
	private function default_options() {
		return array(
			'link' => '/feed/',
			'rel' => '',
			'target' => '_blank',
			'onclick' => '',
			'z-index' => '99'
			);
	}
	
	/**
	 * @access private
	 * @since 1.3.2
	 * @param bool $settings Optional.
	 * @return string jQuery Page Peel slug
	 */
	private function slug($settings = false) {
		return $settings ? 'settings_page_' . basename(dirname(__FILE__)) . '/' . substr(basename(__FILE__), 0, -4) : basename(dirname(__FILE__)) . '/' . basename(__FILE__);
	}
	
	/**
	 * @access private
	 * @since 1.3
	 * @return string Curent working directory
	 */
	private function cwd() {
		$dir = WP_PLUGIN_DIR . '/' . basename(dirname(__FILE__));
		
		if (!is_dir($dir))
			$dir = dirname(__FILE__);
		
		return $dir;
	}
	
	/**
	 * @access private
	 * @since 1.3
	 * @return string Curent working url
	 */
	private function cwu() {
		return WP_PLUGIN_URL . '/' . basename(dirname(__FILE__));
	}
	
	/**
	 * @access private
	 * @since 1.3
	 * @return bool Whether were using a custom underlay
	 */
	private function custom_image() {
		return file_exists($_SERVER['DOCUMENT_ROOT'] . '/jquery-page-peel-underlay.png');
	}
	
	/**
	 * @access private
	 * @since 1.3
	 * @param string file
	 * @param bool $htmlentites Optional.
	 * @param bool $docroot Optional.
	 * @return string Url
	 */
	private function url($file = '', $htmlentities = false, $docroot = false) {
		$fullpath = false;
		
		switch (strtolower($file)) {
			case 'underlay.png':
				if ($this->custom_image()) {
					$docroot = true;
					$file = 'jquery-page-peel-underlay.png';
				}
				
				$o = $file;
				$file = apply_filters('jquery_page_peel_underlay', $file);
				$fullpath = ($file != $o);
				break;
		}
		
		if (!empty($file))
			$file = '/' . $file;
		
		if (!$fullpath && $docroot)
			$url = get_bloginfo('siteurl') . $file;
		else if (!$fullpath)
			$url = $this->cwu() . $file;
		
		if ($htmlentities)
			$url = htmlentities($url);
		
		return $url;
	}
	
	/**
	 * @note WordPress < 2.9.0 will always return true
	 * @access public
	 * @since 1.3
	 * @return bool Is the latest version of jQuery Page Peel
	 */
	function latest_version() {
		if (function_exists('get_site_transient')) {
			$plugins = get_site_transient('update_plugins');
			return (!isset($plugins->response) || !is_array($plugins->response) || !isset($plugins->response[$this->slug()]));
		}
		
		return true;
	}
	
	/**
	 * @access private
	 * @since 1.3.2
	 * @param string $msg
	 * @param string $type Optional.
	 * @param int $priority Optional.
	 * @return void
	 */
	private function add_notice($msg, $type = 'updated', $priority = false) {
		$type = strtolower($type);
		$priority = ($priority === false) ? (($type == 'error') ? 5 : 10) : (int)$priority;
		
		if (!isset($this->notices[$priority]))
			$this->notices[$priority] = array();
		
		$this->notices[$priority][] = (object)array(
			'msg' => $msg,
			'type' => $type
			);
	}
		
	/**
	 * @access private
	 * @since 1.3.2
	 * @return void
	 */
	function _admin_notices() {
		ksort($this->notices);
		
		foreach($this->notices as $priority => $notices) {
			foreach($notices as $notice)
				echo '<div class="' . $notice->type . '"><p>' . $notice->msg . "</p></div>\n";
		}
	}
	
	/**
	 * @access private
	 * @since 1.3.2
	 * @return void
	 */
	function _wp_print_scripts() {
		wp_enqueue_script('jquery');
	}
	
	/**
	 * @access private
	 * @since 1.3
	 * @return void
	 */
	function _admin_menu() {
		add_options_page('jQuery Page Peel', 'jQuery Page Peel', 'manage_options', __FILE__, array(&$this, '_options_page'));
	}
	
	/**
	 * @access private
	 * @since 1.3
	 * @return void
	 */
	function _admin_head_options() {
?>
<!--jQuery Page Peel - <?php echo $this->version(); ?>: http://xenthrax.com/wordpress/jquery-page-peel/-->
<style type="text/css">
#jQuery-page-peel .red{color:red;}
#jQuery-page-peel .green{color:green;}
#jQuery-page-peel table{width:100%;}
#jQuery-page-peel th{width:15%;font-weight:normal;font-size:1.1em;vertical-align:top;}
#jQuery-page-peel td{font-weight:normal;font-size:0.9em;vertical-align:top;}
#jQuery-page-peel abbr,#jQuery-page-peel .dashed{border-bottom:1px dashed #999;}
</style>
<!--/jQuery Page Peel-->
<?php
	}
	
	/**
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	function _options_init() {
		if (isset($_POST['jQuery-page-peel-submit'])) {
			if (check_admin_referer(__FILE__ . $this->version())) {
				foreach (array('link', 'rel', 'target', 'onclick') as $option) {
					if (isset($_POST['jQuery-page-peel-' . $option]))
						$this->set_option($option, stripslashes(trim($_POST['jQuery-page-peel-' . $option])));
				}
				
				if (isset($_POST['jQuery-page-peel-z-index'])) {
					$zIndex = stripslashes(trim($_POST['jQuery-page-peel-z-index']));
					
					if (is_numeric($zIndex))
						$this->set_option('zIndex', $zIndex);
				}
				
				$this->add_notice('Options saved successfully.');
			}
		} else if (isset($_POST['jQuery-page-peel-reset'])) {
			if (check_admin_referer(__FILE__ . $this->version())) {
				foreach ($this->default_options() as $name => $value)
					$this->set_option($name, $value);
				
				$this->add_notice('Options reset.');
			}
		}
	}
	
	/**
	 * @access private
	 * @since 1.3
	 * @return void
	 */
	function _options_page() {
		global $wp_version;
?>
<!--jQuery Page Peel - <?php echo $this->version(); ?>: http://xenthrax.com/wordpress/jquery-page-peel/-->
	<div id="jQuery-page-peel" class="wrap">
		<h2>jQuery Page Peel</h2>
		<form method="post" action="">
			<fieldset class="options">
				<table class="editform">
					<tr>
						<th scope="row">Author:</th>
						<td><a href="http://xenthrax.com" target="_blank">Tom Thorogood</a> | <a href="http://xenthrax.com/wordpress/#plugins" target="_blank">Other plugins by Tom Thorogood</a> | <a href="http://xenthrax.com/wordpress/jquery-page-peel/" target="_blank">Documentation</a></td>
					</tr>
					<tr>
						<th scope="row">Credit:</th>
						<td>Original design, Images, CSS and Javascript: <a href="http://web.archive.org/web/20110423063645/http://www.sohtanaka.com/web-design/simple-page-peel-effect-with-jquery-css/" target="_blank">Simple Page Peel Effect with jQuery &amp; CSS (Archive)</a> by <a href="http://www.sohtanaka.com/about/" target="_blank">Soh Tanaka</a></td>
					</tr>
					<tr>
						<th scope="row">Version:</th>
<?php if (version_compare($wp_version, '2.9.0', '>=')) { ?>
						<td class="<?php if ($this->latest_version()) { echo 'green'; } else { echo 'red'; } ?>"><span class="dashed" title="<?php if ($this->latest_version()) { echo 'Latest version'; } else { echo 'Newer version avalible'; } ?>"><?php echo htmlentities($this->version()); ?></span></td>
<?php } else { ?>
						<td><span class="dashed"><?php echo htmlentities($this->version()); ?></span></td>
<?php } ?>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<th scope="row">href attribute:</th>
						<td><input name="jQuery-page-peel-link" type="text" value="<?php echo $this->get_option('link', true); ?>" /> The <abbr title="Uniform Resource Locator">URL</abbr> to use in the link. <small>eg. http://google.com, /feed/, #top</small> <a href="http://www.w3schools.com/tags/att_a_href.asp" target="_blank">#</a></td>
					</tr>
					<tr>
						<th scope="row">rel attribute:</th>
						<td><input name="jQuery-page-peel-rel" type="text" value="<?php echo $this->get_option('rel', true); ?>" /> The relationship between this site and the link. <small>eg. nofollow, external,nofollow</small> <a href="http://www.w3schools.com/tags/att_a_rel.asp" target="_blank">#</a></td>
					</tr>
					<tr>
						<th scope="row">target attribute:</th>
						<td><input name="jQuery-page-peel-target" type="text" value="<?php echo $this->get_option('target', true); ?>" /> The target of the link. <small>eg. _blank, _top</small> <a href="http://www.w3schools.com/tags/att_a_target.asp" target="_blank">#</a></td>
					</tr>
					<tr>
						<th scope="row">onclick attribute:</th>
						<td><input name="jQuery-page-peel-onclick" type="text" value="<?php echo $this->get_option('onclick', true); ?>" /> Javascript to execute when the link is clicked. <small>eg. alert('Test');return false;</small> <a href="http://www.w3schools.com/tags/ref_eventattributes.asp" target="_blank">#</a></td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<th scope="row">z-index:</th>
						<td><input name="jQuery-page-peel-z-index" type="number" value="<?php echo $this->get_option('z-index', true); ?>" /> The z-index of the page peel div, increase if the images go behind other site content. <small>Numeric values only.</small></td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<th scope="row">Image:</th>
						<td>
							To replace the default image upload a png <code>(image/png)</code> image <code>(307px x 308px)</code> to <code><?php echo ABSPATH . 'jquery-page-peel-underlay.png'; ?></code>.
							<p class="<?php if ($this->custom_image()) { echo 'green">Currently using your custom image:'; } else { echo 'red">Currently using the default image:'; } ?><br /><img src="<?php echo $this->url('underlay.png', true); ?>" alt="" /></p>
						</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<th><input type="submit" class="button-primary" name="jQuery-page-peel-submit" value="Save" /></th>
						<td><input type="submit" class="button-primary" name="jQuery-page-peel-reset" value="Reset" onclick="return confirm('WARNING: This will reset ALL options, are you sure want to continue?');" /></td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<th></th>
						<td>Please support us by <a href="http://twitter.com/?status=I+just+installed+jQuery+Page+Peel+WordPress+plugin+http:%2F%2Fxenthrax.com%2Fjquery-page-peel+%23wordpress" target="_blank">tweeting about this plugin</a> or <a href="<?php echo admin_url(); ?>post-new.php" target="_blank">writing a post about this plugin</a>.</td>
					</tr>
				</table>
			</fieldset>
			<?php wp_nonce_field(__FILE__ . $this->version()); ?>
		</form>
	</div>
<!--/jQuery Page Peel-->
<?php
	}
	
	/**
	 * @access private
	 * @since 1.3
	 * @return void
	 */
	function _head() {
?>
<!--jQuery Page Peel - <?php echo $this->version(); ?>: http://xenthrax.com/wordpress/jquery-page-peel/-->
<style type="text/css">
#jQuery-page-peel{position:fixed;right:0;top:0;z-index:<?php echo $this->get_option('z-index'); ?>;}
#jQuery-page-peel .overlay{width:50px;height:52px;z-index:2;position:absolute;top:0;right:0;border:none;max-width:none;}
#jQuery-page-peel .underlay{width:50px;height:50px;overflow:hidden;position:absolute;top:0;right:0;z-index:1;border:none;background:url('<?php echo $this->url('underlay.png', true); ?>') no-repeat right top;}
</style>
<!--[if lte IE 6]><style type="text/css">#jQuery-page-peel{position:absolute;}</style><![endif]-->
<script type="text/javascript">
jQuery(function($) {
	$('#jQuery-page-peel').hover(function() {
		$('#jQuery-page-peel .overlay,#jQuery-page-peel .underlay').stop().animate({
			width: '307px',
			height: '319px'
		}, 500);
	}, function() {
		$('#jQuery-page-peel .overlay').stop().animate({
			width: '50px',
			height: '52px'
		}, 220);
		$('#jQuery-page-peel .underlay').stop().animate({
			width: '50px',
			height: '50px'
		}, 200);
	});
});
</script>
<!--/jQuery Page Peel-->
<?php
	}
	
	/**
	 * @access private
	 * @since 1.3
	 * @return void
	 */
	function _footer() {
?>
<!--jQuery Page Peel - <?php echo $this->version(); ?>: http://xenthrax.com/wordpress/jquery-page-peel/-->
<div id="jQuery-page-peel">
	<a href="<?php echo $this->get_option('link', true); ?>" target="<?php echo $this->get_option('target', true); ?>" rel="<?php echo $this->get_option('rel', true); ?>" onclick="<?php echo $this->get_option('onclick', true); ?>">
		<img src="<?php echo $this->url('overlay.png', true); ?>" class="overlay" width="307" height="319" alt="" />
		<span class="underlay"></span>
	</a>
</div>
<!--/jQuery Page Peel-->
<?php	
	}
}

/**
 * @global object $jQuery_Page_Peel Creates a new jQuery_Page_Peel object
 * @since 1.3.2
 */
$jQuery_Page_Peel = new jQuery_Page_Peel();
?>