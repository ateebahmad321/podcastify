<?php
/**
 * Enhanced Minimal Player Template
 *
 * @package Podcastify
 */

$player_data = [
	'url' => $episode_enclosure_link,
];
wp_localize_script( 'wp_podcastify_frontend', 'podcastify_uid_' . $uid, $player_data );

// Get episode featured image
$episode_image = get_the_post_thumbnail_url( $post_id, 'medium' );

// If no featured image, try episode artwork meta
if ( ! $episode_image ) {
	$episode_artwork = \Podcastify\Utils\wpp_get_episode_meta( 'episode_artwork', $post_id );
	if ( $episode_artwork ) {
		$episode_image = $episode_artwork;
	}
}

// Final fallback to default thumbnail
if ( ! $episode_image ) {
	$episode_image = WP_PODCASTIFY_URL . 'assets/images/thumbnail.png';
}
?>
<div class="wppfy-container wppfy wppfy-player wppfy-minimal-player" data-pid="<?php echo esc_attr( $uid ); ?>" data-episode-id="<?php echo esc_attr( $post_id ); ?>">
	<div class="wppfy-minimal-wrapper">
		<div class="wppfy-minimal-header">
			<div class="wppfy-minimal-episode-info">
				<div class="wppfy-minimal-episode-thumbnail">
					<img src="<?php echo esc_url( $episode_image ); ?>" alt="<?php esc_attr_e( $episode_title, 'podcastify' ); ?>" class="wppfy-minimal-thumbnail-img">
					<div class="wppfy-minimal-play-overlay">
						<span class="wppfy-play"><i class="fas fa-play"></i></span>
						<span class="wppfy-pause" style="display: none;"><i class="fas fa-pause"></i></span>
					</div>
				</div>
				<div class="wppfy-minimal-episode-details">
					<h4 class="wppfy-minimal-title"><?php esc_html_e( $episode_title, 'podcastify' ); ?></h4>
					<?php if ( $episode_content && strlen( $episode_content ) > 0 ) : ?>
						<p class="wppfy-minimal-description"><?php echo esc_html( wp_trim_words( $episode_content, 15, '...' ) ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</div>
		
		<div class="wppfy-minimal-player-controls">
			<audio preload="metadata" style="display: none;">
				<source src="<?php echo esc_url( $episode_enclosure_link ); ?>" type="audio/mpeg">
				Your browser does not support the audio element.
			</audio>
			
			<div class="wppfy-minimal-main-controls">
				<div class="wppfy-minimal-playback-controls">
					<button class="wppfy-minimal-control-btn wppfy-minimal-backward" title="<?php esc_attr_e( 'Rewind 15 seconds', 'podcastify' ); ?>">
						<i class="fas fa-undo-alt"></i>
						<span class="wppfy-minimal-control-text">15</span>
					</button>
					
					<div class="wppfy-minimal-play-pause-main">
						<span class="wppfy-play"><i class="fas fa-play"></i></span>
						<span class="wppfy-pause" style="display: none;"><i class="fas fa-pause"></i></span>
					</div>
					
					<button class="wppfy-minimal-control-btn wppfy-minimal-forward" title="<?php esc_attr_e( 'Forward 30 seconds', 'podcastify' ); ?>">
						<span class="wppfy-minimal-control-text">30</span>
						<i class="fas fa-redo-alt"></i>
					</button>
				</div>
				
				<div class="wppfy-minimal-progress-section">
					<div class="wppfy-minimal-time-current">0:00</div>
					<div class="wppfy-minimal-progress-wrapper">
						<div class="wppfy-minimal-progress">
							<div class="wppfy-minimal-progress-filled"></div>
							<div class="wppfy-minimal-progress-handle"></div>
						</div>
					</div>
					<div class="wppfy-minimal-time-total">0:00</div>
				</div>
				
				<div class="wppfy-minimal-secondary-controls">
					<div class="wppfy-minimal-volume-control">
						<button class="wppfy-minimal-volume-btn" title="<?php esc_attr_e( 'Volume', 'podcastify' ); ?>">
							<i class="fas fa-volume-up wppfy-volume-icon"></i>
						</button>
						<div class="wppfy-minimal-volume-slider" style="display: none;">
							<div class="wppfy-minimal-volume-track">
								<div class="wppfy-minimal-volume-fill"></div>
								<div class="wppfy-minimal-volume-handle"></div>
							</div>
						</div>
					</div>
					
					<div class="wppfy-minimal-speed-control">
						<button class="wppfy-minimal-speed-btn" title="<?php esc_attr_e( 'Playback Speed', 'podcastify' ); ?>">
							<span class="wppfy-minimal-speed-text">1x</span>
						</button>
						<div class="wppfy-minimal-speed-options" style="display: none;">
							<button data-speed="0.75">0.75x</button>
							<button data-speed="1" class="active">1x</button>
							<button data-speed="1.25">1.25x</button>
							<button data-speed="1.5">1.5x</button>
							<button data-speed="2">2x</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<?php if ( 'off' !== $player_meta['show_player_meta'] ) : ?>
			<div class="wppfy-minimal-meta-actions">
				<?php if ( $player_meta['download'] ) : ?>
					<a href="<?php echo esc_url( $player_meta['download_link'] ); ?>" class="wppfy-minimal-meta-action wppfy-minimal-download" title="<?php esc_attr_e( 'Download Episode', 'podcastify' ); ?>">
						<i class="fas fa-download"></i>
						<span><?php esc_html_e( 'Download', 'podcastify' ); ?></span>
						<?php if ( $downloads_count > 0 ) : ?>
							<span class="wppfy-minimal-count"><?php echo esc_html( $downloads_count ); ?></span>
						<?php endif; ?>
					</a>
				<?php endif; ?>
				
				<?php if ( $player_meta['like'] ) : ?>
					<button class="wppfy-minimal-meta-action wppfy-minimal-like" title="<?php esc_attr_e( 'Like Episode', 'podcastify' ); ?>">
						<i class="fas fa-heart"></i>
						<span><?php esc_html_e( 'Like', 'podcastify' ); ?></span>
						<?php if ( $likes_count > 0 ) : ?>
							<span class="wppfy-minimal-count wppfy-like-count"><?php echo esc_html( $likes_count ); ?></span>
						<?php endif; ?>
					</button>
				<?php endif; ?>
				
				<?php if ( $player_meta['share'] ) : ?>
					<div class="wppfy-minimal-share-wrapper">
						<button class="wppfy-minimal-meta-action wppfy-minimal-share-toggle" title="<?php esc_attr_e( 'Share Episode', 'podcastify' ); ?>">
							<i class="fas fa-share-alt"></i>
							<span><?php esc_html_e( 'Share', 'podcastify' ); ?></span>
						</button>
						<div class="wppfy-minimal-share-dropdown" style="display: none;">
							<?php if ( isset( $player_meta['share_network']['facebook'] ) ) : ?>
								<a href="<?php echo esc_url( $fb_share_link ); ?>" target="_blank" class="wppfy-minimal-share-option">
									<i class="fab fa-facebook-f"></i>
									<span><?php esc_html_e( 'Facebook', 'podcastify' ); ?></span>
								</a>
							<?php endif; ?>
							<?php if ( isset( $player_meta['share_network']['twitter'] ) ) : ?>
								<a href="<?php echo esc_url( $twitter_share_link ); ?>" target="_blank" class="wppfy-minimal-share-option">
									<i class="fab fa-twitter"></i>
									<span><?php esc_html_e( 'Twitter', 'podcastify' ); ?></span>
								</a>
							<?php endif; ?>
							<?php if ( isset( $player_meta['share_network']['linkedin'] ) ) : ?>
								<a href="<?php echo esc_url( $linked_share_link ); ?>" target="_blank" class="wppfy-minimal-share-option">
									<i class="fab fa-linkedin"></i>
									<span><?php esc_html_e( 'LinkedIn', 'podcastify' ); ?></span>
								</a>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</div>