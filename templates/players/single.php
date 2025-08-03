<?php

$player_data = [
	'url' => $episode_enclosure_link,
];
	wp_localize_script( 'wp_podcastify_frontend', 'podcastify_uid_' . $uid, $player_data );
	$wpp_spc_limit          = apply_filters( 'wpp_single_character_limit', 250 );
	$episode_content_length = strlen( $episode_content );
?>
<div class="wppfy-container wppfy wppfy-player wppfy-single-player" data-pid="<?php echo esc_attr( $uid ); ?>" data-episode-id="<?php echo esc_attr( $post_id ); ?>">
	<?php if ( true ) : ?>
		<div class="wppfy-img-description">
			<div class="wppfy-image-container" style="background-image:url(<?php echo WP_PODCASTIFY_URL . 'assets/images/thumbnail.png'; ?>)">
			</div>
			<div class="wppfy-description">
				<h2><?php esc_html_e( $episode_title, 'podcastify' ); ?></h2>
					<?php if ( $episode_content && $post_id != get_the_ID() ) : ?>
						
						<p class="<?php echo ( $episode_content_length > $wpp_spc_limit ) ? 'wppfy-short-text' : ''; ?> wppfy-single-content">
						<?php
						if ( $episode_content_length > $wpp_spc_limit ) {
							esc_html_e( substr( $episode_content, 0, ( $wpp_spc_limit ) ), 'podcastify' );
						} else {
							esc_html_e( $episode_content, 'podcastify' );
						}
						?>
					<span class="wppfy-text-more">... </span><?php if ( $episode_content_length > $wpp_spc_limit ) : ?><span class="wppfy-long-text"><?php esc_html_e( substr( $episode_content, $wpp_spc_limit, $episode_content_length ), 'podcastify' ); ?><span class="wppfy-text-less">less</span></span>
					<?php endif; ?>
					</p>
				<?php endif; ?>
				<div class="wppfy-mobile-rspns-container">
					<div class="wppfy-responsive-wrapper">
						<audio preload="metadata" style="display: none;">
							<source src="<?php echo esc_url( $episode_enclosure_link ); ?>" type="audio/mpeg">
							Your browser does not support the audio element.
						</audio>
						<div class="wppfy-player-setting">
							<div class="wppfy-play-pause">
								<span class="wppfy-backward"><i class="fas fa-undo-alt"></i> 15</span>
								<span class="wppfy-play"><i class="fas fa-play-circle"></i></span>
								<span class="wppfy-pause" style="display: none;"><i class="fas fa-pause-circle"></i></span>
								<span class="wppfy-forward">30 <i class="fas fa-redo-alt"></i></span>
							</div>
							<div class="wppfy-range-time">
							<div class="wppfy-progress">
								<div class="current-slide"></div>
								<span class="dragger">
									<span>00:00</span>
								</span>
							</div>
								<div class="wppfy-time-volume">
									<div class="wppfy-volume">
										<div class="wppfy-volume-control"></div>
										<div class="wppfy-volume-icon-wrap">
											<span class="wppfy-volume-up"><i class="fas fa-volume-up"></i></span>
											<span class="wppfy-volume-off"><i class="fas fa-volume-mute"></i></span>
										</div>
									</div>
									<div class="wppfy-time">
										<span class="current-time">00:00</span>/
										<span class="total-time">00:00</span>
									</div>
								</div>
							</div>
						</div>

						<?php if ( 'off' !== $player_meta['show_player_meta'] ) : ?>
							<div class="wppfy-button-wrapper">
								<?php if ( $player_meta['download'] ) : ?>
									<div class="wppfy-download">
										<a href="<?php echo esc_url( $player_meta['download_link'] ); ?>">
											<span class="wppfy-download-icon"><i class="fas fa-arrow-down"></i></span>
											<span class="wppfy-download-count"><?php echo esc_html( $downloads_count ); ?></span>
										</a>
								</div>
								<?php endif;// download button ?>
								<?php if ( $player_meta['like'] ) : ?>
									<div class="wppfy-like">
										<span class="wppfy-like-icon"><i class="fas fa-heart"></i></span>
										<span class="wppfy-like-count"><?php echo esc_html( $likes_count ); ?></span>
								</div>
								<?php endif; // like button ?>
								<?php if ( $player_meta['share'] ) : ?>
									<div class="wppfy-share-wrapper">
										<div class="wppfy-share"><span class="wppfy-share-icon"><i class="fas fa-share"></i></span></div>
										<div class="wppfy-share-links-wrapper">
											<ul class="wppfy-share-links social">
												<?php if ( isset( $player_meta['share_network']['facebook'] ) ) : ?>
													<li><a href="<?php echo esc_url( $fb_share_link ); ?>"  target='_blank' class="wppfy-facebook"><i class="fab fa-facebook-f"></i></a></li>
												<?php endif;// facebook share ?>
												<?php if ( isset( $player_meta['share_network']['twitter'] ) ) : ?>
													<li><a href="<?php echo esc_url( $twitter_share_link ); ?>" target='_blank' class="wppfy-twitter"><i class="fab fa-twitter"></i></a></li>
												<?php endif;// twitter share ?>
												<?php if ( isset( $player_meta['share_network']['linkedin'] ) ) : ?>
													<li><a href="<?php echo esc_url( $linked_share_link ); ?>" target='_blank' class="wppfy-linkedin"><i class="fab fa-linkedin"></i></a></li>
												<?php endif;// linkedin share ?>
												<?php do_action( 'wpp_single_share_links', $post_id ); ?>
											</ul>
										</div>
									</div>
								<?php endif;// share button ?>

								<?php if ( $player_meta['subscribe'] ) : ?>
									<div class="wppfy-share-wrapper subscribe">
										<div class="wppfy-share"><span class="wppfy-share-icon"><i class="fas fa-rss"></i></span></div>
										<div class="wppfy-share-links-wrapper">
											<ul class="wppfy-share-links subscribe">
												<?php if ( $player_meta['subscribe_network']['rss'] ) : ?>
													<li><a href="<?php echo esc_url( $player_meta['subscribe_network']['rss'] ); ?>" target='_blank' class="wppfy-rss"><i class="fas fa-rss"></i></a></li>
												<?php endif; // Rss feed. ?> 
												<?php if ( $player_meta['subscribe_network']['apple'] ) : ?>
													<li><a href="<?php echo esc_url( $player_meta['subscribe_network']['apple'] ); ?>" target='_blank' class="wppfy-apple"><i class="fas fa-podcast"></i></a></li>
												<?php endif; // Apple feed. ?>
												<?php if ( $player_meta['subscribe_network']['google'] ) : ?>
													<li><a href="<?php echo esc_url( $player_meta['subscribe_network']['google'] ); ?>" target='_blank' class="wppfy-google-cast"><i class="fab fa-google"></i></a></li>
												<?php endif; // GoogleCast feed. ?>
												<?php if ( $player_meta['subscribe_network']['spotify'] ) : ?>
													<li><a href="<?php echo esc_url( $player_meta['subscribe_network']['spotify'] ); ?>" target='_blank' class="wppfy-spotify"><i class="fab fa-spotify"></i></a></li>
												<?php endif; // Spotify feed. ?>
												<?php if ( $player_meta['subscribe_network']['stitcher'] ) : ?>
													<li><a href="<?php echo esc_url( $player_meta['subscribe_network']['stitcher'] ); ?>" target='_blank' class="wppfy-stitcher"><i class="fas fa-print fa-rotate-270"></i></a></li>
												<?php endif; // Spotify feed. ?>
												<?php do_action( 'wpp_single_subscribe_links', $post_id ); ?>
											</ul>
										</div>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	<?php endif; // show image / description for shortcode. ?>


</div>


