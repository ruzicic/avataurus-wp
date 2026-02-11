<?php
/**
 * Uninstall Avataurus.
 *
 * Cleans up Gravatar cache transients and resets avatar default if set to Avataurus.
 *
 * @package Avataurus
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// Reset avatar default if it was set to Avataurus.
$default = get_option( 'avatar_default' );
if ( in_array( $default, array( 'avataurus', 'avataurus_initial' ), true ) ) {
	update_option( 'avatar_default', 'mystery' );
}

// Clean up Gravatar check transients.
global $wpdb;
$wpdb->query(
	"DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_avataurus_grav_%' OR option_name LIKE '_transient_timeout_avataurus_grav_%'"
);
