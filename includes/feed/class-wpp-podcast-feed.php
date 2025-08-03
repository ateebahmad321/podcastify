<?php
/**
 * Admin plugin functionality.
 *
 * @package Podcastify
 */

namespace Podcastify\Feed;

use function Podcastify\Utils\{ wpp_get_setting, wpp_home_url, dd, get_series_data, get_only_series_data, wpp_get_podcast_sub_categories, wpp_get_podcast_categories , wpp_get_episodes_query_args, wpp_get_episode_enclosure_link, wpp_get_episode_meta, wpp_convert_url_to_path };

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'Wpp_Podcast_Feed' ) ) :
	/**
	 * Admin class for admin functionality.
	 *
	 * @since 1.0.0
	 */
	class Wpp_Podcast_Feed {

		/**
		 * Setting api object.
		 *
		 * @since 1.0.0
		 * @access public
		 * @var object
		 */
		public $current_series_id;

		/**
		 * Constructor of the class.
		 */
		public function __construct() {
			$this->set_series();
			add_action( 'init', [ $this, 'add_feed' ] );
		}
		/**
		 * set current series id iin member function.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function set_series() {
			$this->current_series_id = isset( $_GET['podcastify_series'] ) ? sanitize_text_field( wp_unslash( $_GET['podcastify_series'] ) ) : '';
		}

		/**
		 * Callback to `init` hook to set feed.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function add_feed() {
			add_feed( 'podcasts', [ $this, 'render_custom_feed' ] );
		}

		/**
		 * Get Serries feed tag.
		 *
		 * @since 1.0.0
		 * @access public
		 * @param string $category
		 * @return string
		 */
		public function get_feed_category_tag( string $category ) : string {

			$wpp_feed_category     = wpp_get_podcast_categories();
			$wpp_feed_sub_category = wpp_get_podcast_sub_categories();

			$category = \explode( ':', $category );
			$tag      = '';
			if ( isset( $category[0] ) ) {
				$tag = '<itunes:category text="' . \esc_html( $wpp_feed_category[ $category[0] ] ) . '">';
				if ( isset( $category[1] ) ) {
					$tag .= '<itunes:category text="' . \esc_html( $wpp_feed_sub_category[ $category[0] ][ $category[1] ] ) . '"></itunes:category>';
				}
				$tag .= '</itunes:category>';
			}

			return $tag;
		}

		/**
		 * Set feed redirection.
		 *
		 * @since 1.0.0
		 * @access public
		 * @param string     $new_feed_url new feed url.
		 * @param int|string $series_id
		 * @return void
		 */
		public function set_redirection_new_feed_url( string $new_feed_url, $series_id ) {

			if ( $series_id ) {
				$field = $field . '_' . $series_id;
			}
				$data = get_option( 'wpp_redirection_feed' . $field );
			if ( $data ) {
				$redirect_date = apply_filters( 'wpp_feed_redirection_date_limit', strtotime( '+2 days', $data['time'] ), $data['time'] );
				$current_date  = time();
				// Redirect feed to 301 status

				if ( $current_date > $redirect_date ) {
					do_action( 'wpp_feed_redirection', $series_id, $redirect_date, $current_date );
					header( 'HTTP/1.1 301 Moved Permanently' );
					header( 'Location: ' . $new_feed_url );
					die();
				}
			}
		}

		public function render_custom_feed() {
			global $wp_query;
			$current_series = '';
			$series_id      = '';
			if ( isset( $_REQUEST ['podcastify_series'] ) ) {
				$current_series = ! empty( $_REQUEST ['podcastify_series'] ) ? sanitize_text_field( $_REQUEST ['podcastify_series'] ) : '';
			} elseif ( isset( $wp_query->query_vars['podcastify_series'] ) ) {
				$current_series = ! empty( $wp_query->query_vars['podcastify_series'] ) ? sanitize_text_field( $wp_query->query_vars['podcastify_series'] ) : '';
			}
			// Check if series exist then get the id.
			if ( $current_series ) {
				$series    = get_term_by( 'slug', $current_series, 'episode_series' );
				$series_id = $series->term_id;
			}

			// Feed title.
			$feed_title = apply_filters( 'wpp_feed_title', get_series_data( 'title', $series_id, get_bloginfo( 'name' ) ), $series_id );
			// Feed channel atom link.

			$feed_channel_atom_link = apply_filters( 'wpp_feed_channel_link', wpp_home_url(), $series_id );

			// Feed channel desc.
			$feed_channel_desc = apply_filters( 'wpp_feed_channel_description', get_series_data( 'summary', $series_id, get_bloginfo( 'name' ) ), $series_id );

			// Feed language.
			$feed_language = apply_filters( 'wpp_feed_language', get_series_data( 'podcast_language', $series_id, 'en-us' ), $series_id );

			// Feed copyright/ license information.
			$feed_copyright = apply_filters( 'wpp_feed_copy_right', get_series_data( 'podcast_copy_right', $series_id, '&#xA9; ' . date( 'Y' ) . ' ' . get_bloginfo( 'name' ) ), $series_id );

			// Itunes type.
			$feed_itune_type = apply_filters( 'wpp_feed_copyright', get_series_data( 'show_type', $series_id ), $series_id );

			// Feed subtitle.
			$feed_subtitle = apply_filters( 'wpp_feed_subtitle', get_series_data( 'subtitle', $series_id, get_bloginfo( 'description' ) ), $series_id );

			// Feed Author.
			$feed_author = apply_filters( 'wpp_feed_author', get_series_data( 'author', $series_id, get_bloginfo( 'name' ) ), $series_id );

			// Itune Feed type.
			$feed_itune_type = apply_filters( 'wpp_feed_show_type', get_only_series_data( 'show_type', $series_id ), $series_id );

			// Feed Summary.
			$feed_desc_length = apply_filters( 'wpp_feed_summary_length', 3999 );
			$feed_description = apply_filters( 'wpp_feed_summary', mb_substr( strip_tags( get_series_data( 'summary', $series_id, get_bloginfo( 'description' ) ) ), 0, $feed_desc_length ), $series_id );

			// Feed owner name.
			$feed_owner_name = apply_filters( 'wpp_feed_owner_name', get_series_data( 'author_name', $series_id ), $series_id );

			// Feed owner email.
			$feed_owner_email = apply_filters( 'wpp_feed_owner_email', get_series_data( 'author_email', $series_id ), $series_id );

			// Is Feed explicit.
			$feed_itune_explicit = apply_filters( 'wpp_feed_explicit', get_series_data( 'podcast_explicit', $series_id, 'no' ), $series_id );
			// If  For itune is select no it's means no explicit otherwise yes.
			$feed_google_explicit = apply_filters( 'wpp_feed_google_explicit', ( 'no' == $feed_explicit ) ? 'No' : 'Yes' );

			// Feed Completed.
			$feed_completed = apply_filters( 'wpp_feed_completed', get_series_data( 'podcast_complete', $series_id, 'off' ), $series_id );

			// Feed Image.
			$feed_cover_image = apply_filters( 'wpp_feed_image', get_only_series_data( 'podcast_cover', $series_id ), $series_id );

			// Channel feed link
			$channel_feed_link = apply_filters( 'wpp_channel_feed_link', wpp_home_url(), $series_id );

			// Feed categories
			$feed_category_level_1 = apply_filters( 'wpp_feed_category_level_1', get_only_series_data( 'category_level_1', $series_id ), $series_id );
			$feed_category_level_2 = apply_filters( 'wpp_feed_category_level_2', get_only_series_data( 'category_level_2', $series_id ), $series_id );
			$feed_category_level_3 = apply_filters( 'wpp_feed_category_level_3', get_only_series_data( 'category_level_3', $series_id ), $series_id );

			// New Feed URL.
			$feed_new_url = apply_filters( 'wpp_new_feed_url', get_only_series_data( 'new_feed_url', $series_id ), $series_id );
			if ( $feed_new_url ) {
				$this->set_redirection_new_feed_url( $feed_new_url, $series_id );
			}
			// lighten
			$feed_lighten = apply_filters( 'wpp_feed_lighten', get_series_data( 'lighten', $series_id, 'off' ), $series_id );

			// Feed description
			$feed_description_type = apply_filters( 'wpp_feed_description', get_series_data( 'feed_description', $series_id, 'excerpt' ), $series_id );

			do_action( 'wpp_before_feed' );
			// Feed publish date.
			$feed_publish_date = apply_filters( 'wpp_feed_publish_date', get_series_data( 'publish_date', $series_id, 'publish_date' ), $series_id );
			header( 'Content-Type: ' . feed_content_type( 'podcast' ) . '; charset=' . get_option( 'blog_charset' ), true );
			?><?php echo '<?xml version="1.0" encoding="' . get_option( 'blog_charset' ) . '"?>' . "\n"; ?>
<rss version="2.0"
xmlns:content="http://purl.org/rss/1.0/modules/content/"
xmlns:wfw="http://wellformedweb.org/CommentAPI/"
xmlns:dc="http://purl.org/dc/elements/1.1/"
xmlns:atom="http://www.w3.org/2005/Atom"
xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"
xmlns:googleplay="http://www.google.com/schemas/play-podcasts/1.0"
			<?php do_action( 'rss2_ns' ); ?>>
	<channel>
	<title><?php \esc_html_e( $feed_title ); ?></title>
	<atom:link href="<?php echo \esc_url( self_link() ); ?>" rel="self" type="application/rss+xml"/>
	<link><?php echo \esc_url( $feed_channel_atom_link ); ?></link>
	<description><?php \esc_html_e( $feed_channel_desc ); ?></description>
	<lastBuildDate><?php \esc_html_e( mysql2date( 'D, d M Y H:i:s +0000', get_lastpostmodified( 'GMT' ), false ) ); ?></lastBuildDate>
	<language><?php \esc_html_e( $feed_language ); ?></language>
	<copyright><?php \esc_html_e( $feed_copyright ); ?></copyright>
	<itunes:subtitle><?php \esc_html_e( $feed_subtitle ); ?></itunes:subtitle>
	<itunes:author><?php \esc_html_e( $feed_author ); ?></itunes:author>
			<?php echo ( $feed_itune_type ) ? "<itunes:type>$feed_itune_type</itunes:type>" : ''; ?>
	<itunes:summary><?php \esc_html_e( $feed_description ); ?></itunes:summary>
	<itunes:owner>
		<itunes:name><?php \esc_html_e( $feed_owner_name ); ?></itunes:name>
		<itunes:email><?php \esc_html_e( $feed_owner_email ); ?></itunes:email>
	</itunes:owner>
	<itunes:explicit><?php \esc_html_e( $feed_itune_explicit ); ?></itunes:explicit>
			<?php echo ( 'on' == $feed_completed ) ? '<itunes:complete>yes</itunes:complete>' : ''; ?>
			<?php if ( ! empty( trim( $feed_cover_image ) ) ) : ?>
		<itunes:image href="<?php echo esc_url( $feed_cover_image ); ?>"></itunes:image>
		<image>
			<url><?php echo esc_url( $feed_cover_image ); ?></url>
			<title><?php echo esc_html( $feed_title ); ?></title>
			<link><?php echo esc_url( $channel_feed_link ); ?></link>
		</image>
	<?php endif;// End if $feed_cover_image ?>
			<?php echo ( 'none' !== $feed_category_level_1 ) ? $this->get_feed_category_tag( $feed_category_level_1 ) : ''; ?>
			<?php echo ( 'none' !== $feed_category_level_2 ) ? $this->get_feed_category_tag( $feed_category_level_2 ) : ''; ?>
			<?php echo ( 'none' !== $feed_category_level_3 ) ? $this->get_feed_category_tag( $feed_category_level_3 ) : ''; ?>
			<?php echo ! empty( trim( $feed_new_url ) ) ? '<itunes:new-feed-url>' . \esc_url( $feed_new_url ) . '</itunes:new-feed-url>' : ''; ?>
			<?php if ( 'off' == $feed_lighten ) : ?>
		<googleplay:author><?php \esc_html_e( $feed_author ); ?></googleplay:author>
		<googleplay:owner><?php \esc_html_e( $feed_owner_email ); ?></googleplay:owner>
		<googleplay:description><?php \esc_html_e( $feed_description ); ?></googleplay:description>
		<googleplay:explicit><?php \esc_html_e( $feed_google_explicit ); ?></googleplay:explicit>
				<?php if ( ! empty( trim( $feed_cover_image ) ) ) : ?>
			<googleplay:image href="<?php echo \esc_url( $feed_cover_image ); ?>"></googleplay:image>
		<?php endif;// End if $feed_cover_image ?>
	<?php endif;// lighten feed ?>
			<?php
			do_action( 'rrs2_head' );
			$post_per_page = apply_filters( 'wpp_feed_num_per_post', get_option( 'num_post_rss', 10 ) );

			$args                  = [];
			$args['post_per_page'] = $post_per_page;

			if ( $series_id ) {
				$args ['series'] = $series_id;
			} else {
				$wpp_series = get_terms( 'episode_series', apply_filters( 'wpp_feed_series', [ 'hide_empty' => false ] ) );
				foreach ( $wpp_series as $series ) {
					$exclude_feed_option = get_series_data( 'exclude_in_feed', $series->term_id, 'off' );
					if ( 'off' === $exclude_feed_option ) {
						$args ['series'][] = $series->term_id;
					}
				}
			}

			$args      = wpp_get_episodes_query_args( $args );
			$q_episode = new \WP_Query( $args );

			if ( $q_episode->have_posts() ) :
				while ( $q_episode->have_posts() ) :
					$q_episode->the_post();
					$current_post_index          = $q_episode->current_post + 1;
					$feed_episode_enclosure_link = apply_filters( 'wpp_feed_episode_enclosure_link', wpp_get_episode_enclosure_link( get_the_ID() ), get_the_ID() );

					// if enclosure_link don't need to include in feed.
					if ( empty( trim( $feed_episode_enclosure_link ) ) ) {
						continue;
					}
					$wpp_feed_episode_image = apply_filters( 'wpp_feed_episode_image', get_the_post_thumbnail_url( get_the_ID(), 'full' ), get_the_ID() );

					// Feed Podcast post type.
					$feed_episode_type = apply_filters( 'wpp_feed_episode_type', wpp_get_episode_meta( 'episode_type', get_the_ID() ), get_the_ID() );

					// Episode Media file duration.
					$feed_episode_enclosure_duration = apply_filters( 'wpp_feed_episode_enclosure_duration', wpp_get_episode_meta( 'duration', get_the_ID(), '0:00' ), get_the_ID() );

					// Episode Media File size.
					$e_file_size                 = filesize( wpp_convert_url_to_path( wpp_get_episode_meta( apply_filters( 'wpp_media_file_meta_key', 'podcast_file' ), get_the_ID() ) ) );
					$feed_episode_enclosure_size = apply_filters( 'wpp_feed_episode_enclosure_size', $e_file_size, get_the_ID() );

					// File episode type.
					$e_type = \wp_check_filetype( \wp_basename( wpp_get_episode_meta( 'podcast_file', get_the_ID() ) ) );

					$feed_episode_file_type = apply_filters( 'wpp_feed_episode_file_type', $e_type['type'], get_the_ID() );

					// File episode explicit.
					$file_episode_explicit = apply_filters( 'wpp_feed_episode_explicit', wpp_get_episode_meta( 'explicit', get_the_ID() ), get_the_ID() );

					$feed_episode_google_explicit = apply_filters( 'wpp_feed_episode_google_explicit', ( 'no' == $feed_explicit ) ? 'No' : 'Yes' );

					// Feed episode block form feed.
					$is_ep_blocked      = wpp_get_episode_meta( 'feed_block', get_the_ID() );
					$feed_episode_block = apply_filters( 'wpp_feed_episode_block', ( 'on' == $is_ep_blocked ) ? 'yes' : 'no' );

					// Episode author.
					$feed_episode_author = apply_filters( 'wpp_feed_episode_author', get_the_author(), get_the_ID() );

					// Feed description.
					$episode_content = get_the_content_feed();
					$episode_content = preg_replace( '/<\/?iframe(.|\s)*?>/', '', \strip_shortcodes( $episode_content ) );

					if ( 'excerpt' === $feed_description_type ) {
						ob_start();
						the_excerpt_rss();
						$episode_content = ob_get_clean();
					} else {

						/**
						 * If the lighten is on, limit the full html description to 4000.
						 *
						 *  @see https://help.apple.com/itc/podcasts_connect/#/itcb54353390
						 */
						if ( 'on' == $feed_lighten && $current_post_index <= 10 ) {
							$episode_content = mb_substr( $episode_content, 0, 3999 );
						}
					}
					$episode_content = apply_filters( 'wpp_feed_episode_content', $episode_content, get_the_ID() );

					/**
					 * iTunes summary excludes HTML and must be shorter than 4000 characters
					 *
					 *  @see https://help.apple.com/itc/podcasts_connect/#/itcb54353390
					 */
					$episode_itunes_summary = apply_filters( 'wpp_feed_episode_itune_summary ', \mb_substr( \wp_strip_all_tags( $episode_content ), 0, 3999 ), get_the_ID() );

					/**
					 * Same as Itune. but must be shorter than 1000 characters
					 *
					 * @see https://developers.google.com/search/reference/podcast/rss-feed#episode-level
					 */
					$episode_google_description = apply_filters( 'wpp_feed_episode_google_description ', \mb_substr( \wp_strip_all_tags( $episode_content ), 0, 1000 ), get_the_ID() );

					// Itune subtitle.
					$episode_itunes_subtitle = str_replace(
						array(
							'>',
							'<',
							'\'',
							'"',
							'`',
							'[andhellip;]',
							'[&hellip;]',
							'[&#8230;]',
						),
						array( '', '', '', '', '', '', '', '' ),
						\wp_strip_all_tags( $episode_content )
					);
					$episode_itunes_subtitle = apply_filters( 'wpp_episode_itune_subtitle', \mb_substr( $episode_itunes_subtitle, 0, 254 ), get_the_ID() );

					// Publish date.

					if ( 'publish_date' == $feed_publish_date ) {
						$episode_publish_date = esc_html( mysql2date( 'D, d M Y H:i:s +0000', \get_post_time( 'Y-m-d H:i:s', true ), false ) );
					} else {
						// recoded date.
						$episode_publish_date = esc_html( mysql2date( 'D, d M Y H:i:s +0000', wpp_get_episode_meta( 'date_recorded', get_the_ID() ), false ) );
					}

						 /**
							* Tags/keywords.
							*/
						$post_tags        = \get_the_tags( \get_the_ID() );
						$episode_keywords = '';
					if ( $post_tags ) {
						$tags = array();
						foreach ( $post_tags as $tag ) {
							$tags[] = $tag->name;
						}
						$tags = apply_filters( 'ssp_feed_item_itunes_keyword_tags', $tags, get_the_ID() );
						if ( ! empty( $tags ) ) {
							$episode_keywords = implode( $tags, ',' );
						}
					}

					// Episode itune enabled.
					$wpp_itune_field_enabled = wpp_get_setting( 'itune_fields', 'wpp_setting', 'on' );
					if ( 'on' === $wpp_itune_field_enabled ) {
						$episode_itunes_title         = apply_filters( 'wpp_feed_episode_itunes_title', wpp_get_episode_meta( 'itunes_title', get_the_ID() ), get_the_ID() );
						$episode_itunes_type          = apply_filters( 'wpp_feed_episode_itunes_type', wpp_get_episode_meta( 'itunes_episode_type', get_the_ID() ), get_the_ID() );
						$itunes_episode_number        = apply_filters( 'wpp_feed_episode_itunes_episode_number', wpp_get_episode_meta( 'itunes_episode_number', get_the_ID() ), get_the_ID() );
						$episode_itunes_season_number = apply_filters( 'wpp_fee_episode_itunes_season_number', wpp_get_episode_meta( 'itunes_season_number', get_the_ID() ), get_the_ID );
					}
					?>
					<item>
					<title><?php \esc_html( the_title_rss() ); ?></title>
					<link><?php \esc_url( the_permalink_rss() ); ?></link>
					<pubDate><?php echo $episode_publish_date; ?></pubDate>
					<dc:creator><?php \esc_html_e( $feed_episode_author ); ?></dc:creator>
					<guid isPermaLink="false"><?php the_guid(); ?></guid>
					<description><![CDATA[<?php echo $episode_content; ?>]]></description>
					<itunes:subtitle><![CDATA[<?php echo $episode_itunes_subtitle; ?>]]></itunes:subtitle>
					<?php echo ( $episode_keywords ) ? "<itunes:keywords>$episode_keywords</itunes:keywords>" : ''; ?>
					<?php echo ( $episode_itunes_type ) ? "<itunes:episodeType>$episode_itunes_type</itunes:episodeType>" : ''; ?>
					<?php echo ( $episode_itunes_title ) ? "<itunes:title><![CDATA[$episode_itunes_title]]></itunes:title>" : ''; ?>
					<?php echo ( $itunes_episode_number ) ? "<itunes:episode>$itunes_episode_number</itunes:episode>" : ''; ?>
					<?php echo ( $episode_itunes_season_number ) ? "<itunes:season>$episode_itunes_season_number</itunes:season>" : ''; ?>
					<?php echo ( 'off' == $feed_lighten && $current_post_index <= 10 ) ? "<content:encoded><![CDATA[$episode_content]]></content:encoded>" : ''; ?>
					<enclosure url="<?php echo \esc_url( $feed_episode_enclosure_link ); ?>" length="<?php \esc_attr_e( $feed_episode_enclosure_size ); ?>" type="<?php \esc_attr_e( $feed_episode_file_type ); ?>"></enclosure>
					<?php echo ( 'off' == $feed_lighten && $current_post_index <= 10 ) ? "<itunes:summary><![CDATA[$episode_itunes_summary]]></itunes:summary>" : ''; ?>
					<?php echo ( $wpp_feed_episode_image ) ? '<itunes:image  href="' . \esc_url( $wpp_feed_episode_image ) . '"></itunes:image>' : ''; ?>
					<itunes:explicit><?php \esc_attr_e( $file_episode_explicit ); ?></itunes:explicit>
					<itunes:block><?php \esc_html_e( $feed_episode_block ); ?></itunes:block>
					<itunes:duration><?php \esc_attr_e( $feed_episode_enclosure_duration ); ?></itunes:duration>
					
					<?php if ( 'off' == $feed_lighten ) : ?>
						<googleplay:description><![CDATA[<?php echo $episode_google_description; ?>]]></googleplay:description>
						<?php echo ( $wpp_feed_episode_image ) ? '	<googleplay:image href="' . \esc_url( $wpp_feed_episode_image ) . '""></googleplay:image>' : ''; ?>
						<googleplay:explicit><?php \esc_html_e( $feed_episode_google_explicit ); ?></googleplay:explicit>
						<googleplay:block><?php \esc_html_e( $feed_episode_block ); ?></googleplay:block>
					<?php endif;// feed_lighten ?>
					</item>
					<?php
				endwhile;
	endif;// $q_episode->have_posts( )

			?>

	</channel>


</rss>
			<?php
			do_action( 'wpp_after_feed' );
			// \wp_die();
		}

	}//end class

endif;
// Initialize the class on init hook to ensure translations are loaded
add_action( 'init', function() {
	global $wpp_podcast_feed;
	$wpp_podcast_feed = new Wpp_Podcast_Feed();
}, 5 );



