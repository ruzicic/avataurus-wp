<?php
/**
 * Plugin Name:       Avataurus
 * Plugin URI:        https://avataurus.com
 * Description:       Replace default Gravatar avatars with unique, deterministic face avatars powered by Avataurus. Every user gets a distinct, colorful face — no sign-up, no external accounts needed.
 * Version:           1.0.0
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * Author:            Mladen Ruzicic
 * Author URI:        https://mladenruzicic.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       avataurus
 *
 * @package Avataurus
 */

defined( 'ABSPATH' ) || exit;

define( 'AVATAURUS_VERSION', '1.0.0' );
define( 'AVATAURUS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'AVATAURUS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'AVATAURUS_SERVICE_URL', 'https://avataurus.com' );

/**
 * Main plugin class.
 */
final class Avataurus {

	/**
	 * Single instance.
	 *
	 * @var Avataurus|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Avataurus
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_filter( 'avatar_defaults', array( $this, 'register_default_avatar' ) );
		add_filter( 'get_avatar_url', array( $this, 'filter_avatar_url' ), 10, 3 );
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'settings_link' ) );
		}
	}

	/**
	 * Register Avataurus as an avatar default option.
	 *
	 * @param array $defaults Default avatar options.
	 * @return array
	 */
	public function register_default_avatar( $defaults ) {
		$defaults['avataurus']         = __( 'Avataurus (Face)', 'avataurus' );
		$defaults['avataurus_initial'] = __( 'Avataurus (Initial)', 'avataurus' );
		return $defaults;
	}

	/**
	 * Filter the avatar URL to use Avataurus service.
	 *
	 * @param string $url         Current avatar URL.
	 * @param mixed  $id_or_email User identifier.
	 * @param array  $args        Avatar arguments.
	 * @return string
	 */
	public function filter_avatar_url( $url, $id_or_email, $args ) {
		$default = isset( $args['default'] ) ? $args['default'] : get_option( 'avatar_default', 'mystery' );

		if ( ! in_array( $default, array( 'avataurus', 'avataurus_initial' ), true ) ) {
			return $url;
		}

		// If user has a custom Gravatar, respect it (unless force_default).
		$force = isset( $args['force_default'] ) ? $args['force_default'] : false;
		if ( ! $force && $this->has_gravatar( $id_or_email ) ) {
			return $url;
		}

		$seed    = $this->get_seed( $id_or_email );
		$seed    = apply_filters( 'avataurus_seed', $seed, $id_or_email );
		$size    = isset( $args['size'] ) ? absint( $args['size'] ) : 96;
		$size    = max( 16, min( 512, $size ) );
		$variant = ( 'avataurus_initial' === $default ) ? 'initial' : 'face';
		$variant = apply_filters( 'avataurus_variant', $variant, $id_or_email );

		$avatar_url = add_query_arg(
			array(
				'size'    => $size,
				'variant' => $variant,
			),
			trailingslashit( AVATAURUS_SERVICE_URL ) . rawurlencode( $seed )
		);

		return esc_url( apply_filters( 'avataurus_avatar_url', $avatar_url, $id_or_email, $args ) );
	}

	/**
	 * Derive a seed string from a user identifier.
	 *
	 * @param mixed $id_or_email User ID, email, WP_User, or WP_Comment.
	 * @return string
	 */
	private function get_seed( $id_or_email ) {
		$email = '';

		if ( is_numeric( $id_or_email ) ) {
			$user = get_userdata( (int) $id_or_email );
			if ( $user ) {
				$email = $user->user_email;
			}
		} elseif ( is_string( $id_or_email ) ) {
			$email = $id_or_email;
		} elseif ( $id_or_email instanceof WP_User ) {
			$email = $id_or_email->user_email;
		} elseif ( $id_or_email instanceof WP_Comment ) {
			if ( $id_or_email->user_id ) {
				$user = get_userdata( (int) $id_or_email->user_id );
				if ( $user ) {
					$email = $user->user_email;
				}
			}
			if ( empty( $email ) ) {
				$email = $id_or_email->comment_author_email;
			}
		}

		if ( empty( $email ) ) {
			return 'anonymous';
		}

		// Use email as seed for deterministic avatars.
		return strtolower( trim( $email ) );
	}

	/**
	 * Check if user has a real Gravatar (not default).
	 *
	 * Uses a cached HEAD request to Gravatar.
	 *
	 * @param mixed $id_or_email User identifier.
	 * @return bool
	 */
	private function has_gravatar( $id_or_email ) {
		$seed = $this->get_seed( $id_or_email );
		if ( 'anonymous' === $seed ) {
			return false;
		}

		$hash      = md5( $seed );
		$cache_key = 'avataurus_grav_' . $hash;
		$cached    = get_transient( $cache_key );

		if ( false !== $cached ) {
			return '1' === $cached;
		}

		$response = wp_remote_head(
			sprintf( 'https://www.gravatar.com/avatar/%s?d=404', $hash ),
			array( 'timeout' => 2 )
		);

		$has = ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response );

		// Cache for 24 hours.
		set_transient( $cache_key, $has ? '1' : '0', DAY_IN_SECONDS );

		return $has;
	}

	/**
	 * Register plugin settings.
	 */
	public function register_settings() {
		// Settings are handled via the native avatar_default option.
		// No additional settings needed.
	}

	/**
	 * Enqueue admin styles for the avatar preview.
	 *
	 * @param string $hook_suffix Admin page hook.
	 */
	public function admin_styles( $hook_suffix ) {
		if ( 'options-discussion.php' !== $hook_suffix ) {
			return;
		}

		wp_add_inline_style(
			'wp-admin',
			'.avatar-list img[src*="avataurus.com"] { border-radius: 0; }'
		);
	}

	/**
	 * Add settings link on plugin page.
	 *
	 * @param array $links Plugin action links.
	 * @return array
	 */
	public function settings_link( $links ) {
		$settings = sprintf(
			'<a href="%s">%s</a>',
			admin_url( 'options-discussion.php#default-avatar' ),
			__( 'Settings', 'avataurus' )
		);
		array_unshift( $links, $settings );
		return $links;
	}
}

/**
 * Initialize the plugin.
 *
 * @return Avataurus
 */
function avataurus() {
	return Avataurus::instance();
}

avataurus();
