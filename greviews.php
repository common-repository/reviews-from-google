<?php
/**
* Plugin name: Reviews from Google
* Description: Will retrieve reviews from Google My Business Pages and add it to your WordPress Site (Free Version).
* Version: 3.1
* Github: https://github.com/adnanusman
* Author: Adnan Usman
* Author URI: https://www.tacticalengine.com/
* License: GNU General Public License v2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html
* Text Domain: greviews
**/


// Exit if accessed directly
if(!defined('ABSPATH')) {
  exit;
}

function greviews_scripts() {
  wp_enqueue_style( 'greviews', plugin_dir_url( __FILE__ ) . 'css/style.css', array(), '1.0.2', 'all' );
	wp_register_script( 'greviews_read_more_less', plugins_url('/js/greviews.js', __FILE__));
}

add_action('wp_enqueue_scripts', 'greviews_scripts');

// enqueue color picker 
add_action( 'admin_enqueue_scripts', 'greviews_enqueue_admin_scripts' );
function greviews_enqueue_admin_scripts() {
    wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'greviews-colorpicker-js', plugins_url('/js/colorpicker.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
		wp_enqueue_style( 'admin', plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), '1.0.0', 'all' );
}

add_action('wp_head', 'greviews_css_styling', 0);

// Global Variable
$greviews_options = get_option('greviews_settings');

$plugindir = plugins_url( '', __FILE__ );

// DB setup
require(plugin_dir_path(__FILE__) . 'includes/init.php');
register_activation_hook( __FILE__, 'greviews_install' );
register_deactivation_hook( __FILE__, 'greviews_deactivate');

// GReviews Class
require(plugin_dir_path(__FILE__) . 'includes/classes/GReviews.php');
require(plugin_dir_path(__FILE__) . 'includes/classes/Stored_Reviews_WP.php');

// Shortcode Template
require(plugin_dir_path(__FILE__) . 'includes/templates/two-columns-reviews-template.php');
require(plugin_dir_path(__FILE__) . 'includes/templates/fullwidth-reviews-template.php');

// Widget
require(plugin_dir_path(__FILE__) . 'includes/templates/greviews-widget.php');

// Create page in tool submenu 
require(plugin_dir_path(__FILE__) . 'includes/admin-page.php');

// Set colors based on data in the WP Database
function greviews_css_styling() {
	$titleColor = get_option('greviews_settings')['title_color'];
	$textColor = get_option('greviews_settings')['text_color'];
	$linkColor = get_option('greviews_settings')['link_color'];
	$viewAllLinkColor = get_option('greviews_settings')['viewall_link_color'];
	$ratingTextColor = get_option('greviews_settings')['rating_text_color'];
	$starColor = get_option('greviews_settings')['star_color'];
	$containerBG = get_option('greviews_settings')['container_bg'];
	$hideImages = get_option('greviews_settings')['hide_images'];

	echo
		"<style type='text/css'>
			.greviews-title-container {
				color: $titleColor;
			}
			.greviews-rating,
			.greview-content,
			.greview-relative-time {
				color: $textColor;
			}
			.greviews-rating {
				color: $ratingTextColor;
			}
			.greview-star-icon:before {
				color: $starColor;
			}
			.greview-container a {
				color: $linkColor;
			}
			.greviews-more-link a, .greviews-more-link-widget a {
				color: $viewAllLinkColor;
			}
			.greview-container {
				background: $containerBG;
			}
		</style>
		";
}
