<?php
/**
 * Meta Field  for podcast
 *
 * @package Podcastify
 */

 // Add array of fields.
namespace Podcastify\Classes;

use function Podcastify\Utils\{ wpp_get_setting };
require_once 'class-meta-box-api.php';

if ( ! class_exists( 'Wpp_Meta_fields' ) ) :

	class Wpp_Meta_fields extends Meta_Box_Api {

		/**
		 * Meta fields.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $fields = [];

		/**
		 * Podcastify PostType slug
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $post_slug = 'episode';

		/**
		 * Podcastify taxonomy slug.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $taxonomy_slug = 'episode_series';

		public function __construct() {
			$this->fields = $this->set_meta_fields();
			
			if ( is_admin() ) {
				add_action( 'add_meta_boxes', [ $this, 'register_meta_box' ] );
				add_action( 'add_meta_boxes_' . $this->post_slug, [ $this, 'register_meta_box' ] );
				add_action( 'save_post', [ $this, 'save_meta_box' ], 10, 2 );
			}
		}

		/**
		 * Set the meta field setting
		 *
		 * @since 1.0.0
		 * @return array meta setting array.
		 */
		public function set_meta_fields():array {
			$meta_fields = [
				'episode_type'  => [
					'label'             => __( 'Episode type:', 'podcastify' ),
					'desc'              => __( 'The type of podcast episode - either Audio or Video', 'podcastify' ),
					'type'              => 'radio',
					'default'           => 'audio',
					'options'           => [
						'audio' => __( 'Audio', 'podcastify' ),
						'video' => __( 'Video', 'podcastify' ),
					],
					'sanitize_callback' => 'sanitize_text_field',
					'section'           => 'setting',
				],
				'podcast_file'  => [
					'label'              => __( 'Podcast file:', 'podcastify' ),
					'desc'               => __( 'Upload the primary podcast file or paste the file URL here.', 'podcastify' ),
					'type'               => 'file',
					'upload_frame_label' => __( 'file', 'seriously-simple-podcasting' ),
					'sanitize_callback'  => 'sanitize_url',
					'section'            => 'setting',
				],
				'duration'      => [
					'label'             => __( 'Duration:', 'podcastify' ),
					'desc'              => __( 'Duration of podcast file for display.', 'podcastify' ),
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field',
					'section'           => 'setting',
				],
				'filesize'      => [
					'label'             => __( 'File size:', 'podcastify' ),
					'desc'              => __( 'Size of the podcast file for display.', 'podcastify' ),
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field',
					'section'           => 'setting',
				],
				'date_recorded' => [
					'label'             => __( 'Date recorded:', 'podcastify' ),
					'desc'              => __( 'The date on which this episode was recorded.', 'podcastify' ),
					'type'              => 'date',
					'sanitize_callback' => 'sanitize_text_field',
					'section'           => 'setting',
				],
				'explicit'      => [
					'label'   => __( 'Explicit:', 'podcastify' ),
					'desc'    => __( 'Mark this episode as explicit.', 'podcastify' ),
					'type'    => 'select',
					'section' => 'setting',
					'sanitize_callback' => 'sanitize_text_field',
					'default' => 'no',
					'options' => [
						'yes'   => __( 'Yes', 'podcastify' ),
						'no'    => __( 'No', 'podcastify' ),
						'clean' => __( 'Clean', 'podcastify' ),
					],
				],
				'feed_block'    => [
					'label'             => __( 'Block:', 'podcastify' ),
					'desc'              => __( 'Block this episode from appearing in the iTunes & Google Play podcast libraries.', 'podcastify' ),
					'type'              => 'checkbox',
					'sanitize_callback' => 'sanitize_text_field',
					'section'           => 'setting',

				],
			];

			$itune_fields = [
				'itunes_episode_number' => [
					'label'             => __( 'iTunes Episode Number:', 'podcastify' ),
					'desc'              => __( 'The iTunes Episode Number. Leave Blank If None.', 'podcastify' ),
					'type'              => 'number',
					'sanitize_callback' => 'sanitize_text_field',
					'section'           => 'itune',
				],
				'itunes_season_number'  => [
					'label'             => __( 'iTunes Season Number:', 'podcastify' ),
					'desc'              => __( 'The iTunes Season Number. Leave Blank If None.', 'podcastify' ),
					'type'              => 'number',
					'sanitize_callback' => 'sanitize_text_field',
					'section'           => 'itune',
				],
				'itunes_episode_type'   => [
					'label'             => __( 'iTunes Episode Type:', 'podcastify' ),
					'desc'              => '',
					'type'              => 'select',
					'default'           => '',
					'options'           => [
						''        => __( 'Please Select', 'podcastify' ),
						'full'    => __( 'Full: For Normal Episodes', 'podcastify' ),
						'trailer' => __( 'Trailer: Promote an Upcoming Show', 'podcastify' ),
						'bonus'   => __( 'Bonus: For Extra Content Related To a Show', 'podcastify' ),
					],
					'sanitize_callback' => 'sanitize_text_field',
					'section'           => 'itune',
				],
				'itunes_title'          => [
					'label'             => __( 'iTunes Episode Title (Exclude Your Series / Show Number):', 'podcastify' ),
					'desc'              => __( 'The iTunes Episode Title. NO Series / Show Number Should Be Included.', 'podcastify' ),
					'type'              => 'text',
					'sanitize_callback' => 'sanitize_text_field',
					'section'           => 'itune',
				],
			];

			$is_itune_enabled = wpp_get_setting( 'itune_fields', 'wpp_setting' );
			// Is itune field enabled.
			if ( 'on' === $is_itune_enabled ) {
				$meta_fields = array_merge( $meta_fields, $itune_fields );
			}

			return apply_filters( 'wpp_episode_meta_fields', $meta_fields );

		}


		/**
		 * Register meta box.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function register_meta_box() {
			$screen = get_current_screen();
			
			// Only register for our post type
			if ( $screen && $screen->post_type === $this->post_slug ) {
				add_meta_box(
					'wpp-episode-meta',
					__( 'Episode Settings', 'podcastify' ),
					[ $this, 'render_meta_box' ],
					$this->post_slug,
					'advanced',
					'default'
				);
				do_action( 'wpp_episode_meta_boxes' );
			}
		}

		/**
		 * Render meta box call back of the register meta box.
		 *
		 * @since 1.0.0
		 * @param object $post current post object.
		 * @return void
		 */
		public function render_meta_box( $post ) {
			// Add nonce for security and authentication.
			wp_nonce_field( 'wpp_episode_setting', 'wpp_episode_meta_nonce' );
			// Use get_post_meta to retrieve an existing value from the database.
			// Display the form, using the current value.
			?>
			<div class="meta_box_wsprig wppfy-setting-wrapper wppfy-meta-box-wrapper">
				<?php
				foreach ( $this->fields as $field_id => $args ) {

						echo "<div class='meta-field-wrapper $field_id field-{$args['type']}'>";

					if ( metadata_exists( 'post', $post->ID, $field_id ) ) {
						$args['value'] = get_post_meta( $post->ID, $field_id, true );
					} else {
						  $args['value'] = $args['default'] ?? '';
					}
						$args['name'] = $field_id;
						echo call_user_func( [ $this, "callback_{$args['type']}" ], $args );
						echo '</div>';

				}
				?>
			</div>
			<?php

		}

		/**
		 * Handles saving the meta box.
		 *
		 * @since 1.0.0
		 * @param int     $post_id Post ID.
		 * @param WP_Post $post    Post object.
		 * @return void
		 */
		public function save_meta_box( $post_id, $post ) {
			// Check if this is the correct post type
			if ( $post->post_type !== $this->post_slug ) {
				return;
			}
			
			// Add nonce for security and authentication.
			$nonce_name   = $_POST['wpp_episode_meta_nonce'] ?? '';
			$nonce_action = 'wpp_episode_setting';

			// Check if nonce is valid.
			if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
				return;
			}

			// Check if user has permissions to save data.
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			// Check if not an auto save.
			if ( wp_is_post_autosave( $post_id ) ) {
				return;
			}

			// Check if not a revision.
			if ( wp_is_post_revision( $post_id ) ) {
				return;
			}

			// Update the meta field.
			foreach ( $this->fields as $field => $args ) {
				$value = isset( $_POST[ $field ] ) ? wp_unslash( $_POST[ $field ] ) : '';

				if ( isset( $args['sanitize_callback'] ) && is_callable( $args['sanitize_callback'] ) ) {
					$value = call_user_func( $args['sanitize_callback'], $value );
				} else {
					$value = sanitize_text_field( $value );
				}
				update_post_meta( $post_id, $field, $value );
			}
		}

	}
endif; // class exist

// Initialize the class immediately to ensure meta boxes are registered
global $wpp_meta_fields;
$wpp_meta_fields = new Wpp_Meta_fields();
