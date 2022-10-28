<?php
/*
Plugin Name: Social Sharer for Woo
Plugin URI : https://wordpress.org/plugins/social-sharer-for-woo/
Description: Add attractive & responsive social sharing icons with link to your woocommerce product pages.
Version: 1.0.0
Author: Sajjad Hossain Sagor
Author URI: https://sajjadhsagor.com/
Text Domain: social-sharer-for-woo

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

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( ! defined( 'SSFWC_SOCIAL_SHARER_FOR_WC_ROOT_DIR' ) )
{
    define( 'SSFWC_SOCIAL_SHARER_FOR_WC_ROOT_DIR', dirname( __FILE__ ) ); // Plugin root dir
}

if( ! defined( 'SSFWC_SOCIAL_SHARER_FOR_WC_ROOT_URL' ) )
{
    define( 'SSFWC_SOCIAL_SHARER_FOR_WC_ROOT_URL', plugin_dir_url( __FILE__ ) ); // Plugin root url
}

if( ! defined( 'SSFWC_SOCIAL_SHARER_FOR_WC_VERSION' ) )
{
    define( 'SSFWC_SOCIAL_SHARER_FOR_WC_VERSION', '1.0.0' ); // Plugin current version
}

if( ! defined( 'SSFWC_SOCIAL_SHARER_FOR_WC_OPTION_NAME' ) )
{
    define( 'SSFWC_SOCIAL_SHARER_FOR_WC_OPTION_NAME', 'ssfwc_settings' ); // Plugin option name
}

if( ! defined( 'SSFWC_SOCIAL_SHARER_FOR_WC_PLUGIN_URL' ) )
{
    define( 'SSFWC_SOCIAL_SHARER_FOR_WC_PLUGIN_URL', __FILE__ ); // Plugin url
}

/**
 * Plugin Activation Hook
 */
register_activation_hook( __FILE__, 'ssfwc_social_sharer_for_wc_plugin_activated' );

if ( ! function_exists( 'ssfwc_social_sharer_for_wc_plugin_activated' ) )
{
    function ssfwc_social_sharer_for_wc_plugin_activated()
    {
    	if ( function_exists( 'ssfw_check_woocommerce_activation_status' ) )
    	{
    		ssfw_check_woocommerce_activation_status();
    	}
    }
}

/**
 * Plugin Deactivation Hook
 */
register_deactivation_hook( __FILE__, 'ssfwc_social_sharer_for_wc_plugin_deactivated' );

if ( ! function_exists( 'ssfwc_social_sharer_for_wc_plugin_deactivated' ) )
{
    function ssfwc_social_sharer_for_wc_plugin_deactivated() {}
}

/**
 * Plugin Uninstalled / Deleted Hook
 */
register_uninstall_hook( __FILE__, 'ssfwc_social_sharer_for_wc_plugin_uninstalled' );

if ( ! function_exists( 'ssfwc_social_sharer_for_wc_plugin_uninstalled' ) )
{
    function ssfwc_social_sharer_for_wc_plugin_uninstalled() {}
}

/**
 * This gets the plugin ready for translation
 */
add_action( 'plugins_loaded', 'ssfwc_social_sharer_for_wc_load_textdomain' );

if ( ! function_exists( 'ssfwc_social_sharer_for_wc_load_textdomain' ) )
{
    function ssfwc_social_sharer_for_wc_load_textdomain()
    {
        load_plugin_textdomain( 'social-sharer-for-woo', '', dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }
}

/**
 * Add Go To Settings Page in Plugin List Table
 */
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'ssfwc_social_sharer_for_wc_goto_settings_page_link' );

if ( ! function_exists( 'ssfwc_social_sharer_for_wc_goto_settings_page_link' ) )
{
    function ssfwc_social_sharer_for_wc_goto_settings_page_link( $links )
    {   
        $goto_settings_link = [ '<a href="' . admin_url( 'admin.php?page=social-sharer-for-woo' ) . '">'. __( 'Settings', 'social-sharer-for-woo' ) .'</a>' ];
        
        return array_merge( $links, $goto_settings_link );
    }
}

/**
 * Checking if Woocommerce is either installed or active
 */
add_filter( 'admin_init', 'ssfw_check_woocommerce_activation_status' );

if ( ! function_exists( 'ssfw_check_woocommerce_activation_status' ) )
{
    function ssfw_check_woocommerce_activation_status()
    {   
        if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
        {
			// Deactivate the plugin
			deactivate_plugins( __FILE__ );

			// Throw an error in the wordpress admin console
			$error_message = __( 'Social Sharer for WooComerce requires <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a> plugin to be active! <a href="javascript:history.back()"> Go back & activate Woocommerce. </a>', 'social-sharer-for-woo' );

			wp_die( $error_message, __( 'WooCommerce Plugin Not Found', 'social-sharer-for-woo' ) );
		}
    }
}

/**
 * Include plugin settings
 */
require SSFWC_SOCIAL_SHARER_FOR_WC_ROOT_DIR . '/includes/class.plugin.settings.php';

/**
 * Show Buttons to Front Page
 */
require SSFWC_SOCIAL_SHARER_FOR_WC_ROOT_DIR . '/includes/class.render.front.icons.php';
