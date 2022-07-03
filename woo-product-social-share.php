<?php
/*
Plugin Name: Woocommerce Product Page Social Share
Plugin URI : https://wordpress.org/plugins/woo-product-page-social-share/
Description: Add attractive & responsive social sharing icons for Facebook, Twitter, Pinterest to your product pages.
Version: 2.0.2
Author: Sajjad Hossain Sagor
Author URI: https://sajjadhsagor.com/
Text Domain: woo-product-page-social-share

License: GPL2
This WordPress Plugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

This free software is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this software. If not, see http://www.gnu.org/licenses/gpl-2.0.html.
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// ---------------------------------------------------------
// Checking if Woocommerce is either installed or active
// ---------------------------------------------------------

register_activation_hook( __FILE__, 'wppss_check_woocommerce_activation_status' );

add_action( 'admin_init', 'wppss_check_woocommerce_activation_status' );

function wppss_check_woocommerce_activation_status(){

	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

		// Deactivate the plugin
		deactivate_plugins(__FILE__);

		// Throw an error in the wordpress admin console
		$error_message = __( 'Woocommerce Product Page Social Share requires <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a> plugin to be active! <a href="javascript:history.back()"> Go back & activate Woocommerce. </a>', 'woo-product-page-social-share' );

		wp_die( $error_message, __( 'WooCommerce Plugin Not Found', 'woo-product-page-social-share' ) );
	}
}

// ---------------------------------------------------------
// Define Plugin Folders Path
// ---------------------------------------------------------
define( "WPPSS_PLUGIN_PATH", plugin_dir_path( __FILE__ ) );

define( "WPPSS_PLUGIN_URL", __FILE__);

define( "WPPSS_PLUGIN_INCLUDES_PATH", WPPSS_PLUGIN_PATH . 'includes/' );

define( "WPPSS_PLUGIN_OPTION_NAME", 'wpss_register_settings_fields' );

// ---------------------------------------------------------
// Call Required Plugin Files
// ---------------------------------------------------------
require_once WPPSS_PLUGIN_INCLUDES_PATH . 'class.plugin.settings.php';

// ---------------------------------------------------------
// Show Buttons to Front Page
// ---------------------------------------------------------
require_once WPPSS_PLUGIN_INCLUDES_PATH . "class.render.front.icons.php";
