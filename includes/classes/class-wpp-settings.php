<?php
/**
 * Admin plugin functionality.
 *
 * @package Podcastify
 */

namespace Podcastify\Classes;

use function Podcastify\Utils\{wpp_get_podcast_categories, wpp_get_podcast_sub_categories, wpp_podcast_langs, wpp_get_setting};
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'Wpp_Settings' ) ) :
	/**
	 * Admin class for admin functionality.
	 *
	 * @since 1.0.0
	 */
	class Wpp_Settings {

		/**
		 * Setting api object.
		 *
		 * @since 1.0.0
		 * @access public
		 * @var object
		 */
		public $setting_api;

		/**
		 * Constructor of the class.
		 */
		public function __construct() {

			// $this->includes();
			$this->setting_api = new Wpp_Setting_API();
			add_action( 'admin_init', [ $this, 'admin_init' ] );
			add_action( 'admin_menu', [ $this, 'admin_menu' ] );

		}

		/**
		 * Include assets.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function includes() {

		}

		/**
		 * Add menu to the plugin.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function admin_menu() {
			add_submenu_page(
				'edit.php?post_type=episode',
				__( 'Settings', 'podcastify' ),
				__( 'Settings', 'podcastify' ),
				'manage_options',
				'podcastify-setting',
				[ $this->setting_api, 'plugin_page' ],
				11
			);
		}

		/**
		 * Section and setting api.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function admin_init() {

			// Set setting sections.
			$this->setting_api->set_sections( $this->get_sections() );
			// Set setting fields.
			$this->setting_api->set_fields( $this->get_fields() );
			// Setting api init.
			$this->setting_api->admin_init();

			$this->set_new_feed_url();
		}

		/**
		 * Set new feed url data in options.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function set_new_feed_url() {

			$episode_series = [
				(object) [
					'term_id' => '',
					'name'    => 'default',
				],
			];

			if ( ! empty( $e_series = $this->get_episode_series() ) ) {
				$episode_series = array_merge( $episode_series, $e_series );
			}

			foreach ( $episode_series as $series ) {

				$series_id = '';

				if ( $series->term_id ) {
					$series_id = '_' . $series->term_id;
				}
					$new_feed_url = wpp_get_setting( 'new_feed_url' . $series_id, 'wpp_feed' );
				if ( $new_feed_url ) {

					$feed_redirect_data = get_option( 'wpp_redirection_feed' . $series_id );
					if ( ! $feed_redirect_data || ( isset( $feed_redirect_data['new_url'] ) && $feed_redirect_data['new_url'] !== $new_feed_url ) ) {
						$data = apply_filters(
							'wpp_series_expiration_data',
							[
								'time'    => \time(),
								'new_url' => $new_feed_url,
							],
							$series->term_id
						);
						update_option( 'wpp_redirection_feed' . $series_id, $data );
					}
				}
			}
		}
		/**
		 * Get setting section.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		public function get_sections():array {
			$sections = [
				[
					'id'    => 'wpp_setting',
					'title' => __( 'Settings', 'podcastify' ),
				],
				[
					'id'       => 'wpp_feed',
					'title'    => __( 'Feed Details', 'podcastify' ),
					'callback' => [ $this, 'section_callback' ],
				],
				[
					'id'    => 'wpp_player',
					'title' => __( 'Player', 'podcastify' ),
					'desc'  => __( 'Player related settings ', 'podcastify' ),
				],
			];
			return apply_filters( 'wpp_setting_sections', $sections );
		}


		public function section_callback( $args ) {

			echo sprintf( '<p>%s</p>', __( 'Your series feed details.', 'podcastify' ) );
			$episode_series = $this->get_episode_series();

			$html = '<h2 class="series-tab-wrapper">';
			if ( ! empty( $episode_series ) ) {
				$html .= sprintf( '<a href="#%1$s" data-slug="default" class="nav-tab default nav-tab-active" id="series-tab-%1$s">%2$s <span class="dashicons dashicons-rss"></span></a>', 'default', __( 'Default', 'podcastify' ) );

				foreach ( $episode_series as $series ) {
					$html .= sprintf( '<a href="#%1$s" data-slug="%1$s" class="nav-tab series" id="series-tab-%2$s">%3$s</a>', $series->slug, $series->term_id, $series->name );
				}
			}
			$html .= '</h2>';
			echo $html;

		}

		/**
		 * Setting fields.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		public function get_fields():array {

			$feed_fields    = $this->podcast_feed_fields();
			$episode_series = $this->get_episode_series();
			if ( ! empty( $episode_series ) ) {
				$_feed_fields = $feed_fields;
				foreach ( $episode_series as $id => $series ) {
					foreach ( $_feed_fields as $_id => $field ) {
						if ( isset( $field['s_default'] ) && ! true == $field['s_default'] ) {
							unset( $field['default'] );
						}
						$field['id']    = $field['id'] . '_' . $series->term_id;
						$field['class'] = $series->slug;
						$feed_fields[]  = $field;
					}
				}
			}

			// unset exclude_in_feed form default feed settigs.
			if ( isset( $feed_fields[18] ) && 'exclude_in_feed' === $feed_fields[18]['id'] ) {
				unset( $feed_fields[18] );
			}

			$setting_fields = [
				'wpp_setting' => [
					[
						'id'      => 'player_location',
						'type'    => 'multicheck',
						'name'    => __( 'Podcast  Player Location', 'podcastify' ),
						'desc'    => __( 'Where you want to show podcast media player.', 'podcastify' ),
						'default' => 'the_content',
						'options' => [
							'the_content'    => __( 'Full Content', 'podcastify' ),
							'the_excerpt'    => __( 'Excerpt', 'podcastify' ),
							'oembed_excerpt' => __( 'OEmbed Excerpt', 'podcastify' ),
						],
					],
					[
						'id'      => 'player_position',
						'type'    => 'radio',
						'name'    => __( 'PodCast Player Position', 'podcastify' ),
						'desc'    => __( 'Position player of the where  you want to show podcast player.', 'podcastify' ),
						'default' => 'above_content',
						'options' => [
							'above_content' => __( 'Above the Content', 'podcastify' ),
							'below_content' => __( 'Below the Content', 'podcastify' ),
						],
					],
					[
						'id'      => 'player_visibility',
						'type'    => 'radio',
						'name'    => __( 'PodCast Player Visibility', 'podcastify' ),
						'default' => 'all_users',
						'desc'    => __(
							'Whether to display the media player to everybody or only logged in users.',
							'podcastify'
						),
						'options' => [
							'all_users'    => __( 'All Users', 'podcastify' ),
							'logged_users' => __( 'Logged in Users', 'podcastify' ),
						],
					],
					[
						'id'      => 'itune_fields',
						'type'    => 'checkbox',
						'name'    => __( 'Itune Fields ', 'podcastify' ),
						'default' => 'on',
						'desc'    => __(
							'Enable iTunes iOS11 specific fields on each episode.
						',
							'podcastify'
						),
					],
				],
				'wpp_feed'    => $feed_fields,
				'wpp_player'  => [
					[
						'id'      => 'player_type',
						'type'    => 'radio',
						'name'    => __( 'Player Type', 'podcastify' ),
						'desc'    => __( 'Choose the player style you want to use.', 'podcastify' ),
						'default' => 'single',
						'options' => [
							'single'  => __( 'Default Player', 'podcastify' ),
							'minimal' => __( 'Minimal Player', 'podcastify' ),
						],
					],
					[
						'id'      => 'show_meta',
						'type'    => 'checkbox',
						'name'    => __( 'Show Player Meta ', 'podcastify' ),
						'default' => 'on',
					],
					[
						'id'      => 'player_meta',
						'type'    => 'multiselect',
						'name'    => __( 'Player Meta', 'podcastify' ),
						'desc'    => __( 'Select Player Meta You want to show on player.', 'podcastify' ),
						'default' => [ 'download', 'like', 'share' ],
						'options' => [
							'download'  => __( 'Download', 'podcastify' ),
							'like'      => __( 'Like', 'podcastify' ),
							'share'     => __( 'Share', 'podcastify' ),
							'subscribe' => __( 'Subscribe', 'podcastify' ),
						],
					],
					[
						'id'      => 'share_network',
						'type'    => 'multiselect',
						'name'    => __( 'Share Network', 'podcastify' ),
						'desc'    => __( 'Share networks social sharing', 'podcastify' ),
						'default' => [ 'facebook', 'linkedin', 'twitter' ],
						'options' => [
							'facebook' => __( 'Facebook', 'podcastify' ),
							'linkedin' => __( 'Linkedin', 'podcastify' ),
							'twitter'  => __( 'Twitter', 'podcastify' ),
						],
					],
					[
						'id'      => 'control_color',
						'type'    => 'color',
						'name'    => __( 'Player Control Color', 'podcastify' ),
						'desc'    => __( 'Progress bar, volume, play and pause color.', 'podcastify' ),
						'default' => '#fff',
					],
					[
						'id'      => 'background_color',
						'type'    => 'color',
						'name'    => __( 'Player Background Color ', 'podcastify' ),
						'desc'    => __( 'Player background color.', 'podcastify' ),
						'default' => '#fff',
					],
				],
			];

			return apply_filters( 'wpp_setting_fields', $setting_fields );
		}

		public function podcast_feed_fields():array {

			$podcast_categories     = wpp_get_podcast_categories();
			$podcast_sub_categories = wpp_get_podcast_sub_categories();

			$podcast_categories_options = [ 'none' => __( ' -- None --' ) ];

			foreach ( $podcast_categories as $cat_id => $category ) {
				$podcast_categories_options[ $cat_id ] = $category;
				if ( isset( $podcast_sub_categories[ $cat_id ] ) ) {
					foreach ( $podcast_sub_categories[ $cat_id ] as $sub_cat_id => $sub_category ) {
						$podcast_categories_options[ $cat_id . ':' . $sub_cat_id ] = '&mdash; ' . $sub_category;
					}
				}
			}

			$podcast_languages_option = wpp_podcast_langs();

			$feed_fields = [
				[
					'id'                => 'title',
					'type'              => 'text',
					'name'              => __( 'Title', 'podcastify' ),
					'class'             => 'default',
					'desc'              => __( 'Your podcast title.', 'podcastify' ),
					'default'           => get_bloginfo( 'name' ),
					'placeholder'       => get_bloginfo( 'name' ),
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'id'                => 'subtitle',
					'type'              => 'text',
					'name'              => __( 'Subtitle', 'podcastify' ),
					'class'             => 'default',
					'default'           => get_bloginfo( 'description' ),
					'placeholder'       => get_bloginfo( 'description' ),
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'id'                => 'author',
					'type'              => 'text',
					'name'              => __( 'Author', 'podcastify' ),
					'class'             => 'default',
					'desc'              => __( 'Your podcast author', 'podcastify' ),
					'default'           => get_bloginfo( 'name' ),
					'placeholder'       => get_bloginfo( 'name' ),
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'id'                => 'summary',
					'type'              => 'textarea',
					'name'              => __( 'Summary', 'podcastify' ),
					'class'             => 'default',
					'desc'              => __( 'Your description/summary of the podcast.', 'podcastify' ),
					'default'           => get_bloginfo( 'description' ),
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'id'    => 'podcast_cover',
					'type'  => 'image',
					'name'  => __( 'Cover Image		', 'podcastify' ),
					'desc'  => __( 'Your podcast cover image must have a minimum 1400px x 1400px..', 'podcastify' ),
					'class' => 'default',
				],
				[
					'id'                => 'keywords',
					'type'              => 'text',
					'name'              => __( 'Key Words', 'podcastify' ),
					'class'             => 'default',
					'desc'              => __( 'Comma-separated keywords to help people find your podcast.', 'podcastify' ),
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'id'      => 'category_level_1',
					'type'    => 'select',
					'name'    => __( 'Category	1', 'podcastify' ),
					'desc'    => __( 'Your podcast primary category.', 'podcastify' ),
					'class'   => 'default',
					'options' => $podcast_categories_options,
				],
				[
					'id'      => 'category_level_2',
					'type'    => 'select',
					'name'    => __( 'Category	2', 'podcastify' ),
					'desc'    => __( 'Your podcast secondary category.', 'podcastify' ),
					'class'   => 'default',
					'options' => $podcast_categories_options,
				],
				[
					'id'      => 'category_level_3',
					'type'    => 'select',
					'name'    => __( 'Category	3', 'podcastify' ),
					'desc'    => __( 'Your podcast tertiary category.', 'podcastify' ),
					'class'   => 'default',
					'options' => $podcast_categories_options,
				],
				[
					'id'                => 'author_name',
					'type'              => 'text',
					'name'              => __( 'Owner/Author Name', 'podcastify' ),
					'class'             => 'default',
					'desc'              => __( 'Podcast owner name.', 'podcastify' ),
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'id'    => 'author_email',
					'type'  => 'email',
					'name'  => __( 'Owner/Author Email', 'podcastify' ),
					'class' => 'default email',
					'desc'  => __( 'Podcast owner email.', 'podcastify' ),
				],
				[
					'id'        => 'podcast_language',
					'type'      => 'select',
					'name'      => __( 'Podcast/Feed Language', 'podcastify' ),
					'class'     => 'default',
					'default'   => 'en-us',
					'options'   => $podcast_languages_option,
					's_default' => true,
				],
				[
					'id'      => 'podcast_explicit',
					'type'    => 'select',
					'name'    => __( 'Explicit', 'podcastify' ),
					'class'   => 'default',
					'default' => 'no',
					'options' => [
						'yes'   => __( 'Yes', 'podcastify' ),
						'no'    => __( 'No', 'podcastify' ),
						'clean' => __( 'Clean', 'podcastify' ),
					],
				],
				[
					'id'    => 'podcast_complete',
					'type'  => 'checkbox',
					'name'  => __( 'Completed', 'podcastify' ),
					'desc'  => __( 'Check if this podcast is completed and not  more episodes are going to be added to this feed.', 'podcastify' ),
					'class' => 'default',
				],
				[
					'id'          => 'podcast_copy_right',
					'type'        => 'text',
					'name'        => __( 'Copyright / License information', 'podcastify' ),
					'class'       => 'default',
					'placeholder' => '&#xA9; ' . date( 'Y' ) . ' ' . get_bloginfo( 'name' ),
				],
				[
					'id'      => 'publish_date',
					'type'    => 'select',
					'name'    => __( 'Publish date', 'podcastify' ),
					'desc'    => __(
						'Use the "Published date" of the post or "Recorded Date" from the Podcast episode.
					',
						'podcastify'
					),
					'class'   => 'default',
					'default' => 'publish_date',
					'options' => [
						'publish_date'  => __( 'Publish Date', 'podcastify' ),
						'recorded_date' => __( 'Recorded Date', 'podcastify' ),
					],
				],
				[
					'id'      => 'show_type',
					'type'    => 'select',
					'name'    => __( 'Show Type', 'podcastify' ),
					'class'   => 'default',
					'default' => '',
					'options' => apply_filters(
						'wpp_show_types',
						[
							''         => __( 'Select show type', 'podcastify' ),
							'episodic' => __( 'Episodic', 'podcastify' ),
							'serial'   => __( 'Serial', 'podcastify' ),
						]
					),
				],
				[
					'id'      => 'feed_description',
					'type'    => 'select',
					'name'    => __( 'Episode Description', 'podcastify' ),
					'class'   => 'default',
					'default' => 'excerpt',
					'options' => [
						'content' => __( 'Content', 'podcastify' ),
						'excerpt' => __( 'Excerpt', 'podcastify' ),
					],
				],
				[
					'id'    => 'exclude_in_feed',
					'type'  => 'checkbox',
					'name'  => __( 'Exclude form default Feed', 'podcastify' ),
					'class' => 'default',
				],
				[
					'id'    => 'lighten',
					'type'  => 'checkbox',
					'name'  => __( 'Lighten Feed', 'podcastify' ),
					'desc'  => __( 'If you have 100+ episodes with transcription. You can enable lighten feed. <a href="#">more info</a>' ),
					'class' => 'default',
				],
				[
					'id'                => 'new_feed_url',
					'type'              => 'url',
					'name'              => __( 'New Feed URL', 'podcastify' ),
					'desc'              => __( ' If you set new feed URL after 24hour gone we will redirect new feed url. ', 'podcastify' ),
					'placeholder'       => 'http://example.com',
					'class'             => 'default',
					'sanitize_callback' => 'esc_url_raw',
				],
				[
					'id'                => 'itune_feed_url',
					'type'              => 'url',
					'name'              => __( 'ITunes Feed URL', 'podcastify' ),
					'desc'              => __( 'Your ITunes Podcast\'s Feed URL.', 'podcastify' ),
					'placeholder'       => 'http://example.com',
					'class'             => 'default',
					'sanitize_callback' => 'esc_url_raw',
				],
				[
					'id'                => 'google_play_feed_url',
					'type'              => 'url',
					'name'              => __( 'Google Play Feed URL', 'podcastify' ),
					'desc'              => __( 'Your Google Play Podcast\'s  Feed URL.', 'podcastify' ),
					'placeholder'       => 'http://example.com',
					'class'             => 'default',
					'sanitize_callback' => 'esc_url_raw',
				],
				[
					'id'                => 'spotify_feed_url',
					'type'              => 'url',
					'name'              => __( 'Spoitfy Feed URL', 'podcastify' ),
					'desc'              => __( 'Spoitfy Podcast\'s  Feed URL', 'podcastify' ),
					'placeholder'       => 'http://example.com',
					'class'             => 'default',
					'sanitize_callback' => 'esc_url_raw',
				],
				[
					'id'                => 'stitcher_feed_url',
					'type'              => 'url',
					'name'              => __( 'Stitcher Feed URL', 'podcastify' ),
					'desc'              => __( 'Stitcher Podcast\'s  Feed URL', 'podcastify' ),
					'placeholder'       => 'http://example.com',
					'class'             => 'default',
					'sanitize_callback' => 'esc_url_raw',
				],

			];
			return apply_filters( 'wpp_feed_fields', $feed_fields );
		}


		/**
		 * Get Episode series.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		public function get_episode_series() {

			return get_terms( 'episode_series', apply_filters( 'wpp_feed_series', [ 'hide_empty' => false ] ) );
		}
	}//end class

endif;
// Initialize the class on init hook to ensure translations are loaded
add_action( 'init', function() {
	global $wpp_settings;
	$wpp_settings = new Wpp_Settings();
}, 5 );
// echo "<pre>";
// var_dump( get_option( 'wpp_setting' ) );
// echo "</pre>";
// die;



