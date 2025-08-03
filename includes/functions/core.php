<?php
/**
 * Core plugin functionality.
 *
 * @package Podcastify
 */

namespace Podcastify\Core;

use \WP_Error as WP_Error;

/**
 * Default setup routine
 *
 * @return void
 */
function setup() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	add_action( 'plugins_loaded', $n( 'i18n' ) );
	add_action( 'init', $n( 'init' ) );
	add_action( 'wp_enqueue_scripts', $n( 'scripts' ) );
	add_action( 'wp_enqueue_scripts', $n( 'styles' ) );
	add_action( 'admin_enqueue_scripts', $n( 'admin_scripts' ) );
	add_action( 'admin_enqueue_scripts', $n( 'admin_styles' ) );

	// Editor styles. add_editor_style() doesn't work outside of a theme.
	add_filter( 'mce_css', $n( 'mce_css' ) );
	// Hook to allow async or defer on asset loading.
	add_filter( 'script_loader_tag', $n( 'script_loader_tag' ), 10, 2 );
	
	// Block editor support
	add_action( 'enqueue_block_editor_assets', $n( 'block_editor_assets' ) );
	
	add_action( 'init', $n( 'wpp_includes' ), 1 );
	add_action( 'init', __NAMESPACE__ . '\wpp_feed_permalink_structure', 99 );
	do_action( 'wp_podcastify_loaded' );
}

/**
 * Podcastify includes
 *
 * @since 1.0.0
 * @return void
 */
function wpp_includes() {
	require_once WP_PODCASTIFY_INC . '/utils/helper.php';
	require_once WP_PODCASTIFY_INC . '/classes/class-podcast-post.php';
	require_once WP_PODCASTIFY_INC . '/classes/class-wpp-setting-api.php';
	require_once WP_PODCASTIFY_INC . '/classes/class-wpp-settings.php';
	require_once WP_PODCASTIFY_INC . '/classes/class-wpp-frontend.php';
	require_once WP_PODCASTIFY_INC . '/classes/class-wpp-ajax.php';
	require_once WP_PODCASTIFY_INC . '/feed/class-wpp-podcast-feed.php';
	require_once WP_PODCASTIFY_INC . '/rest/class-wpp-episode-controller.php';

}

/**
 * Root hooks for Podcastify
 *
 * @since 1.0.0
 * @return void
 */
function wpp_hooks() {

}

/**
 * Setup custom permalink structure for podcastify.
 *
 * @since 1.0.0
 * @return void
 */
function wpp_feed_permalink_structure() {

	// $feed_slug = apply_filters( 'ssp_feed_slug', 'episode_series' );
	add_rewrite_rule( '^feed/podcasts/([^/]*)/?', 'index.php?feed=podcasts&podcastify_series=$matches[1]', 'top' );
	add_rewrite_tag( '%podcastify_series%', '([^&]+)' );

	// Download link..
	add_rewrite_rule( '^podcastify-download/([^/]*)/([^/]*)/?', 'index.php?podcast_episode=$matches[1]', 'top' );
	add_rewrite_tag( '%podcast_episode%', '([^&]+)' );
}

/**
 * Registers the default textdomain.
 *
 * @return void
 */
function i18n() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'podcastify' );
	load_textdomain( 'podcastify', WP_LANG_DIR . '/podcastify/podcastify-' . $locale . '.mo' );
	load_plugin_textdomain( 'podcastify', false, plugin_basename( WP_PODCASTIFY_PATH ) . '/languages/' );
}

/**
 * Initializes the plugin and fires an action other plugins can hook into.
 *
 * @return void
 */
function init() {
	do_action( 'wp_podcastify_init' );
}

/**
 * Activate the plugin
 *
 * @return void
 */
function activate() {
	// First load the init scripts in case any rewrite functionality is being loaded
	init();
	flush_rewrite_rules();
	set_default_setting();
}

/**
 * Deactivate the plugin
 *
 * Uninstall routines should be in uninstall.php
 *
 * @return void
 */
function deactivate() {

}


/**
 * The list of knows contexts for enqueuing scripts/styles.
 *
 * @return array
 */
function get_enqueue_contexts() {
	return [ 'admin', 'frontend', 'shared' ];
}

/**
 * Generate an URL to a script, taking into account whether SCRIPT_DEBUG is enabled.
 *
 * @param string $script Script file name (no .js extension)
 * @param string $context Context for the script ('admin', 'frontend', or 'shared')
 *
 * @return string|WP_Error URL
 */
function script_url( $script, $context ) {

	if ( ! in_array( $context, get_enqueue_contexts(), true ) ) {
		return new WP_Error( 'invalid_enqueue_context', 'Invalid $context specified in Podcastify script loader.' );
	}

	return WP_PODCASTIFY_URL . "dist/js/${script}.js";

}

/**
 * Generate an URL to a stylesheet, taking into account whether SCRIPT_DEBUG is enabled.
 *
 * @param string $stylesheet Stylesheet file name (no .css extension)
 * @param string $context Context for the script ('admin', 'frontend', or 'shared')
 *
 * @return string URL
 */
function style_url( $stylesheet, $context ) {

	if ( ! in_array( $context, get_enqueue_contexts(), true ) ) {
		return new WP_Error( 'invalid_enqueue_context', 'Invalid $context specified in Podcastify stylesheet loader.' );
	}

	return WP_PODCASTIFY_URL . "dist/css/${stylesheet}.css";

}

/**
 * Enqueue scripts for front-end.
 *
 * @return void
 */
function scripts() {

	wp_enqueue_script(
		'wp_podcastify_shared',
		script_url( 'shared', 'shared' ),
		[],
		WP_PODCASTIFY_VERSION,
		true
	);

	wp_enqueue_script(
		'wp_podcastify_frontend',
		script_url( 'frontend', 'frontend' ),
		[ 'wp-hooks' ],
		WP_PODCASTIFY_VERSION,
		true
	);

	wp_localize_script(
		'wp_podcastify_frontend',
		'Podcastify',
		[
			'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
			'ajaxSecurity' => wp_create_nonce( 'podcastify_security' ),
		]
	);

	// Minimal player controls are now integrated into main frontend.js

}

/**
 * Enqueue scripts for admin.
 *
 * @return void
 */
function admin_scripts() {

	wp_enqueue_script(
		'wp_podcastify_shared',
		script_url( 'shared', 'shared' ),
		[],
		WP_PODCASTIFY_VERSION,
		true
	);
	wp_enqueue_media();
	wp_enqueue_script(
		'wp_podcastify_admin',
		script_url( 'admin', 'admin' ),
		[ 'wp-hooks' ],
		WP_PODCASTIFY_VERSION,
		true
	);

}

/**
 * Enqueue styles for front-end.
 *
 * @return void
 */
function styles() {

	wp_enqueue_style(
		'wp_podcastify_shared',
		style_url( 'shared-style', 'shared' ),
		[],
		WP_PODCASTIFY_VERSION
	);

	if ( is_admin() ) {
		wp_enqueue_style(
			'wp_podcastify_admin',
			style_url( 'admin-style', 'admin' ),
			[],
			WP_PODCASTIFY_VERSION
		);
	} else {
		wp_enqueue_style(
			'wp_podcastify_frontend',
			style_url( 'style', 'frontend' ),
			[],
			WP_PODCASTIFY_VERSION
		);

		wp_add_inline_style( 'wp_podcastify_frontend', \Podcastify\Utils\player_color_customization() );

		// Load minimal player assets if minimal player is selected
		$player_type = \Podcastify\Utils\wpp_get_setting( 'player_type', 'wpp_player', 'single' );
		if ( 'minimal' === $player_type ) {
			wp_enqueue_style(
				'wp_podcastify_minimal_player',
				WP_PODCASTIFY_URL . 'assets/css/frontend/minimal-player-styles.css',
				[ 'wp_podcastify_frontend' ],
				WP_PODCASTIFY_VERSION
			);
			
			// Enqueue minimal player JavaScript
			wp_enqueue_script(
				'wp_podcastify_minimal_player',
				WP_PODCASTIFY_URL . 'assets/js/frontend/minimal-player.js',
				[ 'wp_podcastify_frontend' ],
				WP_PODCASTIFY_VERSION,
				true
			);
		}

		// Google fonts.
		wp_enqueue_style( 'wpp-google-fonts', 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;700&display=swap', [], WP_PODCASTIFY_VERSION );

		// Font Awesome.
		wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css', [], WP_PODCASTIFY_VERSION );

	}

}

/**
 * Enqueue styles for admin.
 *
 * @return void
 */
function admin_styles() {

	wp_enqueue_style(
		'wp_podcastify_shared',
		style_url( 'shared-style', 'shared' ),
		[],
		WP_PODCASTIFY_VERSION
	);

	wp_enqueue_style(
		'wp_podcastify_admin',
		style_url( 'admin-style', 'admin' ),
		[],
		WP_PODCASTIFY_VERSION
	);

}

/**
 * Enqueue block editor assets.
 *
 * @return void
 */
function block_editor_assets() {
	wp_enqueue_style(
		'wp_podcastify_editor',
		WP_PODCASTIFY_URL . 'assets/css/frontend/editor-style.css',
		[],
		WP_PODCASTIFY_VERSION
	);
}

/**
 * Enqueue editor styles. Filters the comma-delimited list of stylesheets to load in TinyMCE.
 *
 * @param string $stylesheets Comma-delimited list of stylesheets.
 * @return string
 */
function mce_css( $stylesheets ) {
	if ( ! empty( $stylesheets ) ) {
		$stylesheets .= ',';
	}

	return $stylesheets . WP_PODCASTIFY_URL . ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ?
			'assets/css/frontend/editor-style.css' :
			'dist/css/editor-style.min.css' );
}

/**
 * Add async/defer attributes to enqueued scripts that have the specified script_execution flag.
 *
 * @link https://core.trac.wordpress.org/ticket/12009
 * @param string $tag    The script tag.
 * @param string $handle The script handle.
 * @return string
 */
function script_loader_tag( $tag, $handle ) {
	$script_execution = wp_scripts()->get_data( $handle, 'script_execution' );

	if ( ! $script_execution ) {
		return $tag;
	}

	if ( 'async' !== $script_execution && 'defer' !== $script_execution ) {
		return $tag; // _doing_it_wrong()?
	}

	// Abort adding async/defer for scripts that have this script as a dependency. _doing_it_wrong()?
	$wp_scripts = wp_scripts();
	foreach ( $wp_scripts->registered as $script ) {
		if ( in_array( $handle, $script->deps, true ) ) {
			return $tag;
		}
	}

	// Add the attribute if it hasn't already been added.
	if ( ! preg_match( ":\s$script_execution(=|>|\s):", $tag ) ) {
		$tag = preg_replace( ':(?=></script>):', " $script_execution", $tag, 1 );
	}

	return $tag;
}


/**
 * Default settings of the plugin.
 *
 *@since 1.1.0
 */
function get_default_settings() {
	$settings = [
		'wpp_setting'             => [
			'player_location'     => [
				'the_content'     => 'the_content',
			],
			'player_position'     => 'above_content',
			'player_visibility'   => 'all_users',
			'itune_fields'        => 'on',
		],
		'wpp_player'              => [
			'player_type'         => 'single',
			'show_meta'           => 'on',
			'player_meta'         => [
				'download'        => 'download',
				'like'            => 'like',
				'share'           => 'share',
			],
			'share_network'       => [
				'facebook'        => 'facebook',
				'linkedin'        => 'linkedin',
				'twitter'         => 'twitter',
			],
			'control_color'       => '#101a32',
			'background_color'    => '#fff',
		],
		'wpp_feed'                => [
			'title'               => get_bloginfo( 'name' ),
			'subtitle'            => get_bloginfo( 'description' ),
			'author'              => get_bloginfo( 'name' ),
			'summary'             => get_bloginfo( 'description' ),
			'podcast_language'    => 'en-us',
			'podcast_explicit'    => 'no',
			'publish_date'        => 'publish_date',
			'show_type'           => '',
			'feed_description'    => 'excerpt',
		],
	];

	return $settings;
}


	/**
	 * Set default setting
	 *
	 * @since 1.0.0
	 */
	function set_default_setting() {

		$set_defaults = get_default_settings();

		foreach ( $set_defaults as $key => $value ) {
			if ( 'wpp_setting' === $key ) {
				update_option( $key, $value );
			}
			if ( 'wpp_player' === $key ) {
				update_option( $key, $value );
			}
			if ( 'wpp_feed' === $key ) {
				update_option( $key, $value );
			}
		}
	}