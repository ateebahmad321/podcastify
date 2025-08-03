<?php
/**
 * Custom post type for podcast
 *
 * @package Podcastify
 */
namespace Podcastify\Classes;

if ( ! class_exists( 'PodcastPost' ) ) :
	class PodcastPost {

		public $post_slug = 'episode';
		public function __construct() {
			add_action( 'init', [ $this, 'register_post_type' ], 11 );
			require_once WP_PODCASTIFY_INC . '/classes/class-taxonomy-fields.php';
			require_once WP_PODCASTIFY_INC . '/classes/class-wpp-meta-fields.php';
		}

		/**
		 * Register post type
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function register_post_type() {
			$labels = [
				'name'                  => _x( 'Podcast', 'post type general name', 'podcastify' ),
				'singular_name'         => _x( 'Podcast', 'post type singular name', 'podcastify' ),
				'add_new'               => _x( 'Add New', 'podcast', 'podcastify' ),
				'add_new_item'          => _x( 'Add New Episode', 'podcastify' ),
				'edit_item'             => _x( 'Edit podcastify Episode', 'podcastify' ),
				'new_item'              => _x( 'New podcastify Episode', 'podcastify' ),
				'all_items'             => _x( 'All podcastify Episodes', 'podcastify' ),
				'view_item'             => _x( 'View podcastify Episode', 'podcastify' ),
				'search_items'          => _x( 'Search podcastify Episodes', 'podcastify' ),
				'not_found'             => _x( 'No Episodes Found', 'podcastify'),
				'not_found_in_trash'    => _x( 'No Episodes Found In Trash', 'podcastify' ),
				'parent_item_colon'     => '',
				'menu_name'             => __( 'Podcastify', 'podcastify' ),
				'filter_items_list'     => _x( 'Filter Episode list', 'podcastify' ),
				'items_list_navigation' => _x( 'Episode list navigation', 'podcastify' ),
				'items_list'            => _x( 'Episode list', 'podcastify' ),
			];

			$slug = apply_filters( 'wpp_archive_slug', 'podcasts' );
			$args = [
				'labels'              => $labels,
				'public'              => true,
				'publicly_queryable'  => true,
				'exclude_from_search' => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => true,
				'query_var'           => true,
				'can_export'          => true,
				'rewrite'             => [
					'slug'  => $slug,
					'feeds' => true,
				],
				'capability_type'     => 'post',
				'has_archive'         => true,
				'hierarchical'        => false,
				'supports'            => [
					'title',
					'editor',
					'excerpt',
					'thumbnail',
					'page-attributes',
					'comments',
					'author',
					'custom-fields',
					'publicize',
				],
				'menu_position'       => 6,
				'menu_icon'           => 'dashicons-megaphone',
				'show_in_rest'        => true,
			];

			$args = apply_filters( 'wpp_register_post_type_args', $args );
			register_post_type( $this->post_slug, $args );
			$this->add_taxonomies();
		}

		/**
		 * Register taxonomies
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function add_taxonomies() {

			// Tag taxonomy
			$tags_labels = array(
				'name'                       => __( 'Tags', 'podcastify' ),
				'singular_name'              => __( 'Tag', 'podcastify' ),
				'search_items'               => __( 'Search Tags', 'podcastify' ),
				'popular_items'              => __( 'Popular Tags', 'podcastify' ),
				'all_items'                  => __( 'All Tags', 'podcastify' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'                  => __( 'Edit Tag', 'podcastify' ),
				'update_item'                => __( 'Update Tag', 'podcastify' ),
				'add_new_item'               => __( 'Add New Tag', 'podcastify' ),
				'new_item_name'              => __( 'New Tag Name', 'podcastify' ),
				'separate_items_with_commas' => __( 'Separate tags with commas', 'podcastify' ),
				'add_or_remove_items'        => __( 'Add or remove tags', 'podcastify' ),
				'choose_from_most_used'      => __( 'Choose from the most used tags', 'podcastify' ),
				'not_found'                  => __( 'No tags found.', 'podcastify' ),
				'menu_name'                  => __( 'Tags', 'podcastify' ),
			);

			$tags_args = [
				'hierarchical'          => false,
				'labels'                => apply_filters( 'wpp_tag_tax_labels', $tags_labels ),
				'show_ui'               => true,
				'show_admin_column'     => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var'             => true,
				'rewrite'               => [ 'slug' => apply_filters( 'wpp_tags_slug', 'podcast_tags' ) ],
			];

			// Set Post tags for podcastify default or user can set `podcast_tags`
			if ( apply_filters( 'wpp_use_post_tags', true ) ) {
				register_taxonomy_for_object_type( 'post_tag', $this->post_slug );
			} else {
				$tags_args = apply_filters( 'wpp_tags_tax_args', $tags_args );
				register_taxonomy( 'podcast_tags', $this->post_slug, $tags_args );
			}

			// Series taxonomy
			$series_labels = [
				'name'                       => __( 'Podcast Series', 'podcastify' ),
				'singular_name'              => __( 'Series', 'podcastify' ),
				'search_items'               => __( 'Search Series', 'podcastify' ),
				'all_items'                  => __( 'All Series', 'podcastify' ),
				'parent_item'                => __( 'Parent Series', 'podcastify' ),
				'parent_item_colon'          => __( 'Parent Series:', 'podcastify' ),
				'edit_item'                  => __( 'Edit Series', 'podcastify' ),
				'update_item'                => __( 'Update Series', 'podcastify' ),
				'add_new_item'               => __( 'Add New Series', 'podcastify' ),
				'new_item_name'              => __( 'New Series Name', 'podcastify' ),
				'menu_name'                  => __( 'Series', 'podcastify' ),
				'view_item'                  => __( 'View Series', 'podcastify' ),
				'popular_items'              => __( 'Popular Series', 'podcastify' ),
				'separate_items_with_commas' => __( 'Separate series with commas', 'podcastify' ),
				'add_or_remove_items'        => __( 'Add or remove Series', 'podcastify' ),
				'choose_from_most_used'      => __( 'Choose from the most used Series', 'podcastify' ),
				'not_found'                  => __( 'No Series Found', 'podcastify' ),
				'items_list_navigation'      => __( 'Series list navigation', 'podcastify' ),
				'items_list'                 => __( 'Series list', 'podcastify' ),
				'back_to_items'              => __( '← Back to Series', 'podcastify' ),
			];

			$series_args = [
				'public'            => true,
				'hierarchical'      => true,
				'rewrite'           => [ 'slug' => apply_filters( 'wpp_series_slug', 'series' ) ],
				'labels'            => apply_filters( 'wpp_series_tax_labels', $series_labels ),
				'show_in_rest'      => true,
				'show_admin_column' => true,
			];

			$series_args = apply_filters( 'wpp_series_tax_args', $series_args );

			register_taxonomy( apply_filters( 'wpp_series_taxonomy', 'episode_series' ), $this->post_slug, $series_args );
		}

		/* Attaches meta boxes to the post type */
		public function add_meta_box() {
		}

		/* Listens for when the post type being saved */
		public function save() {
		}
	}

endif;// class exit check

// Initialize the class on init hook to ensure translations are loaded
add_action( 'init', function() {
	global $podcastify_post;
	$podcastify_post = new PodcastPost();
}, 5 );
