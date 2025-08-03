<?php
/**
 * Ajax call controller.
 *
 * @package Podcastify
 */

namespace Podcastify\classes;

use function Podcastify\Utils\{ dd, wpp_get_setting, wpp_get_episode_meta };

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'Wpp_Ajax' ) ) :
	/**
	 * Admin class for admin functionality.
	 *
	 * @since 1.0.0
	 */
	class Wpp_Ajax {


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



		public function hooks() {
			$ajax_calls = [
				'episode_download' => true,
				'episode_like'     => true,
			];

			foreach ( $ajax_calls as $ajax_call => $no_priv ) {
				add_action( 'wp_ajax_podcastify_' . $ajax_call, [ $this, $ajax_call ] );
				if ( $no_priv ) {
					add_action( 'wp_ajax_nopriv_podcastify_' . $ajax_call, [ $this, $ajax_call ] );
				}
			}
		}

		/**
		 * set plugins setting in member function.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function set_setting() {
			$this->settings = get_option( 'wpp_setting' );
		}

		/**
		 * Increase the download counter for episode.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function episode_download() {
			check_ajax_referer( 'podcastify_security', 'security' );
			$episode_id        = (int) $_POST['episodeID'];
			$episode_download  = wpp_get_episode_meta( 'download_count', $episode_id, 0 );
			$episode_download += 1;
			update_post_meta( $episode_id, 'download_count', $episode_download );
			wp_send_json_success(
				[
					'episodeID' => $episode_id,
					'downloads' => $episode_download,
				]
			);
		}

		/**
		 * Increase the download counter for episode.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function episode_like() {
			check_ajax_referer( 'podcastify_security', 'security' );
			$episode_id        = (int) $_POST['episodeID'];
			$episode_download  = wpp_get_episode_meta( 'like_count', $episode_id, 0 );
			$episode_download += 1;
			update_post_meta( $episode_id, 'like_count', $episode_download );
			wp_send_json_success(
				[
					'episodeID' => $episode_id,
					'likes'     => $episode_download,
				]
			);
		}

	}//end class

endif;
// Initialize the class on init hook to ensure translations are loaded
add_action( 'init', function() {
	global $wpp_ajax;
	$wpp_ajax = new Wpp_Ajax();
}, 5 );



