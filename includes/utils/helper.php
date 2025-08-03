<?php
/**
 * Helper functions.
 *
 * @package Podcastify
 */

namespace Podcastify\Utils;

  /**
   * Itunes Podcast categories.
   *
   * @see https://www.buzzsprout.com/blog/apple-podcast-categories
   * @since 1.0.0
   * @return array
   */
function wpp_get_podcast_categories():array {

	$categories = [
		'arts'                  => __( 'Arts', 'podcastify' ),
		'business'              => __( 'Business', 'podcastify' ),
		'comedy'                => __( 'Comedy', 'podcastify' ),
		'education'             => __( 'Education', 'podcastify' ),
		'fiction'               => __( 'Fiction', 'podcastify' ),
		'government'            => __( 'Government', 'podcastify' ),
		'history'               => __( 'History', 'podcastify' ),
		'health-fitness'        => __( 'Health & Fitness', 'podcastify' ),
		'kids-family'           => __( 'Kids & Family', 'podcastify' ),
		'leisure'               => __( 'Leisure', 'podcastify' ),
		'music'                 => __( 'Music', 'podcastify' ),
		'news'                  => __( 'News', 'podcastify' ),
		'religion-spirituality' => __( 'Religion & Spirituality', 'podcastify' ),
		'science'               => __( 'Science', 'podcastify' ),
		'society-culture'       => __( 'Society & Culture', 'podcastify' ),
		'sports'                => __( 'Sports', 'podcastify' ),
		'technology'            => __( 'Technology', 'podcastify' ),
		'true-crime'            => __( 'True Crime', 'podcastify' ),
		'tv-film'               => __( 'TV & Film', 'podcastify' ),
	];
		return apply_filters( 'wpp_podcast_categories', $categories );
}




 /**
  * Itunes Podcast subcategories.
  *
  * @see https://www.buzzsprout.com/blog/apple-podcast-categories
  * @since 1.0.0
  * @return array
  */
function wpp_get_podcast_sub_categories():array {

	$sub_categories = [
		'arts'                  => [
			'books'           => __( 'Books' ),
			'design'          => __( 'Design' ),
			'fashion-beauty'  => __( 'Fashion & Beauty' ),
			'performing-arts' => __( 'Performing Arts' ),
			'visual-arts'     => __( 'Visual Arts' ),
		],
		'business'              => [
			'careers'          => __( 'Careers', 'podcastify' ),
			'entrepreneurship' => __( 'Entrepreneurship', 'podcastify' ),
			'investing'        => __( 'Investing', 'podcastify' ),
			'management'       => __( 'Management', 'podcastify' ),
			'non-profit'       => __( 'Non-Profit', 'podcastify' ),
		],
		'comedy'                => [
			'comedy-interviews' => __( 'Comedy Interviews', 'podcastify' ),
			'improv'            => __( 'Improv', 'podcastify' ),
			'stand-up'          => __( 'Stand-Up', 'podcastify' ),
		],
		'education'             => [
			'courses'           => __( 'Courses', 'podcastify' ),
			'how-to'            => __( 'How To', 'podcastify' ),
			'language-learning' => __( 'Language Learning', 'podcastify' ),
			'self-improvement'  => __( 'Self-Improvement', 'podcastify' ),
		],
		'fiction'               => [
			'comedy-fiction'  => __( 'Comedy Fiction', 'podcastify' ),
			'drama'           => __( 'Drama', 'podcastify' ),
			'science-fiction' => __( 'Science Fiction', 'podcastify' ),
		],
		'government',
		'history',
		'health-fitness'        => [
			'alternative-health' => __( 'Alternative Health', 'podcastify' ),
			'fitness'            => __( 'fitness', 'podcastify' ),
			'medicine'           => __( 'medicine', 'podcastify' ),
			'mental-health'      => __( 'Mental Health', 'podcastify' ),
			'nutrition'          => __( 'Nutrition', 'podcastify' ),
			'sexuality'          => __( 'Sexuality', 'podcastify' ),
		],
		'kids-family'           => [
			'education-for-kids' => __( 'Education for Kids', 'podcastify' ),
			'parenting'          => __( 'Parenting', 'podcastify' ),
			'pets-animals'       => __( 'Pets & Animals', 'podcastify' ),
			'stories-for-kids'   => __( 'Stories for Kids', 'podcastify' ),
		],
		'leisure'               => [
			'animation-manga' => __( 'Animation & Manga', 'podcastify' ),
			'automotive'      => __( 'Automotive', 'podcastify' ),
			'aviation'        => __( 'Aviation', 'podcastify' ),
			'crafts'          => __( 'Crafts', 'podcastify' ),
			'games'           => __( 'Games', 'podcastify' ),
			'hobbies'         => __( 'Hobbies', 'podcastify' ),
			'home-garden'     => __( 'Home & Garden', 'podcastify' ),
			'video-games'     => __( 'Video Games', 'podcastify' ),
		],
		'music'                 => [
			'music-commentary' => __( 'Music Commentary', 'podcastify' ),
			'music-history	'  => __( 'Music History', 'podcastify' ),
			'music-interviews' => __( 'Music Interviews', 'podcastify' ),
		],
		'news'                  => [
			'business-news'      => __( 'Business News', 'podcastify' ),
			'daily-news'         => __( 'Daily News', 'podcastify' ),
			'entertainment-news' => __( 'Entertainment News', 'podcastify' ),
			'news-commentary'    => __( 'News Commentary', 'podcastify' ),
			'politics'           => __( 'Politics', 'podcastify' ),
			'sports-news'        => __( 'Sports News', 'podcastify' ),
			'tech-News'          => __( 'Tech News', 'podcastify' ),
		],
		'religion-spirituality' => [
			'buddhism'     => __( 'Buddhism', 'podcastify' ),
			'christianity' => __( 'Christianity', 'podcastify' ),
			'hinduism'     => __( 'Hinduism', 'podcastify' ),
			'islam'        => __( 'Islam', 'podcastify' ),
			'judaism'      => __( 'Judaism', 'podcastify' ),
			'religion'     => __( 'Religion', 'podcastify' ),
			'spirituality' => __( 'Spirituality', 'podcastify' ),
		],
		'science'               => [
			'astronomy'        => __( 'Astronomy', 'podcastify' ),
			'chemistry'        => __( 'Chemistry', 'podcastify' ),
			'earth-sciences'   => __( 'Earth Sciences', 'podcastify' ),
			'life-sciences'    => __( 'Life Sciences', 'podcastify' ),
			'mathematics'      => __( 'Mathematics', 'podcastify' ),
			'natural-sciences' => __( 'Natural Sciences', 'podcastify' ),
			'nature'           => __( 'Nature', 'podcastify' ),
			'physics'          => __( 'Physics', 'podcastify' ),
			'social-sciences'  => __( 'Social Sciences', 'podcastify' ),
		],
		'society-culture'       => [
			'Documentary'       => __( 'Documentary', 'podcastify' ),
			'personal-journals' => __( 'Personal Journals', 'podcastify' ),
			'philosophy'        => __( 'Philosophy', 'podcastify' ),
			'places-travel'     => __( 'Places & Travel', 'podcastify' ),
			'relationships'     => __( 'Relationships', 'podcastify' ),
		],
		'sports'                => [
			'baseball'       => __( 'Baseball', 'podcastify' ),
			'basketball'     => __( 'Basketball', 'podcastify' ),
			'cricket'        => __( 'Cricket', 'podcastify' ),
			'fantasy-sports' => __( 'Fantasy Sports', 'podcastify' ),
			'football'       => __( 'Football', 'podcastify' ),
			'hockey'         => __( 'Hockey', 'podcastify' ),
			'rugby'          => __( 'Rugby', 'podcastify' ),
			'soccer'         => __( 'Soccer', 'podcastify' ),
			'swimming'       => __( 'Swimming', 'podcastify' ),
			'tennis'         => __( 'Tennis', 'podcastify' ),
			'volleyball'     => __( 'Volleyball', 'podcastify' ),
			'wilderness'     => __( 'Wilderness', 'podcastify' ),
			'wrestling'      => __( 'Wrestling', 'podcastify' ),
		],
		'technology',
		'true-crime',
		'tv-film'               => [
			'after-shows'     => __( 'After Shows', 'podcastify' ),
			'film-history'    => __( 'Film History', 'podcastify' ),
			'film-interviews' => __( 'Film Interviews', 'podcastify' ),
			'tv-reviews'      => __( 'TV Reviews', 'podcastify' ),
		],
	];
		return apply_filters( 'wpp_podcast_sub_categories', $sub_categories );
}



/**
 * Podcast feed language.
 *
 * @since 1.0.0
 * @return array
 */
function wpp_podcast_langs():array {
	$langs          = [];
	$langs['af']    = __( 'Afrikaans', 'podcatify' );
	$langs['sq']    = __( 'Albanian', 'podcatify' );
	$langs['ar']    = __( 'Arabic', 'podcatify' );
	$langs['ar-sa'] = __( 'Arabic (Saudi Arabia)', 'podcatify' );
	$langs['ar-eg'] = __( 'Arabic (Egypt)', 'podcatify' );
	$langs['ar-dz'] = __( 'Arabic (Algeria)', 'podcatify' );
	$langs['ar-tn'] = __( 'Arabic (Tunisia)', 'podcatify' );
	$langs['ar-ye'] = __( 'Arabic (Yemen)', 'podcatify' );
	$langs['ar-jo'] = __( 'Arabic (Jordan)', 'podcatify' );
	$langs['ar-kw'] = __( 'Arabic (Kuwait)', 'podcatify' );
	$langs['ar-bh'] = __( 'Arabic (Bahrain)', 'podcatify' );
	$langs['eu']    = __( 'Basque', 'podcatify' );
	$langs['be']    = __( 'Belarusian', 'podcatify' );
	$langs['bg']    = __( 'Bulgarian', 'podcatify' );
	$langs['ca']    = __( 'Catalan', 'podcatify' );
	$langs['zh-cn'] = __( 'Chinese (Simplified)', 'podcatify' );
	$langs['zh-tw'] = __( 'Chinese (Traditional)', 'podcatify' );
	$langs['hr']    = __( 'Croatian', 'podcatify' );
	$langs['cs']    = __( 'Czech', 'podcatify' );
	$langs['cr']    = __( 'Cree', 'podcatify' );
	$langs['da']    = __( 'Danish', 'podcatify' );
	$langs['nl']    = __( 'Dutch', 'podcatify' );
	$langs['nl-be'] = __( 'Dutch (Belgium)', 'podcatify' );
	$langs['nl-nl'] = __( 'Dutch (Netherlands)', 'podcatify' );
	$langs['en']    = __( 'English', 'podcatify' );
	$langs['en-au'] = __( 'English (Australia)', 'podcatify' );
	$langs['en-bz'] = __( 'English (Belize)', 'podcatify' );
	$langs['en-ca'] = __( 'English (Canada)', 'podcatify' );
	$langs['en-ie'] = __( 'English (Ireland)', 'podcatify' );
	$langs['en-jm'] = __( 'English (Jamaica)', 'podcatify' );
	$langs['en-nz'] = __( 'English (New Zealand)', 'podcatify' );
	$langs['en-ph'] = __( 'English (Phillipines)', 'podcatify' );
	$langs['en-za'] = __( 'English (South Africa)', 'podcatify' );
	$langs['en-tt'] = __( 'English (Trinidad)', 'podcatify' );
	$langs['en-gb'] = __( 'English (United Kingdom)', 'podcatify' );
	$langs['en-us'] = __( 'English (United States)', 'podcatify' );
	$langs['en-zw'] = __( 'English (Zimbabwe)', 'podcatify' );
	$langs['et']    = __( 'Estonian', 'podcatify' );
	$langs['fo']    = __( 'Faeroese', 'podcatify' );
	$langs['fi']    = __( 'Finnish', 'podcatify' );
	$langs['fr']    = __( 'French', 'podcatify' );
	$langs['fr-be'] = __( 'French (Belgium)', 'podcatify' );
	$langs['fr-ca'] = __( 'French (Canada)', 'podcatify' );
	$langs['fr-fr'] = __( 'French (France)', 'podcatify' );
	$langs['fr-lu'] = __( 'French (Luxembourg)', 'podcatify' );
	$langs['fr-mc'] = __( 'French (Monaco)', 'podcatify' );
	$langs['fr-ch'] = __( 'French (Switzerland)', 'podcatify' );
	$langs['gl']    = __( 'Galician', 'podcatify' );
	$langs['gd']    = __( 'Gaelic', 'podcatify' );
	$langs['de']    = __( 'German', 'podcatify' );
	$langs['de-at'] = __( 'German (Austria)', 'podcatify' );
	$langs['de-de'] = __( 'German (Germany)', 'podcatify' );
	$langs['de-li'] = __( 'German (Liechtenstein)', 'podcatify' );
	$langs['de-lu'] = __( 'German (Luxembourg)', 'podcatify' );
	$langs['de-ch'] = __( 'German (Switzerland)', 'podcatify' );
	$langs['el']    = __( 'Greek', 'podcatify' );
	$langs['haw']   = __( 'Hawaiian', 'podcatify' );
	$langs['he_IL'] = __( 'Hebrew', 'podcatify' );
	$langs['hu']    = __( 'Hungarian', 'podcatify' );
	$langs['is']    = __( 'Icelandic', 'podcatify' );
	$langs['in']    = __( 'Indonesian', 'podcatify' );
	$langs['ga']    = __( 'Irish', 'podcatify' );
	$langs['it']    = __( 'Italian', 'podcatify' );
	$langs['hi']    = __( 'Hindi', 'podcatify' );
	$langs['it-it'] = __( 'Italian (Italy)', 'podcatify' );
	$langs['it-ch'] = __( 'Italian (Switzerland)', 'podcatify' );
	$langs['ja']    = __( 'Japanese', 'podcatify' );
	$langs['ko']    = __( 'Korean', 'podcatify' );
	$langs['mk']    = __( 'Macedonian', 'podcatify' );
	$langs['no']    = __( 'Norwegian', 'podcatify' );
	$langs['pa']    = __( 'Punjabi', 'podcatify' );
	$langs['pl']    = __( 'Polish', 'podcatify' );
	$langs['pt']    = __( 'Portuguese', 'podcatify' );
	$langs['pt-br'] = __( 'Portuguese (Brazil)', 'podcatify' );
	$langs['pt-pt'] = __( 'Portuguese (Portugal)', 'podcatify' );
	$langs['ro']    = __( 'Romanian', 'podcatify' );
	$langs['ro-mo'] = __( 'Romanian (Moldova)', 'podcatify' );
	$langs['ro-ro'] = __( 'Romanian (Romania)', 'podcatify' );
	$langs['ru']    = __( 'Russian', 'podcatify' );
	$langs['ru-mo'] = __( 'Russian (Moldova)', 'podcatify' );
	$langs['ru-ru'] = __( 'Russian (Russia)', 'podcatify' );
	$langs['sr']    = __( 'Serbian', 'podcatify' );
	$langs['sk']    = __( 'Slovak', 'podcatify' );
	$langs['sl']    = __( 'Slovenian', 'podcatify' );
	$langs['es']    = __( 'Spanish', 'podcatify' );
	$langs['es-ar'] = __( 'Spanish (Argentina)', 'podcatify' );
	$langs['es-bo'] = __( 'Spanish (Bolivia)', 'podcatify' );
	$langs['es-cl'] = __( 'Spanish (Chile)', 'podcatify' );
	$langs['es-co'] = __( 'Spanish (Colombia)', 'podcatify' );
	$langs['es-cr'] = __( 'Spanish (Costa Rica)', 'podcatify' );
	$langs['es-do'] = __( 'Spanish (Dominican Republic)', 'podcatify' );
	$langs['es-ec'] = __( 'Spanish (Ecuador)', 'podcatify' );
	$langs['es-sv'] = __( 'Spanish (El Salvador)', 'podcatify' );
	$langs['es-gt'] = __( 'Spanish (Guatemala)', 'podcatify' );
	$langs['es-hn'] = __( 'Spanish (Honduras)', 'podcatify' );
	$langs['es-mx'] = __( 'Spanish (Mexico)', 'podcatify' );
	$langs['es-ni'] = __( 'Spanish (Nicaragua)', 'podcatify' );
	$langs['es-pa'] = __( 'Spanish (Panama)', 'podcatify' );
	$langs['es-py'] = __( 'Spanish (Paraguay)', 'podcatify' );
	$langs['es-pe'] = __( 'Spanish (Peru)', 'podcatify' );
	$langs['es-pr'] = __( 'Spanish (Puerto Rico)', 'podcatify' );
	$langs['es-es'] = __( 'Spanish (Spain)', 'podcatify' );
	$langs['es-uy'] = __( 'Spanish (Uruguay)', 'podcatify' );
	$langs['es-ve'] = __( 'Spanish (Venezuela)', 'podcatify' );
	$langs['sv']    = __( 'Swedish', 'podcatify' );
	$langs['sv-fi'] = __( 'Swedish (Finland)', 'podcatify' );
	$langs['sv-se'] = __( 'Swedish (Sweden)', 'podcatify' );
	$langs['ta']    = __( 'Tamil', 'podcatify' );
	$langs['th']    = __( 'Thai', 'podcatify' );
	$langs['bo']    = __( 'Tibetan', 'podcatify' );
	$langs['tr']    = __( 'Turkish', 'podcatify' );
	$langs['uk']    = __( 'Ukranian', 'podcatify' );
	$langs['ve']    = __( 'Venda', 'podcatify' );
	$langs['vi']    = __( 'Vietnamese', 'podcatify' );
	$langs['zu']    = __( 'Zulu', 'podcatify' );

	return apply_filters( 'wpp_podcast_lang', $langs );
}



/**
 * Get Setting  options.0
 *
 * @since 1.0.0
 * @param string $option
 * @param string $section
 * @param string $default
 * @return mixed
 */
function wpp_get_setting( string $option, string $section, $default = '' ) {

	$options = get_option( $section );

	if ( isset( $options[ $option ] ) ) {
		return $options[ $option ];
	}

	return $default;
}


/**
 * Get home URL.
 *
 * @since
 *
 * @return mixed|String
 */
function wpp_home_url():string {
	return get_home_url();
}



/**
 * Get episode query builder.
 *
 * @since 1.0.0
 * @param array $args
 * @return array
 */
function wpp_get_episodes_query_args( array $args ): array {

	// Need 10up rule to apply for get posts
	$query = [];
	if ( isset( $args['posts_per_page'] ) ) {
		$query['posts_per_page'] = $args['post_per_page'];
	}
	$query['post_type']  = 'episode';
	$query['post_stats'] = $args['post_stats'] ?? 'publish';

	/**
	 * Bad practice to use post__in or post__not_in .
	 *
	 * @see https://10up.github.io/Engineering-Best-Practices/php/
	 */
	if ( isset( $query['post__in'] ) ) {
		$query['post__in'] = $episode_ids;
	}
	/**
	 * Default false mark true if we don't need pagination it's stop to run extra query for pagination.
	 *
	 * @see  https://10up.github.io/Engineering-Best-Practices/php/
	 */
	$query['no_found_rows'] = $args['no_found_rows'] ?? true;

	if ( isset( $args['series'] ) ) {
		$query['tax_query'] = [
			[
				'taxonomy' => 'episode_series',
				'terms'    => $args['series'],
			],
		];
	}
		// use tax operator for exclude.
	if ( isset( $args['tax_operator'] ) ) {
		$query['tax_query'] ['operator'] = esc_attr( $args['tax_operator'] );
	}

	// podcast file exist check.
	$query['meta_query'] = [
		[
			'key'     => 'podcast_file',
			'compare' => '!=',
			'value'   => '',
		],
	];

	if ( isset( $args['extra'] ) ) {
		$query = array_merge( $query, $args['extra'] );
	}

	return apply_filters( 'wpp_episodes_query_args', $query );
}


/**
 * Get episode enclosure media link.
 *
 * @since 1.0.0
 * @param integer $episode_id
 * @param boolean $only_file_link
 * @return string
 */
function wpp_get_episode_enclosure_link( int $episode_id, $only_file_link = false ): string {
	$media_file = wpp_get_episode_meta( apply_filters( 'wpp_media_file_meta_key', 'podcast_file' ), $episode_id );

	if ( $only_file_link ) {
		return $media_file;
	}
	do_action( 'wpp_enclosure_link', $media_file );
	$link = '';
	if ( get_option( 'permalink_structure' ) ) {
		$file_type = wp_check_filetype( wp_basename( $media_file ) );
		$ext       = $file_type['ext'] ?? 'mp3';
		$episode   = get_post( $episode_id );
		$link      = wpp_home_url() . '/podcastify-download/' . $episode_id . '/' . $episode->post_name . '.' . $ext;
	} else {
		$link = $media_file;
	}

	return apply_filters( 'wpp_enclosure_link', $link, $media_file, $episode_id );
}


/**
 * Get episode meta.
 *
 * @since 1.0.0
 * @param string  $key
 * @param integer $episode_id
 * @param string  $default
 * @param boolean $single
 * @return mixed
 */
function wpp_get_episode_meta( string $key, int $episode_id, $default = '', $single = true ) {
	$value = get_post_meta( $episode_id, $key, $single );
	if ( ! $value && $default ) {
		return $default;
	} else {
		return $value;
	}

}


function dd() {
	// Debug function disabled to prevent JSON response errors
	// echo '<pre>';
	// array_map(
	//	function( $x ) {
	//			var_dump( $x );
	//	},
	//	func_get_args()
	// );
	// die;
}


	/**
	 * Get series data if not exist get default data.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $field field name.
	 * @param integer $series_id series id.
	 * @param string  $default default value.
	 * @return mixed
	 */
function get_series_data( $field, $series_id = '', $default = '' ) {

	$parent_data = wpp_get_setting( $field, 'wpp_feed' );

	$series_data = wpp_get_setting( $field . '_' . $series_id, 'wpp_feed' );

	if ( $series_data ) {
		return $series_data;
	}
	if ( ! empty( $default ) && ! $parent_data ) {
		$parent_data = $default;
	}
	return $parent_data;
}


	/**
	 * Get only series data.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $field field name.
	 * @param integer $series_id series id.
	 * @param string  $default default value.
	 * @return mixed
	 */
function get_only_series_data( $field, $series_id = '', $default = '' ) {

	if ( $series_id ) {
		$field = $field . '_' . $series_id;
	}
	$series_data = wpp_get_setting( $field, 'wpp_feed' );

	if ( $series_data ) {
		return $series_data;
	}

	$series_data = $default;

	return \apply_filters( 'wpp_only_series_data', $series_data, $series_id, $default );
}


/**
 * convert URL to path of wp-content.
 *
 * @since 1.0.0
 * @param string $url
 * @return string
 */
function wpp_convert_url_to_path( $url ) {
	return str_replace(
		wp_get_upload_dir()['baseurl'],
		wp_get_upload_dir()['basedir'],
		$url
	);
}



	/**
	 * Add podcast player
	 *
	 * @since 1.0.0
	 * @param integer $post_id
	 * @return string Html markup.
	 */
function wpp_podcast_player( int $post_id ) {

	if ( is_feed() || is_admin( ) ) {
		return;
	}

	$episode_type = wpp_get_episode_meta( 'episode_type', $post_id );

	$player_content = '';
	if ( 'audio' === $episode_type ) {
		$player_content = wpp_audio_player( $post_id );
	} else {
		$player_content = wpp_video_player( $post_id );
	}

	return $player_content;
}



	/**
	 * Get Audio play markup.
	 *
	 * @param integer $post_id
	 * @return string $content Html markup.
	 */
function wpp_audio_player( int $post_id ):string {

	do_action( 'wpp_audio_payer_before', $post_id );
	$episode                = get_post( $post_id );
	$episode_title          = $episode->post_title;
	$episode_enclosure_link = wpp_get_episode_meta( 'podcast_file', $post_id );
	
	// Return empty string if no audio file is provided
	if ( empty( $episode_enclosure_link ) ) {
		return '';
	}
	
	$player_meta            = wpp_player_meta( $post_id );
	$episode_content        = wp_strip_all_tags( get_the_content( $post_id ) );
	$episode_excerpt        = $episode->post_excerpt;
	$uid                    = substr( wp_generate_uuid4(), 0, 5 );
	$downloads_count        = wpp_get_episode_meta( 'download_count', $post_id );
	$likes_count            = wpp_get_episode_meta( 'like_count', $post_id );

	$fb_share_link      = 'https://www.facebook.com/sharer/sharer.php?u=' . get_permalink( $post_id );
	$twitter_share_link = 'https://twitter.com/intent/tweet?url=' . get_permalink( $post_id ) . '&text=';
	$linked_share_link  = 'https://www.linkedin.com/shareArticle?mini=true&url=' . get_permalink( $post_id ) . '&title=&summary=&source=';

	// Get player type from settings
	$player_type = wpp_get_setting( 'player_type', 'wpp_player', 'single' );
	$player_template = WP_PODCASTIFY_PATH . 'templates/players/' . $player_type . '.php';
	
	// Fallback to single player if template doesn't exist
	if ( ! file_exists( $player_template ) ) {
		$player_template = WP_PODCASTIFY_PATH . 'templates/players/single.php';
	}
	
	ob_start();
	require $player_template;
	$content = ob_get_clean();
	do_action( 'wpp_audio_payer_after', $post_id );
	return $content;
}


/**
 * Get video play markup.
 *
 * @since 1.0.0
 * @param integer $post_id
 * @return string $content Html markup.
 */
function wpp_video_player( int $post_id ):string {
	$player_meta = wpp_player_meta( $post_id );
	do_action( 'wpp_video_payer_before', $post_id );
	$episode_title          = get_the_title( $post_id );
	$episode_enclosure_link = wpp_get_episode_meta( 'podcast_file', $post_id );
	
	// Return empty string if no video file is provided
	if ( empty( $episode_enclosure_link ) ) {
		return '';
	}
	ob_start();
	?>
		<video  controls>
			<source src="<?php echo esc_url( $episode_enclosure_link ); ?>" type="video/mp4">
			Your browser does not support HTML5 video.
		</video>
	<?php
	if ( isset( $player_meta['duration'] ) ) {
		echo $player_meta['duration'] . ' | ';
	}
	if ( isset( $player_meta['download'] ) ) {
		echo \sprintf( '<a href="%s"> Download </a>', $player_meta['download'] );
	}
	?>
	<?php
	$content = ob_get_clean();
	do_action( 'wpp_video_payer_after', $post_id );
	return $content;

}


/**
 * Get player meta option.
 *
 * @since 1.0.0
 * @param integer $post_id
 * @return array|mixed
 */
function wpp_player_meta( int $post_id ) {

	$player_meta = wpp_get_setting( 'player_meta', 'wpp_player' );
	
	$player_meta = array_flip( $player_meta );

	$meta                     = [];
	$meta['download_link']    = add_query_arg( 'podcastify-ref', 'download', wpp_get_episode_enclosure_link( $post_id ) );
	$meta['share_network']    = array_flip( wpp_get_setting( 'share_network', 'wpp_player' ) );
	$meta['show_player_meta'] = wpp_get_setting( 'show_meta', 'wpp_player' );
	$p_meta                   = [ 'download', 'like', 'share', 'subscribe' ];
	foreach ( $p_meta as $_meta ) {
		$meta[ $_meta ] = isset( $player_meta[ $_meta ] ) ? true : false;
	}

	$series_id = get_episode_series( $post_id );
	if ( $series_id ) {
		$feed_subscriber             = [];
		$feed_subscriber['rss']      = get_series_feed( $series_id );
		$feed_subscriber['apple']    = get_series_data( 'itune_feed_url', $series_id );
		$feed_subscriber['google']   = get_series_data( 'google_play_feed_url', $series_id );
		$feed_subscriber['spotify']  = get_series_data( 'spotify_feed_url', $series_id );
		$feed_subscriber['stitcher'] = get_series_data( 'stitcher_feed_url', $series_id );
		$meta['subscribe_network']   = apply_filters( 'wpp_feed_subscriber', $feed_subscriber );
	}
	return $meta;
}

/**
 * Help to download the large chuck file.
 *
 * @see http://codeigniter.com/wiki/Download_helper_for_large_files/
 * @since 1.0.0
 * @param string  $file
 * @param boolean $ret_bytes
 * @return mixed
 */
function wpp_readfile_chunked( $file, $ret_bytes = true ) {

	$chunk_size = 1 * ( 1024 * 1024 );
	$cnt        = 0;

	$handle = fopen( $file, 'r' );
	if ( false === $handle ) {
		return false;
	}

	while ( ! feof( $handle ) ) {
		$buffer = fread( $handle, $chunk_size );
		echo $buffer;
		ob_flush();
		flush();

		if ( $ret_bytes ) {
			$cnt += strlen( $buffer );
		}
	}

	$status = fclose( $handle );

	if ( $ret_bytes && $status ) {
		return $cnt;
	}

	return $status;
}


	/**
	 * Pretty number format.
	 *
	 * @since 1.0.0
	 * @see  https://gist.github.com/RadGH/84edff0cc81e6326029c
	 * @param int $n number format.
	 * @return void
	 */
function pretty_number_format( $n ) {
	if ( $n >= 0 && $n < 1000 ) {
		// 1 - 999
		$n_format = floor( $n );
		$suffix   = '';
	} elseif ( $n >= 1000 && $n < 1000000 ) {
		// 1k-999k
		$n_format = floor( $n / 1000 );
		$suffix   = 'K+';
	} elseif ( $n >= 1000000 && $n < 1000000000 ) {
		// 1m-999m
		$n_format = floor( $n / 1000000 );
		$suffix   = 'M+';
	} elseif ( $n >= 1000000000 && $n < 1000000000000 ) {
		// 1b-999b
		$n_format = floor( $n / 1000000000 );
		$suffix   = 'B+';
	} elseif ( $n >= 1000000000000 ) {
		// 1t+
		$n_format = floor( $n / 1000000000000 );
		$suffix   = 'T+';
	}

	return ! empty( $n_format . $suffix ) ? $n_format . $suffix : 0;
}


	/**
	 * PLayer color customization.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
function player_color_customization() {
	$css                     = '';
	$single_control_color    = wpp_get_setting( 'control_color', 'wpp_player', '' );
	$single_background_color = wpp_get_setting( 'background_color', 'wpp_player', '' );
	if ( $single_control_color ) {
		$css = "
			.wppfy-player {
				--custom-single-player-color: $single_control_color;
				color: var(--custom-single-player-color);
			}
			.wppfy-player .wppfy-play i,
			.wppfy-player .wppfy-pause i {
				border-radius: 50%;
				
			}
			.wppfy-player .wppfy-play i::after,
			.wppfy-player .wppfy-pause i::after{
				border-color: var(--custom-single-player-color);
			}
			.wppfy-player .range-slider-track::before {
				background-color: var(--custom-single-player-color) !important;
				opacity: 0.3;
			}
			.wppfy-player .wppfy-volume .wppfy-volume-control .range-slider-track > .dragger::after {
				background-color: var(--custom-single-player-color) !important;
			}
			.wppfy-player .wppfy-button-wrapper button {
				background-color: var(--custom-single-player-color);
				color: #fff;
			}
			.wppfy-player .wppfy-button-wrapper button:hover {
				background-color: var(--custom-single-player-color);
				color: #fff;
			}
			.wppfy-player .wppfy-button-wrapper .wppfy-download a {

				color: var(--custom-single-player-color) !important;
			}
			.wppfy-player .wppfy-button-wrapper .wppfy-share-links {
				left: 50%;
				transform: translateX(-50%);
				background-color: var(--custom-single-player-color);
			}
			.wppfy-player .wppfy-button-wrapper .wppfy-share-links-wrapper::after {
				background: var(--custom-single-player-color);
			}
			.wppfy-player .range-slider .dragger {
				background-color: var(--custom-single-player-color) !important;
			}
			.wppfy-player .range-slider .current-slide {
				background-color: var(--custom-single-player-color) !important;
			}
			.wppfy-share-links::-webkit-scrollbar-track {
				background-color: var(--custom-single-player-color);
			}
			.wppfy-share-links::-webkit-scrollbar {
				background-color: var(--custom-single-player-color);
			}
			.wppfy-share-links::-webkit-scrollbar-thumb {
				background-color: var(--custom-single-player-color);
				-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.5);
			}
			";
	}

	if ( $single_background_color ) {
		$css .= "
			.wppfy-player {
				background-color: $single_background_color;
			}
			";
	}

	return $css;
}

/**
 * Get episode series.
 *
 * @since 1.0.0
 *
 * @param integer $episode_id episode id.
 * @param string  $get_by get by name.
 * @return void
 */
function get_episode_series( int $episode_id, $get_by = '' ) {
	$series = get_the_terms( $episode_id, 'episode_series' );

	if ( $series && ! is_wp_error( $series ) && count( $series ) > 0 ) {
		$episode_series = $series[0]->term_id;
		if ( 'name' == $get_by ) {
			$episode_series = $series[0]->slug;
		}
		return $episode_series;
	}
	return false;
}

/**
 * Series rss  feed url.
 *
 * @since 1.0.0
 * @param integer $term_id
 * @return string
 */
function get_series_feed( int $term_id ):string {
	$series      = get_term( $term_id );
	$series_slug = $series->slug;

	if ( get_option( 'permalink_structure' ) ) {
		$feed_slug = apply_filters( 'wpp_feed_slug', $series_slug );
		$feed_url  = wpp_home_url() . '/feed/podcasts/' . $series_slug;
	} else {
		$feed_url = add_query_arg(
			array(
				'feed'              => 'podcasts', // needs to inherit form base feed controller classes/
				'podcastify_series' => $series_slug,
			),
			wpp_home_url()
		);
	}

	return $feed_url;
}


/**
 * Hexa to rgba converter.
 *
 * @since 1.1.0
 * @param string $hex color.
 * @return string gba color.
 */
function hex_to_rgba( $hex ) {
	list($r, $g, $b) = sscanf( $hex, '#%02x%02x%02x' );
	return "$r,$g,$b";
}