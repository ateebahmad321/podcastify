import PodcastifyRangeSlider from './components/prslider';
import {wppSetMeta} from './../shared/helper';
const { doAction } = wp.hooks;
const WPPFY = [];

console.log('Podcastify: Frontend JavaScript loaded');


/**
 * Format time string.
 * 
 * @since 1.0.0
 * @param {integer} time time is seconds.
 * @returns {string} format times string.
 */
function wppFormatTime( seconds ) {
	let h, m, s, result='';/* eslint-disable-line prefer-const */
	// HOURs
	h = Math.floor( seconds/3600 ); /* eslint-disable-line prefer-const */
	seconds -= h*3600;
	if( h ){
		result = 10>h ? `0${h}:` : `${h}:`;
	}
	// MINUTEs
	m = Math.floor( seconds/60 ); /* eslint-disable-line prefer-const */
	seconds -= m*60;
	result += 10>m ? `0${m}:` : `${m}:`;
	// SECONDs
	s=Math.floor( seconds%60 ); /* eslint-disable-line prefer-const */
	result += 10>s ? `0${s}` : s;
	return result;
}

/**
 * Get player element.
 * 
 * @since 1.0.01
 * @param {Object} HTMLElement
 * @returns {Object} HTMLMediaElement
 */
function wppGetPlayer( el ) {
	const player = el.closest( '.wppfy-player' );
	const { pid } = player.dataset;
	return WPPFY[`Podcastify_Player_${pid}`];
}

/**
 * Get episode ID.
 * 
 * @since 1.0.0
 * @param {HTML Element} el 
 */
function wppGetEpisodeID( el ) {
	const player = el.closest( '.wppfy-player' );
	const { episodeId } = player.dataset;
	return episodeId;
}


const allPlayers = document.querySelectorAll( '.wppfy-single-player, .wppfy-minimal-player' );
console.log('Podcastify: Found players:', allPlayers.length);
allPlayers.forEach((player, index) => {
	console.log(`Podcastify: Player ${index + 1} classes:`, player.className);
});

if ( allPlayers ) {
	allPlayers.forEach( player => {
		const uid = player.dataset.pid;
		console.log('Podcastify: Processing player with UID:', uid, 'Classes:', player.className);

		/**
		 * Fire before player initiating.
		 * 
		 * @since 1.0.0
		 */
		doAction( 'wppfy_before_player_initiate', uid );

		// Check if URL exists before creating audio element
		if ( window[`podcastify_uid_${uid}`] && window[`podcastify_uid_${uid}`].url ) {
			WPPFY[`Podcastify_Player_${uid}`] = new Audio( window[`podcastify_uid_${uid}`].url );

			WPPFY[`Podcastify_Player_${uid}`].addEventListener( 'loadeddata', function () {
				const totalDuration = wppFormatTime( WPPFY[`Podcastify_Player_${uid}`].duration );
				// Support both single and minimal player time selectors
				const totalTimeEl = document.querySelector( `[data-pid="${uid}"] .wppfy-time .total-time` ) || 
				                   document.querySelector( `[data-pid="${uid}"] .wppfy-minimal-total-time` ) ||
				                   document.querySelector( `[data-pid="${uid}"] .wppfy-minimal-time-total` );
				if ( totalTimeEl ) totalTimeEl.innerHTML = totalDuration;
			} );

			WPPFY[`Podcastify_Player_${uid}`].addEventListener( 'error', function () {
				console.error( 'Podcastify: Error loading audio file for player', uid );
				const playerEl = document.querySelector( `[data-pid="${uid}"]` );
				if ( playerEl ) playerEl.classList.add( 'wppfy-error' );
			} );

			WPPFY[`Podcastify_Player_${uid}`].addEventListener( 'timeupdate', function () {
			const { currentTime, duration } = WPPFY[`Podcastify_Player_${uid}`];
			const completedPercentage = ( currentTime / duration ) * 100;
			const ProgressBar = document.querySelector( `[data-pid="${uid}"] .wppfy-progress` ) ||
			                   document.querySelector( `[data-pid="${uid}"] .wppfy-minimal-progress` );

			// Support both single and minimal player current time selectors
			const currentTimeEl = document.querySelector( `[data-pid="${uid}"] .wppfy-time .current-time` ) ||
			                     document.querySelector( `[data-pid="${uid}"] .wppfy-minimal-current-time` ) ||
			                     document.querySelector( `[data-pid="${uid}"] .wppfy-minimal-time-current` );
			if ( currentTimeEl ) currentTimeEl.innerHTML = wppFormatTime( currentTime );

			if ( ProgressBar && !ProgressBar.classList.contains( 'dragging' ) ) {
				// Handle single player progress elements
				const dragger = ProgressBar.querySelector( '.dragger > span' );
				const draggerEl = ProgressBar.querySelector( '.dragger' );
				const currentSlide = ProgressBar.querySelector( '.current-slide' );
				
				// Handle minimal player progress elements
				const minimalFilled = ProgressBar.querySelector( '.wppfy-minimal-progress-filled' );
				const minimalHandle = ProgressBar.querySelector( '.wppfy-minimal-progress-handle' );
				
				if ( dragger ) dragger.innerHTML = wppFormatTime( currentTime );
				if ( draggerEl ) draggerEl.style.left = `${completedPercentage}%`;
				if ( currentSlide ) currentSlide.style.width = `${completedPercentage}%`;
				
				// Update minimal player progress
				if ( minimalFilled ) minimalFilled.style.width = `${completedPercentage}%`;
				if ( minimalHandle ) minimalHandle.style.left = `${completedPercentage}%`;
			}
		} );

		// Initialize progress bar for this specific player
		const wpProgress = document.querySelector( `[data-pid="${uid}"] .wppfy-progress` ) ||
		                  document.querySelector( `[data-pid="${uid}"] .wppfy-minimal-progress` );
		if ( wpProgress ) {
			PodcastifyRangeSlider( wpProgress, {
				create: function ( value, target ) {
					// Initialize progress bar
				},
				start: function ( draggedPercentage, slider ) {
					slider.classList.add( 'dragging' );
				},
				drag: function ( draggedPercentage, slider ) {
					const WPP = wppGetPlayer( slider );
					const { currentTime, duration } = WPP;

					if ( slider.classList.contains( 'dragging' ) ) {
						const MoveTime = ( draggedPercentage / 100 ) * duration;
						const draggerSpan = slider.querySelector( '.dragger > span' );
						if ( draggerSpan ) draggerSpan.innerHTML = wppFormatTime( MoveTime );
					} else {
						const draggerSpan = slider.querySelector( '.dragger > span' );
						if ( draggerSpan ) draggerSpan.innerHTML = wppFormatTime( currentTime );
					}
				},
				stop: function ( draggedPercentage, slider ) {
					if( ! slider.classList.contains( 'dragging' ) ){
						return;
					}
					slider.classList.remove( 'dragging' );
					const WPP = wppGetPlayer( slider );
					const { duration } = WPP;
					const MoveTime = ( draggedPercentage / 100 ) * duration;
					const draggerSpan = slider.querySelector( '.dragger > span' );
					if ( draggerSpan ) draggerSpan.innerHTML = wppFormatTime( MoveTime );
					WPP.currentTime = MoveTime;
				}
			} );
		}

		// Initialize volume control for this specific player
		const PPVolumeControl = document.querySelector( `[data-pid="${uid}"] .wppfy-volume-control` );
		if ( PPVolumeControl ) {
			PodcastifyRangeSlider( PPVolumeControl, {
				create: function (  ) {
					// Initialize volume control
				},
				start: function ( draggedPercentage, slider ) {
					slider.classList.add( 'dragging' );
				},
				drag: function ( draggedPercentage, slider ) {
					if( ! slider.classList.contains( 'dragging' ) ){
						return;
					}
					const WPP = wppGetPlayer( slider );
					WPP.volume = 1 - draggedPercentage / 100;
				},
				stop: function ( draggedPercentage, slider ) {
					slider.classList.remove( 'dragging' );
				},
				vertical: true
			} );
		}
		// Initialize minimal player specific controls
		console.log('Podcastify: Checking player classes:', player.className);
		console.log('Podcastify: Has minimal class:', player.classList.contains('wppfy-minimal-player'));
		
		if (player.classList.contains('wppfy-minimal-player')) {
			console.log('Podcastify: Initializing minimal player controls for UID:', uid);
			initializeMinimalPlayerControls(uid, player);
		}
		
		} else {
			console.error( 'Podcastify: No audio URL provided for player', uid );
		}

	} );

	/**
	 * Play the player.
	 * 
	 * @since 1.0.0
	 */
	document.querySelectorAll( '.wppfy-play' ).forEach( ( el ) => {
		// Skip minimal player elements - they have their own handlers
		if ( el.closest('.wppfy-minimal-player') ) {
			return;
		}
		
		el.addEventListener( 'click', function () {
			const wpp = wppGetPlayer( this );
			this.style.display = 'none';
			this.nextElementSibling.style.display = 'block';
			wpp.play();
		} );
	} );

	/**
	 * Pause the player.
	 * 
	 * @since 1.0.0
	 */
	document.querySelectorAll( '.wppfy-pause' ).forEach( ( el ) => {
		// Skip minimal player elements - they have their own handlers
		if ( el.closest('.wppfy-minimal-player') ) {
			return;
		}
		
		el.addEventListener( 'click', function () {
			const wpp = wppGetPlayer( this );
			this.style.display = 'none';
			this.previousElementSibling.style.display = 'block';
			wpp.pause();
		} );
	} );

	/**
	 * Backward specific amount of time.
	 * 
	 * @since 1.0.0
	 */
	document.querySelectorAll( '.wppfy-backward' ).forEach( ( el ) => {

		el.addEventListener( 'click', function () {
			const wpp = wppGetPlayer( this );
			wpp.currentTime = wpp.currentTime - 15;
		} );

	} );

	/**
	 * Froward the specific amount of time.
	 * 
	 * @since 1.0.0
	 */
	document.querySelectorAll( '.wppfy-forward' ).forEach( ( el ) => {

		el.addEventListener( 'click', function () {
			const wpp = wppGetPlayer( this );
			wpp.currentTime = wpp.currentTime + 30;
		} );

	} );

	/**
	 * Volume mute functionally.
	 * 
	 * @since 1.0.0
	 */
	document.querySelectorAll( '.wppfy-volume-icon-wrap' ).forEach( ( el ) => {

		el.addEventListener( 'click', function( ) {
			const PPLayer = wppGetPlayer( this );
			if ( this.classList.contains( 'off' ) ) {
				this.classList.remove( 'off' );
				PPLayer.volume = 1;
			} else {
				this.classList.add( 'off' );
				PPLayer.volume = 0;
			}
		} );

	} );

	
}



if ( document.querySelector( '.wppfy-short-text' ) ) {

	/**
 * Show more text.
 */
	document.querySelector( '.wppfy-short-text .wppfy-text-more' ).addEventListener( 'click', ( e ) => {
		e.target.parentNode.classList.add( 'show-more' );
	} );

	/**
 * Hide more text.
 */
	document.querySelector( '.wppfy-text-less' ).addEventListener( 'click', ( e ) => {
		e.target.parentNode.parentNode.classList.remove( 'show-more' );
	} );
}


if ( document.querySelector( '.wppfy-download' ) ) {
	document.querySelectorAll( '.wppfy-download' ).forEach( ( el ) => {

		el.addEventListener( 'click' , ( e ) =>{
			//e.preventDefault();
			const episodeID = wppGetEpisodeID( e.target );

			const args = {
				beforeCall: () => {
					e.target.closest( '.wppfy-download' ).classList.add( 'loading' );
				},
				onSuccess: ( result ) => { 
					
					if( result.success ) {
						const { downloads } = result.data;
						document.querySelector( `.wppfy-player[data-episode-id='${episodeID}'] .wppfy-download-count` ).innerHTML = downloads;
					}
				},
				requestData : {
					action: 'podcastify_episode_download',
					episodeID
				}
			};
			wppSetMeta( 'download_episode', episodeID, args );
		} );
	} );
}

if ( document.querySelector( '.wppfy-like' ) ) {
	document.querySelectorAll( '.wppfy-like' ).forEach( ( el ) => {

		el.addEventListener( 'click' , ( e ) =>{
			//e.preventDefault();
			const episodeID = wppGetEpisodeID( e.target );

			const args = {
				beforeCall: () => {
					e.target.closest( '.wppfy-like' ).classList.add( 'loading' );
				},
				onSuccess: ( result ) => { 
					
					if( result.success ) {
						const { likes } = result.data;
						document.querySelector( `.wppfy-player[data-episode-id='${episodeID}'] .wppfy-like-count` ).innerHTML = likes;
					}
				},
				requestData : {
					action: 'podcastify_episode_like',
					episodeID
				}
			};
			wppSetMeta( 'episode_like', episodeID, args );
		} );
	} );
}

if ( document.querySelector( '.wppfy-share-links.social' ) ) {
	document.querySelectorAll( '.wppfy-share-links.social a' ).forEach( ( el ) => {
		el.addEventListener( 'click' , function ( e ){
			e.preventDefault();
			window.open( this.getAttribute( 'href' ), '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600' );
			return false;
		} );
	} );
}

/**
 * Initialize minimal player specific controls
 * @param {string} uid Player unique ID
 * @param {HTMLElement} playerElement Player element
 */
function initializeMinimalPlayerControls(uid, playerElement) {
	console.log('Podcastify: Initializing minimal player controls for UID:', uid);
	
	const audio = WPPFY[`Podcastify_Player_${uid}`];
	if (!audio) {
		console.error('Podcastify: Audio not found for minimal player');
		return;
	}

	// Play/Pause functionality
	const playButtons = playerElement.querySelectorAll('.wppfy-play');
	const pauseButtons = playerElement.querySelectorAll('.wppfy-pause');
	
	console.log('Podcastify: Found play buttons:', playButtons.length, 'pause buttons:', pauseButtons.length);
	
	playButtons.forEach(btn => {
		btn.addEventListener('click', function(e) {
			e.preventDefault();
			console.log('Podcastify: Minimal player play clicked');
			
			// Hide all play buttons in this player
			playButtons.forEach(p => p.style.display = 'none');
			// Show all pause buttons in this player
			pauseButtons.forEach(p => p.style.display = 'inline');
			
			audio.play().then(() => {
				console.log('Podcastify: Audio playing');
			}).catch(error => {
				console.error('Podcastify: Play error:', error);
				// Revert button states on error
				playButtons.forEach(p => p.style.display = 'inline');
				pauseButtons.forEach(p => p.style.display = 'none');
			});
		});
	});
	
	pauseButtons.forEach(btn => {
		btn.addEventListener('click', function(e) {
			e.preventDefault();
			console.log('Podcastify: Minimal player pause clicked');
			
			// Hide all pause buttons in this player
			pauseButtons.forEach(p => p.style.display = 'none');
			// Show all play buttons in this player
			playButtons.forEach(p => p.style.display = 'inline');
			
			audio.pause();
			console.log('Podcastify: Audio paused');
		});
	});

	// Listen to audio events to sync button states
	audio.addEventListener('play', () => {
		playButtons.forEach(p => p.style.display = 'none');
		pauseButtons.forEach(p => p.style.display = 'inline');
	});
	
	audio.addEventListener('pause', () => {
		pauseButtons.forEach(p => p.style.display = 'none');
		playButtons.forEach(p => p.style.display = 'inline');
	});
	
	audio.addEventListener('ended', () => {
		pauseButtons.forEach(p => p.style.display = 'none');
		playButtons.forEach(p => p.style.display = 'inline');
	});

	// Rewind/Forward controls
	const rewindBtn = playerElement.querySelector('.wppfy-minimal-backward');
	const forwardBtn = playerElement.querySelector('.wppfy-minimal-forward');
	
	if (rewindBtn) {
		rewindBtn.addEventListener('click', function(e) {
			e.preventDefault();
			console.log('Podcastify: Rewind clicked');
			audio.currentTime = Math.max(0, audio.currentTime - 15);
		});
	}
	
	if (forwardBtn) {
		forwardBtn.addEventListener('click', function(e) {
			e.preventDefault();
			console.log('Podcastify: Forward clicked');
			audio.currentTime = Math.min(audio.duration, audio.currentTime + 30);
		});
	}

	// Volume control
	const volumeBtn = playerElement.querySelector('.wppfy-minimal-volume-btn');
	const volumeSlider = playerElement.querySelector('.wppfy-minimal-volume-slider');
	
	if (volumeBtn && volumeSlider) {
		console.log('Podcastify: Setting up volume controls');
		
		volumeBtn.addEventListener('click', (e) => {
			e.stopPropagation();
			console.log('Podcastify: Volume button clicked');
			
			const isVisible = volumeSlider.classList.contains('show');
			console.log('Podcastify: Volume slider currently visible:', isVisible);
			
			volumeSlider.classList.toggle('show');
			
			console.log('Podcastify: Volume slider now visible:', volumeSlider.classList.contains('show'));
			
			// Close other dropdowns
			playerElement.querySelectorAll('.wppfy-minimal-speed-options, .wppfy-minimal-share-dropdown').forEach(dropdown => {
				dropdown.classList.remove('show');
			});
		});

		// Volume slider interaction
		const volumeTrack = playerElement.querySelector('.wppfy-minimal-volume-track');
		if (volumeTrack) {
			volumeTrack.addEventListener('click', (e) => {
				e.stopPropagation();
				const rect = volumeTrack.getBoundingClientRect();
				const clickY = e.clientY - rect.top;
				const percentage = 1 - (clickY / rect.height);
				const newVolume = Math.max(0, Math.min(1, percentage));
				
				audio.volume = newVolume;
				audio.muted = false;
				
				// Update volume display
				const volumeFill = playerElement.querySelector('.wppfy-minimal-volume-fill');
				const volumeHandle = playerElement.querySelector('.wppfy-minimal-volume-handle');
				if (volumeFill) volumeFill.style.height = `${newVolume * 100}%`;
				if (volumeHandle) volumeHandle.style.top = `${100 - (newVolume * 100)}%`;
				
				console.log('Podcastify: Volume set to:', newVolume);
			});
		}
	}

	// Speed control
	const speedBtn = playerElement.querySelector('.wppfy-minimal-speed-btn');
	const speedOptions = playerElement.querySelector('.wppfy-minimal-speed-options');
	const speedText = playerElement.querySelector('.wppfy-minimal-speed-text');
	
	if (speedBtn && speedOptions && speedText) {
		console.log('Podcastify: Setting up speed controls');
		
		speedBtn.addEventListener('click', (e) => {
			e.stopPropagation();
			console.log('Podcastify: Speed button clicked');
			
			const isVisible = speedOptions.classList.contains('show');
			console.log('Podcastify: Speed options currently visible:', isVisible);
			
			speedOptions.classList.toggle('show');
			
			console.log('Podcastify: Speed options now visible:', speedOptions.classList.contains('show'));
			
			// Close other dropdowns
			playerElement.querySelectorAll('.wppfy-minimal-volume-slider, .wppfy-minimal-share-dropdown').forEach(dropdown => {
				dropdown.classList.remove('show');
			});
		});

		// Speed option buttons
		speedOptions.querySelectorAll('button[data-speed]').forEach(btn => {
			btn.addEventListener('click', (e) => {
				e.preventDefault();
				e.stopPropagation();
				
				const speed = parseFloat(btn.dataset.speed);
				console.log('Podcastify: Setting speed to:', speed);
				
				audio.playbackRate = speed;
				speedText.textContent = `${speed}x`;
				
				// Update active state
				speedOptions.querySelectorAll('button').forEach(b => b.classList.remove('active'));
				btn.classList.add('active');
				
				speedOptions.classList.remove('show');
			});
		});
	}

	// Share control
	const shareBtn = playerElement.querySelector('.wppfy-minimal-share-toggle');
	const shareDropdown = playerElement.querySelector('.wppfy-minimal-share-dropdown');
	
	if (shareBtn && shareDropdown) {
		console.log('Podcastify: Setting up share controls');
		
		shareBtn.addEventListener('click', (e) => {
			e.stopPropagation();
			console.log('Podcastify: Share button clicked');
			shareDropdown.classList.toggle('show');
			
			// Close other dropdowns
			playerElement.querySelectorAll('.wppfy-minimal-volume-slider, .wppfy-minimal-speed-options').forEach(dropdown => {
				dropdown.classList.remove('show');
			});
		});
	}

	// Like button
	const likeBtn = playerElement.querySelector('.wppfy-minimal-like');
	if (likeBtn) {
		console.log('Podcastify: Setting up like button');
		
		likeBtn.addEventListener('click', (e) => {
			e.preventDefault();
			e.stopPropagation();
			console.log('Podcastify: Like button clicked');
			
			const episodeId = playerElement.dataset.episodeId;
			if (episodeId) {
				likeBtn.style.opacity = '0.6';
				
				const formData = new FormData();
				formData.append('action', 'podcastify_episode_like');
				formData.append('episodeID', episodeId);
				if (window.Podcastify && window.Podcastify.ajaxSecurity) {
					formData.append('security', window.Podcastify.ajaxSecurity);
				}

				const ajaxUrl = window.Podcastify && window.Podcastify.ajaxUrl ? window.Podcastify.ajaxUrl : '/wp-admin/admin-ajax.php';
				
				fetch(ajaxUrl, {
					method: 'POST',
					body: formData
				})
				.then(response => response.json())
				.then(result => {
					likeBtn.style.opacity = '1';
					console.log('Podcastify: Like response:', result);
					
					if (result.success && result.data && result.data.likes) {
						const likeCount = playerElement.querySelector('.wppfy-like-count');
						if (likeCount) {
							likeCount.textContent = result.data.likes;
						}
					}
				})
				.catch(error => {
					console.error('Podcastify: Like error:', error);
					likeBtn.style.opacity = '1';
				});
			}
		});
	}

	// Close dropdowns when clicking outside
	document.addEventListener('click', (e) => {
		if (!playerElement.contains(e.target)) {
			playerElement.querySelectorAll('.wppfy-minimal-volume-slider, .wppfy-minimal-speed-options, .wppfy-minimal-share-dropdown').forEach(dropdown => {
				dropdown.classList.remove('show');
			});
		}
	});

	console.log('Podcastify: Minimal player controls initialized successfully');
}

