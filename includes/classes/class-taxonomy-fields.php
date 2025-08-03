<?php
/**
 * Taxonomy  for podcast
 *
 * @package Podcastify
 */
namespace Podcastify\Classes;

use function Podcastify\Utils\{wpp_home_url, get_series_feed};
if ( ! class_exists( 'TaxonomyFields' ) ) :

	class TaxonomyFields {

		public $tax_slug = 'episode_series';

		public function __construct() {

			// add_action( 'episode_series_add_form_fields', [ $this, 'add_series_term_meta_form' ], 15 );
			// add_action( 'episode_series_edit_form_fields', [ $this, 'edit_series_term_meta_form' ], 15, 2 );
			// add_action( 'created_episode_series', [ $this, 'save_episode_series_meta' ], 12, 2 );
			// add_action( 'edited_episode_series', [ $this, 'save_episode_series_meta' ], 12, 2 );

			// add_filter( 'term_updated_messages', [ $this, 'change_update_massage' ], 15 );
			add_filter( 'manage_edit-episode_series_columns', [ $this, 'edit_episode_series_columns' ] );
			add_filter( 'manage_episode_series_custom_column', [ $this, 'add_episode_series_columns' ], 10, 3 );

		}

		/**
		 * Save or Update if meta already exist.
		 *
		 * @since 1.0.0
		 * @param int $term_id  Term ID.
		 * @param int $tt_id Term Taxonomy ID.
		 * @return void
		 */
		public function save_episode_series_meta( $term_id, $tt_id ) {

			$meta_id       = $this->tax_slug . '_image';
			$prev_media_id = get_term_meta( $term_id, $meta_id, true );
			$media_id      = sanitize_title( $_POST[ $meta_id ] );
			update_term_meta( $term_id, $meta_id, $media_id, $prev_media_id );
		}

		/**
		 * Add meta field form.
		 *
		 * @since 1.0.0
		 * @param string $taxonomyThe taxonomy slug.
		 * @return void
		 */
		public function add_series_term_meta_form( $taxonomy ) {
			$this->series_image_upload( $taxonomy );
		}

		/**
		 * Render edit meta field form.
		 *
		 * @since 1.0.0
		 * @param object $term  Current taxonomy term object..
		 * @param int    $taxonomy Current taxonomy slug.
		 * @return void
		 */
		public function edit_series_term_meta_form( $term, $taxonomy ) {
			$this->series_image_upload( $taxonomy, $term );
		}

		/**
		 * Series image handler
		 *
		 * @param slug        $taxonomy $taxonomy Current taxonomy slug.
		 * @param object|NULL $term  Current taxonomy term object.
		 * @return void
		 */
		public function series_image_upload( $taxonomy, $term = null ) {
			$image_field = $this->tax_slug . '_image';
			// Define a default image.
			$default_image = esc_url( WP_PODCASTIFY_URL . 'assets/images/placeholder.jpg' );
			$media_id      = '';
			if ( ! is_null( $term ) ) {
				$media_id = get_term_meta( $term->term_id, $image_field, true );
			}
			$image_width      = apply_filters( 'episode_series_field_image_width', 'auto' );
			$image_height     = apply_filters( 'episode_series_field_image_height', 'auto' );
			$is_default_image = true;
			$hidden_class     = 'hidden';
			if ( ! empty( $media_id ) ) {
				$image_attributes = wp_get_attachment_image_src( $media_id, array( $image_width, $image_height ) );
				$src              = $image_attributes[0];
				$is_default_image = false;
				$hidden_class     = '';
			} else {
				$src = $default_image;
			}

			$series_img_title      = __( 'Series Image', 'podcastify' );
			$upload_btn_text       = __( 'Choose series image', 'podcastify' );
			$upload_btn_value      = __( 'Add Image', 'podcastify' );
			$upload_btn_title      = __( 'Choose an image file', 'podcastify' );
			$series_img_desc       = __( 'Set an image as the artwork for the series. No image will be set if not provided.', 'podcastify' );
			$series_img_form_label = "<label>{$series_img_title}</label>";

			$series_img_form_fields = "<div class='{$taxonomy}_image_preview_wrap'><img id='{$taxonomy}_image_preview' data-src='{$default_image}' src='$src' width='{$image_width}' height='{$image_height}' /><button id='{$taxonomy}_remove_image_button' class='button {$hidden_class}'>&times;</button></div>
																<div>
																	<input type='hidden' id='{$taxonomy}_image_id' name='{$image_field}' value='{$media_id}' />
																	<button id='{$taxonomy}_upload_image_button' class='button wppfy-button' data-upload-title='{$upload_btn_title}' data-upload-button-text='{$upload_btn_text}' type='button'>
																	</span> {$upload_btn_value}
																	</button>
																	
																</div>
																<p class='description'>{$series_img_desc}</p>";
			if ( is_null( $term ) ) {

				echo "<div class='form-field term-upload-wrap'>
					{$series_img_form_label}
					{$series_img_form_fields}
				</div>";
			} else {

				echo "<tr class='form-field term-upload-wrap'>
				<th scope='row'>{$series_img_form_label}</th>
				<td>
					{$series_img_form_fields}
				</td>
			</tr>";

			}

		}

		/**
		 * Change series updated message.
		 *
		 * @since 1.0.0
		 * @param array $message the message to be displayed.
		 * @return array
		 */
		public function change_update_massage( $messages ) {
			$messages['episode_series'] = [
				0 => '',
				1 => __( 'Series added.', 'podcastify' ),
				2 => __( 'Series deleted.', 'podcastify' ),
				3 => __( 'Series updated.', 'podcastify' ),
				4 => __( 'Series not added.', 'podcastify' ),
				5 => __( 'Series not updated.', 'podcastify' ),
				6 => __( 'Series deleted.', 'podcastify' ),
			];

				return $messages;
		}

		/**
		 * Add or modify taxonomy list table columns
		 *
		 * @since 1.0.0
		 * @param array $columns Default columns
		 * @return array $columns update columns
		 */
		public function edit_episode_series_columns( $columns ) {

			unset( $columns['description'] );

			// $columns['series_image']    = __( 'Series Image', 'podcastify' );
			$columns['posts']           = __( 'Episodes', 'podcastify' );
			$columns['series_feed_url'] = __( 'Series feed URL', 'podcastify' );
			$columns                    = apply_filters( 'wpp_episode_series_admin_columns', $columns );

			return $columns;
		}

		/**
		 * Add taxonomy value to custom columns.
		 *
		 * @since 1.0.0
		 * @param string $data default data.
		 * @param string $column_name Name of the column.
		 * @param int    $term_id Term ID.
		 * @return string $data value of the column.
		 */
		public function add_episode_series_columns( $data, $column_name, $term_id ) {

			if ( 'series_image' === $column_name ) {
				$series           = get_term( $term_id, $this->tax_slug );
				$default_image    = esc_url( WP_PODCASTIFY_URL . 'assets/images/placeholder.jpg' );
				$media_id         = get_term_meta( $term_id, $this->tax_slug . '_image', true );
				$image_attributes = wp_get_attachment_image_src( $media_id );
				$source           = ( ! is_null( $image_attributes[0] ) ) ? $image_attributes[0] : $default_image;
				$data             = "<img id='{$this->tax_slug}_image' src='{$source}' width='100%' height='auto' style='max-width:50px;' />";

			} elseif ( 'series_feed_url' === $column_name ) {

				$feed_url = get_series_feed( $term_id );

				// $data = '<a href="' . esc_attr( $feed_url ) . '" target="_blank">' . esc_html( $feed_url ) . '</a>';

				$data = '<input type="text" readonly class="podcastify-feed-url" value="' . esc_url( $feed_url ) . '"><span  data="' . __( ' Copied', 'podcastify' ) . '" class="copy-feed-url" > ' . __( 'Copy', 'podcastify' ) . '</span>';
			}

			return $data;
		}

	}
endif; // class exist.

// Initialize the class on init hook to ensure translations are loaded
add_action( 'init', function() {
	global $series_taxonomy_fields;
	$series_taxonomy_fields = new TaxonomyFields();
}, 5 );
