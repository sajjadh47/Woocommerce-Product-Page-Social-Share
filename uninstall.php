<?php

/**
 * Bail if uninstall constant is not defined
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/**
 * Remove plugin options on uninstall/delete
 */

delete_option( 'ssfwc_settings' );
