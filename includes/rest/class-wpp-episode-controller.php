<?php
/**
 * Episode rest Api
 *
 * @package Podcastify
 */


namespace Podcastify\Rest;

use WP_REST_Controller;
use function Podcastify\Utils\{ wpp_get_setting };


if ( ! class_exists( 'Wpp_Episode_Controller' ) ) :

	class Wpp_Episode_Controller extends WP_REST_Controller {

		/**
		 * Namespace of the api.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		protected $namespace;

		/**
		 * End point path.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		protected $rest_base;

		/**
		 * post type.
		 *
		 * @var string
		 */
		protected $post_type = 'episode';


		/**
		 * Instance of a post meta fields object.
		 *
		 * @since 1.0.0
		 * @var WP_REST_Post_Meta_Fields
		 */
		protected $meta;


		// Here initialize our namespace and resource name.
		public function __construct() {
			$this->namespace = '/wpp/v1';
			$this->rest_base = 'episodes';
		}

		// Register our routes.
		public function register_routes() {
			register_rest_route(
				$this->namespace,
				'/' . $this->rest_base,
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_items' ],
						'permission_callback' => [ $this, 'get_items_permissions_check' ],
						'args'                => $this->get_collection_params(),
					],
					// Register our schema callback.
					'schema' => [ $this, 'get_item_schema' ],
				]
			);
		}

		/**
		 * Check permissions for the posts.
		 *
		 * @param WP_REST_Request $request Current request.
		 */
		public function get_items_permissions_check( $request ) {

			do_action( "wp_before_{$this->post_type}_rest_permission" );

			if ( ! current_user_can( 'read' ) ) {
				return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot view the post resource.' ), [ 'status' => $this->authorization_status_code() ] );
			}
			return true;
		}

		/**
		 * Grabs the five most recent posts and outputs them as a rest response.
		 *
		 * @param WP_REST_Request $request Current request.
		 */
		public function get_items( $request ) {

			// Retrieve the list of registered collection query parameters.
			$registered = $this->get_collection_params();
			$args       = [];

			/*
			* This array defines mappings between public API query parameters whose
			* values are accepted as-passed, and their internal WP_Query parameter
			* name equivalents (some are the same). Only values which are also
			* present in $registered will be set.
			*/
			$parameter_mappings = [
				'author'         => 'author__in',
				'author_exclude' => 'author__not_in',
				'menu_order'     => 'menu_order',
				'offset'         => 'offset',
				'order'          => 'order',
				'orderby'        => 'orderby',
				'page'           => 'paged',
				'include'        => 'post__in',
				'exclude'        => 'post__not_in',
				'per_page'       => 'posts_per_page',
				'slug'           => 'name',
				'post_type'      => $this->post_type,
				'parent'         => 'post_parent__in',
				'parent_exclude' => 'post_parent__not_in',
				'status'         => 'post_status',
				'search'         => 's',
			];
			/*
			* For each known parameter which is both registered and present in the request,
			* set the parameter's value on the query $args.
			*/
			foreach ( $parameter_mappings as $api_param => $wp_param ) {
				if ( isset( $registered[ $api_param ], $request[ $api_param ] ) ) {
					$args[ $wp_param ] = $request[ $api_param ];
				}
			}

			// Check for & assign any parameters which require special handling or setting.
			$args['date_query'] = [];

			// Set before into date query. Date query must be specified as an array of an array.
			if ( isset( $registered['before'], $request['before'] ) ) {
				$args['date_query'][0]['before'] = $request['before'];
			}

			// Set after into date query. Date query must be specified as an array of an array.
			if ( isset( $registered['after'], $request['after'] ) ) {
				$args['date_query'][0]['after'] = $request['after'];
			}

			if ( is_array( $request['filter'] ) ) {
				$args = array_merge( $args, $request['filter'] );
				unset( $args['filter'] );
			}

			/**
		 * Filters the query arguments for a request.
		 *
		 * Enables adding extra arguments or setting defaults for a post collection request.
		 *
		 * @since 1.0.0
		 *
		 * @link https://developer.wordpress.org/reference/classes/wp_query/
		 *
		 * @param array           $args    Key value array of query var to query value.
		 * @param WP_REST_Request $request The request used.
		 */
			$args       = apply_filters( "rest_{$this->post_type}_query", $args, $request );
			$query_args = $this->prepare_items_query( $args, $request );

			// Get taxonomies for each of the requested post_types.
			$taxonomies = wp_list_filter( get_object_taxonomies( $query_args['post_type'], 'objects' ), array( 'show_in_rest' => true ) );

			foreach ( $taxonomies as $taxonomy ) {
				$base        = ! empty( $taxonomy->rest_base ) ? $taxonomy->rest_base : $taxonomy->name;
				$tax_exclude = $base . '_exclude';

				if ( ! empty( $request[ $base ] ) ) {
					$query_args['tax_query'][] = array(
						'taxonomy'         => $taxonomy->name,
						'field'            => 'term_id',
						'terms'            => $request[ $base ],
						'include_children' => false,
					);
				}

				if ( ! empty( $request[ $tax_exclude ] ) ) {
					$query_args['tax_query'][] = array(
						'taxonomy'         => $taxonomy->name,
						'field'            => 'term_id',
						'terms'            => $request[ $tax_exclude ],
						'include_children' => false,
						'operator'         => 'NOT IN',
					);
				}
			}

			$posts_query  = new WP_Query();
			$query_result = $posts_query->query( $query_args );

			$posts = [];

			foreach ( $query_result as $post ) {
				$posts_controller = new WP_REST_Posts_Controller( $post->post_type );
				if ( ! $post_controller->check_read_permission( $post ) ) {
					continue;
				}

				$data    = $post_controller->prepare_item_for_response( $post, $request );
				$posts[] = $post_controller->prepare_response_for_collection( $data );
			}

			$page        = (int) $query_args['paged'];
			$total_posts = $posts_query->found_posts;

			if ( $total_posts < 1 ) {
				// Out-of-bounds, run the query again without LIMIT for total count.
				unset( $query_args['paged'] );

				$count_query = new WP_Query();
				$count_query->query( $query_args );
				$total_posts = $count_query->found_posts;
			}

			$max_pages = ceil( $total_posts / (int) $posts_query->query_vars['posts_per_page'] );

			if ( $page > $max_pages && $total_posts > 0 ) {
				return new WP_Error(
					'rest_post_invalid_page_number',
					__( 'The page number requested is larger than the number of pages available.', 'podcastify' ),
					array( 'status' => 400 )
				);
			}

			$response = rest_ensure_response( $posts );

			$response->header( 'X-WP-Total', (int) $total_posts );
			$response->header( 'X-WP-TotalPages', (int) $max_pages );

			$request_params = $request->get_query_params();
			if ( ! empty( $request_params['filter'] ) ) {
				// Normalize the pagination params.
				unset( $request_params['filter']['posts_per_page'] );
				unset( $request_params['filter']['paged'] );
			}
			$base = add_query_arg( $request_params, rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ) );

			// Previous post link.
			if ( $page > 1 ) {
				$prev_page = $page - 1;

				if ( $prev_page > $max_pages ) {
					$prev_page = $max_pages;
				}

				$prev_link = add_query_arg( 'page', $prev_page, $base );
				$response->link_header( 'prev', $prev_link );
			}

			// Next post link.
			if ( $max_pages > $page ) {
				$next_page = $page + 1;
				$next_link = add_query_arg( 'page', $next_page, $base );

				$response->link_header( 'next', $next_link );
			}
		}

		/**
		 * Check permissions for the posts.
		 *
		 * @param WP_REST_Request $request Current request.
		 */
		public function get_item_permissions_check( $request ) {
			if ( ! current_user_can( 'read' ) ) {
				return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot view the post resource.' ), [ 'status' => $this->authorization_status_code() ] );
			}
			return true;
		}

		/**
		 * Grabs the five most recent posts and outputs them as a rest response.
		 *
		 * @param WP_REST_Request $request Current request.
		 */
		public function get_item( $request ) {
			$id   = (int) $request['id'];
			$post = get_post( $id );

			if ( empty( $post ) ) {
				return rest_ensure_response( [] );
			}

			$response = $this->prepare_item_for_response( $post, $request );

			// Return all of our post response data.
			return $response;
		}

		/**
		 * Matches the post data to the schema we want.
		 *
		 * @param WP_Post $post The comment object whose response is being prepared.
		 */
		public function prepare_item_for_response( $post, $request ) {
			$post_data = [];

			$schema = $this->get_item_schema( $request );

			// We are also renaming the fields to more understandable names.
			if ( isset( $schema['properties']['id'] ) ) {
				$post_data['id'] = (int) $post->ID;
			}

			if ( isset( $schema['properties']['content'] ) ) {
				$post_data['content'] = apply_filters( 'the_content', $post->post_content, $post );
			}

			return rest_ensure_response( $post_data );
		}

		/**
		 * Prepare a response for inserting into a collection of responses.
		 *
		 * This is copied from WP_REST_Controller class in the WP REST API v2 plugin.
		 *
		 * @param WP_REST_Response $response Response object.
		 * @return array Response data, ready for insertion into collection data.
		 */
		public function prepare_response_for_collection( $response ) {
			if ( ! ( $response instanceof WP_REST_Response ) ) {
				return $response;
			}

			$data   = (array) $response->get_data();
			$server = rest_get_server();

			if ( method_exists( $server, 'get_compact_response_links' ) ) {
				$links = call_user_func( array( $server, 'get_compact_response_links' ), $response );
			} else {
				$links = call_user_func( array( $server, 'get_response_links' ), $response );
			}

			if ( ! empty( $links ) ) {
				$data['_links'] = $links;
			}

			return $data;
		}

		/**
		 * Get our sample schema for a post.
		 *
		 * @since 1.0.0
		 */
		public function get_item_schema() {
			if ( $this->schema ) {
				// Since WordPress 5.3, the schema can be cached in the $schema property.
				return $this->schema;
			}

			$this->schema = array(
				// This tells the spec of JSON Schema we are using which is draft 4.
				'$schema'    => 'http://json-schema.org/draft-04/schema#',
				// The title property marks the identity of the resource.
				'title'      => 'post',
				'type'       => 'object',
				// In JSON Schema you can specify object properties in the properties attribute.
				'properties' => array(
					'id'      => array(
						'description' => esc_html__( 'Unique identifier for the object.', 'my-textdomain' ),
						'type'        => 'integer',
						'context'     => array( 'view', 'edit', 'embed' ),
						'readonly'    => true,
					),
					'content' => array(
						'description' => esc_html__( 'The content for the object.', 'my-textdomain' ),
						'type'        => 'string',
					),
				),
			);

			return $this->schema;
		}

		// Sets up the proper HTTP status code for authorization.
		public function authorization_status_code() {

			$status = 401;

			if ( is_user_logged_in() ) {
				$status = 403;
			}

			return $status;
		}



		/**
		 * Get the query params for collections.
		 *
		 * @since 1.0.0
		 *
		 * @return array $query_params list of arguments.
		 */
		public function get_collection_params() {
			$query_params = parent::get_collection_params();

			$query_params['context']['default'] = 'view';

			$query_params['after'] = [
				'description'       => __( 'Limit response to resources published after a given ISO8601 compliant date.', 'podcastify' ),
				'type'              => 'string',
				'format'            => 'date-time',
				'validate_callback' => 'rest_validate_request_arg',
			];

			$query_params['author'] = [
				'description'       => __( 'Limit result set to posts assigned to specific authors.', 'podcastify' ),
				'type'              => 'array',
				'default'           => [],
				'sanitize_callback' => 'wp_parse_id_list',
				'validate_callback' => 'rest_validate_request_arg',
			];

			$query_params['author_exclude'] = [
				'description'       => __( 'Ensure result set excludes posts assigned to specific authors.', 'podcastify' ),
				'type'              => 'array',
				'default'           => [],
				'sanitize_callback' => 'wp_parse_id_list',
				'validate_callback' => 'rest_validate_request_arg',
			];

			$query_params['before'] = [
				'description'       => __( 'Limit response to resources published before a given ISO8601 compliant date.', 'podcastify' ),
				'type'              => 'string',
				'format'            => 'date-time',
				'validate_callback' => 'rest_validate_request_arg',
			];

			$query_params['exclude'] = [
				'description'       => __( 'Ensure result set excludes specific ids.', 'podcastify' ),
				'type'              => 'array',
				'default'           => [],
				'sanitize_callback' => 'wp_parse_id_list',
			];

			$query_params['include'] = [
				'description'       => __( 'Limit result set to specific ids.', 'podcastify' ),
				'type'              => 'array',
				'default'           => [],
				'sanitize_callback' => 'wp_parse_id_list',
			];

			$query_params['menu_order'] = [
				'description'       => __( 'Limit result set to resources with a specific menu_order value.', 'podcastify' ),
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			];

			$query_params['offset'] = [
				'description'       => __( 'Offset the result set by a specific number of items.', 'w-podcastify' ),
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			];

			$query_params['order'] = [
				'description'       => __( 'Order sort attribute ascending or descending.', 'podcastify' ),
				'type'              => 'string',
				'default'           => 'desc',
				'enum'              => [ 'asc', 'desc' ],
				'validate_callback' => 'rest_validate_request_arg',
			];

			$query_params['orderby'] = [
				'description'       => __( 'Sort collection by object attribute.', 'podcastify' ),
				'type'              => 'string',
				'default'           => 'date',
				'enum'              => [
					'date',
					'id',
					'include',
					'title',
					'slug',
					'menu_order',
				],
				'validate_callback' => 'rest_validate_request_arg',
			];

			// $query_params['orderby']['enum'][] = 'menu_order';

			$query_params['parent'] = [
				'description'       => __( 'Limit result set to those of particular parent ids.', 'podcastify' ),
				'type'              => 'array',
				'sanitize_callback' => 'wp_parse_id_list',
				'default'           => [],
			];

			$query_params['parent_exclude'] = [
				'description'       => __( 'Limit result set to all items except those of a particular parent id.', 'podcastify' ),
				'type'              => 'array',
				'sanitize_callback' => 'wp_parse_id_list',
				'default'           => [],
			];

			$query_params['slug'] = [
				'description'       => __( 'Limit result set to posts with a specific slug.', 'podcastify' ),
				'type'              => 'string',
				'validate_callback' => 'rest_validate_request_arg',
			];

			$query_params['status'] = [
				'default'           => 'publish',
				'description'       => __( 'Limit result set to posts assigned a specific status.', 'podcastify' ),
				'sanitize_callback' => 'sanitize_key',
				'type'              => 'string',
				'validate_callback' => [ $this, 'validate_user_can_query_private_statuses' ],
			];

			$query_params['filter'] = array(
				'description' => __( 'Use WP Query arguments to modify the response private query vars require appropriate authorization.', 'podcastify' ),
			);

			$taxonomies = wp_list_filter( get_object_taxonomies( get_post_types( [], 'names' ), 'objects' ), array( 'show_in_rest' => true ) );
			foreach ( $taxonomies as $taxonomy ) {
				$base = ! empty( $taxonomy->rest_base ) ? $taxonomy->rest_base : $taxonomy->name;

				$query_params[ $base ] = array(
					'description'       => sprintf( __( 'Limit result set to all items that have the specified term assigned in the %s taxonomy.' ), $base ),
					'type'              => 'array',
					'sanitize_callback' => 'wp_parse_id_list',
					'default'           => [],
				);
			}

			return $query_params;
		}

		/**
		 * Validate whether the user can query private statuses
		 *
		 * @since 1.0.0
		 *
		 * @param  mixed           $value
		 * @param  WP_REST_Request $request
		 * @param  string          $parameter
		 *
		 * @return WP_Error|boolean
		 */
		public function validate_user_can_query_private_statuses( $value, $request, $parameter ) {
			if ( 'publish' === $value ) {
				return true;
			}

			if ( ! current_user_can( $post_type_obj->cap->edit_posts ) ) {
				$post_type_obj = get_post_type_object( $this->post_type );
				return new WP_Error(
					'rest_forbidden_status',
					__( 'Status is forbidden' ),
					array(
						'status'    => rest_authorization_required_code(),
						'post_type' => $post_type_obj->name,
					)
				);
			}

			return true;
		}

		/**
		 * Determines the allowed query_vars for a get_items() response and prepares
		 * them for WP_Query.
		 *
		 * @since 4.7.0
		 *
		 * @param array           $prepared_args Optional. Prepared WP_Query arguments. Default empty array.
		 * @param WP_REST_Request $request       Optional. Full details about the request.
		 * @return array Items query arguments.
		 */
		protected function prepare_items_query( $prepared_args = array(), $request = null ) {
			$query_args = array();

			foreach ( $prepared_args as $key => $value ) {
				/**
				 * Filters the query_vars used in get_items() for the constructed query.
				 *
				 * The dynamic portion of the hook name, `$key`, refers to the query_var key.
				 *
				 * @since 4.7.0
				 *
				 * @param string $value The query_var value.
				 */
				$query_args[ $key ] = apply_filters( "rest_query_var-{$key}", $value ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
			}

			if ( 'post' !== $this->post_type || ! isset( $query_args['ignore_sticky_posts'] ) ) {
				$query_args['ignore_sticky_posts'] = true;
			}

			// Map to proper WP_Query orderby param.
			if ( isset( $query_args['orderby'] ) && isset( $request['orderby'] ) ) {
				$orderby_mappings = array(
					'id'            => 'ID',
					'include'       => 'post__in',
					'slug'          => 'post_name',
					'include_slugs' => 'post_name__in',
				);

				if ( isset( $orderby_mappings[ $request['orderby'] ] ) ) {
					$query_args['orderby'] = $orderby_mappings[ $request['orderby'] ];
				}
			}

			return $query_args;
		}

	}
endif; // class exist
// @see https://developer.wordpress.org/rest-api/extending-the-rest-api/controller-classes/


/**
 * Temporary added
 *
 * @return void
 */
function wpp_register_episode_rest_routes() {
	global $wpp_episode_controller;
	$wpp_episode_controller = new Wpp_Episode_Controller();
	$wpp_episode_controller->register_routes();
}
// add_action( 'rest_api_init', 'wpp_register_episode_rest_routes' );
