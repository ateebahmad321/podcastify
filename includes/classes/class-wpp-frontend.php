<?php
/**
 * Frontend plugin functionality.
 *
 * @package Podcastify
 */

namespace Podcastify\classes;

use function Podcastify\Utils\{ dd, wpp_get_setting, wpp_get_episode_enclosure_link, wpp_podcast_player, wpp_get_episode_meta, wpp_readfile_chunked };

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'Wpp_Frontend' ) ) :
	/**
	 * Admin class for admin functionality.
	 *
	 * @since 1.0.0
	 */
	class Wpp_FrontEnd {


		/**
		 * Constructor of the class.
		 */
		public function __construct() {

			$this->set_setting();
			$this->hooks();
		}

		/**
		 * Plugin main functionality settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $settings;

		/**
		 * Player position in post.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $player_position;

		public function hooks() {

			add_action( 'wp', [ $this, 'download_file' ] );
			add_filter( 'wpp_player_visibility', [ $this, 'wpp_before_adding_player' ] );

			$this->add_player();
		}

		public function add_player() {
			$player_locations = wpp_get_setting( 'player_location', 'wpp_setting' );

			if ( ! empty( $player_locations ) ) {

				foreach ( $player_locations as $key => $location ) {
					// die( $location );
					add_filter( $location, [ $this, 'add_podcast_player' ] );
				}
			}

		}

		/**
		 * Podcast player location hook callback.
		 *
		 * @param string $content
		 * @return string
		 */
		public function add_podcast_player( $content ): string {

			$episode_enclosure_link = wpp_get_episode_meta( 'podcast_file', get_the_ID() );
			$player_visibility      = apply_filters( 'wpp_player_visibility', true );
			if ( ! trim( $episode_enclosure_link ) || ! $player_visibility ) {
				return $content;
			}
			$player_content = wpp_podcast_player( get_the_ID() );
			if ( 'above_content' === $this->player_position ) {
				return $player_content . $content;
			}

			return $content . $player_content;
		}

		/**
		 * set plugins setting in member function.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function set_setting() {

			$this->settings        = get_option( 'wpp_setting' );
			$this->player_position = wpp_get_setting( 'player_position', 'wpp_setting' );

		}


		/**
		 * Download podcast file.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function download_file() {
			global $wp_query;
			if ( isset( $wp_query->query_vars['podcast_episode'] ) && ! empty( $wp_query->query_vars['podcast_episode'] ) ) {

				$episode_id = intval( $wp_query->query_vars['podcast_episode'] );

				$is_podcast_download = apply_filters( 'wpp_is_podcast_download', true, $episode_id );
				if ( ! $is_podcast_download || ! $episode_id ) {
					wp_redirect( $_SERVER['HTTP_REFERER'] );
				}

				$episode = get_post( $episode_id );

				$file_link = wpp_get_episode_enclosure_link( $episode_id, true );

				if ( ! is_object( $episode ) || is_wp_error( $episode ) ) {
					return;
				}
				do_action( 'wpp_file_download', $file_link, $episode_id );

				header( 'Pragma: no-cache' );
				header( 'Expires: 0' );
				header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
				header( 'Robots: none' );

				if ( isset( $_REQUEST['podcastify-ref'] ) && ! empty( $_REQUEST['podcastify-ref'] ) ) {
					$referrer =  esc_attr( $_REQUEST['podcastify-ref'] ); // Check ref for download or play.
				}

				if ( 'download' == $referrer ) {
					// Force file download.
					header( 'Content-Type: application/force-download' );

					// Set other relevant headers.
					header( 'Content-Description: File Transfer' );
					header( 'Content-Disposition: attachment; filename="' . basename( $file_link ) . '";' );
					header( 'Content-Transfer-Encoding: binary' );
					// Encode spaces until fixed (https://core.trac.wordpress.org/ticket/36998)
					$file_link = str_replace( ' ', '%20', $file_link );
					// Use wpp_readfile_chunked() if allowed on the server or simply access file directly
					@wpp_readfile_chunked( $file_link ) or header( 'Location: ' . $file_link );
				} else {
					// Encode spaces until fixed (https://core.trac.wordpress.org/ticket/36998)
					$file_link = str_replace( ' ', '%20', $file_link );
					wp_redirect( $file_link, 302 );
				}
				exit();

			}
		}

		/**
		 * Custom hook to show and hide player.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function wpp_before_adding_player( $is_visible ) {

			$player_visibility = wpp_get_setting( 'player_visibility', 'wpp_setting' );

			if ( 'logged_users' == $player_visibility && ! is_user_logged_in() ) {
				return false;
			}
			return $is_visible;
		}


	}//end class

endif;
// Initialize the class on init hook to ensure translations are loaded
add_action( 'init', function() {
	global $wpp_frontend;
	$wpp_frontend = new Wpp_Frontend();
}, 5 );



