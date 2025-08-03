/**
 * Minimal Player JavaScript
 * Dedicated JS for minimal player functionality
 * 
 * @package Podcastify
 */

(function() {
    'use strict';
    
    console.log('Podcastify: Minimal player JavaScript loaded');

    /**
     * Format time in MM:SS or HH:MM:SS format
     * @param {number} seconds 
     * @returns {string}
     */
    function formatTime(seconds) {
        if (isNaN(seconds)) return '0:00';
        
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = Math.floor(seconds % 60);
        
        if (hours > 0) {
            return `${hours}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }
        return `${minutes}:${secs.toString().padStart(2, '0')}`;
    }

    /**
     * Get audio player instance for a minimal player
     * @param {HTMLElement} playerElement 
     * @returns {HTMLAudioElement|null}
     */
    function getAudioPlayer(playerElement) {
        const uid = playerElement.dataset.pid;
        // Try multiple ways to get the audio player
        if (window.WPPFY && window.WPPFY[`Podcastify_Player_${uid}`]) {
            return window.WPPFY[`Podcastify_Player_${uid}`];
        }
        
        // Fallback: look for audio element in the player
        const audioEl = playerElement.querySelector('audio');
        return audioEl;
    }

    /**
     * Update progress bar and time display
     * @param {HTMLElement} playerElement 
     * @param {HTMLAudioElement} audio 
     */
    function updateProgress(playerElement, audio) {
        if (!audio || isNaN(audio.duration)) return;

        const currentTime = audio.currentTime;
        const duration = audio.duration;
        const percentage = (currentTime / duration) * 100;

        // Update time displays
        const currentTimeEl = playerElement.querySelector('.wppfy-minimal-time-current');
        const totalTimeEl = playerElement.querySelector('.wppfy-minimal-time-total');
        
        if (currentTimeEl) currentTimeEl.textContent = formatTime(currentTime);
        if (totalTimeEl) totalTimeEl.textContent = formatTime(duration);

        // Update progress bar
        const progressFill = playerElement.querySelector('.wppfy-minimal-progress-filled');
        const progressHandle = playerElement.querySelector('.wppfy-minimal-progress-handle');
        
        if (progressFill) progressFill.style.width = `${percentage}%`;
        if (progressHandle) progressHandle.style.left = `${percentage}%`;
    }

    /**
     * Update volume display
     * @param {HTMLElement} playerElement 
     * @param {HTMLAudioElement} audio 
     */
    function updateVolumeDisplay(playerElement, audio) {
        const volumeIcon = playerElement.querySelector('.wppfy-minimal-volume-btn i');
        const volumeFill = playerElement.querySelector('.wppfy-minimal-volume-fill');
        const volumeHandle = playerElement.querySelector('.wppfy-minimal-volume-handle');
        
        if (volumeIcon) {
            if (audio.muted || audio.volume === 0) {
                volumeIcon.className = 'fas fa-volume-mute';
            } else if (audio.volume < 0.5) {
                volumeIcon.className = 'fas fa-volume-down';
            } else {
                volumeIcon.className = 'fas fa-volume-up';
            }
        }

        if (volumeFill) {
            volumeFill.style.height = `${audio.volume * 100}%`;
        }
        
        if (volumeHandle) {
            volumeHandle.style.top = `${100 - (audio.volume * 100)}%`;
        }
    }

    /**
     * Initialize a single minimal player
     * @param {HTMLElement} playerElement 
     */
    function initializeMinimalPlayer(playerElement) {
        const audio = getAudioPlayer(playerElement);
        if (!audio) {
            console.error('Podcastify: Audio player not found for minimal player');
            return;
        }

        console.log('Podcastify: Initializing minimal player with audio:', audio);

        // Ensure audio has source
        if (!audio.src && !audio.querySelector('source')) {
            console.error('Podcastify: Audio has no source');
            return;
        }

        // Set up audio event listeners
        audio.addEventListener('loadedmetadata', () => {
            updateProgress(playerElement, audio);
        });

        audio.addEventListener('timeupdate', () => {
            updateProgress(playerElement, audio);
        });

        audio.addEventListener('volumechange', () => {
            updateVolumeDisplay(playerElement, audio);
        });

        // Play/Pause buttons
        const playButtons = playerElement.querySelectorAll('.wppfy-play');
        const pauseButtons = playerElement.querySelectorAll('.wppfy-pause');

        function updatePlayPauseButtons(isPlaying) {
            if (isPlaying) {
                playButtons.forEach(p => p.style.display = 'none');
                pauseButtons.forEach(p => p.style.display = 'inline');
            } else {
                pauseButtons.forEach(p => p.style.display = 'none');
                playButtons.forEach(p => p.style.display = 'inline');
            }
        }

        // Listen to audio events to sync button states
        audio.addEventListener('play', () => updatePlayPauseButtons(true));
        audio.addEventListener('pause', () => updatePlayPauseButtons(false));
        audio.addEventListener('ended', () => updatePlayPauseButtons(false));

        playButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                console.log('Podcastify: Play button clicked');
                
                if (audio.paused) {
                    audio.play().then(() => {
                        console.log('Podcastify: Audio started playing');
                    }).catch(error => {
                        console.error('Podcastify: Error playing audio:', error);
                    });
                }
            });
        });

        pauseButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                console.log('Podcastify: Pause button clicked');
                
                if (!audio.paused) {
                    audio.pause();
                    console.log('Podcastify: Audio paused');
                }
            });
        });

        // Rewind button
        const rewindBtn = playerElement.querySelector('.wppfy-minimal-backward');
        if (rewindBtn) {
            rewindBtn.addEventListener('click', () => {
                audio.currentTime = Math.max(0, audio.currentTime - 15);
            });
        }

        // Forward button
        const forwardBtn = playerElement.querySelector('.wppfy-minimal-forward');
        if (forwardBtn) {
            forwardBtn.addEventListener('click', () => {
                audio.currentTime = Math.min(audio.duration, audio.currentTime + 30);
            });
        }

        // Progress bar click
        const progressBar = playerElement.querySelector('.wppfy-minimal-progress');
        if (progressBar) {
            progressBar.addEventListener('click', (e) => {
                const rect = progressBar.getBoundingClientRect();
                const clickX = e.clientX - rect.left;
                const percentage = clickX / rect.width;
                const newTime = percentage * audio.duration;
                
                if (!isNaN(newTime)) {
                    audio.currentTime = newTime;
                }
            });
        }

        // Volume control
        const volumeBtn = playerElement.querySelector('.wppfy-minimal-volume-btn');
        const volumeSlider = playerElement.querySelector('.wppfy-minimal-volume-slider');
        
        if (volumeBtn && volumeSlider) {
            // Single click shows volume slider
            volumeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                console.log('Podcastify: Volume button clicked');
                
                // Toggle volume slider visibility
                volumeSlider.classList.toggle('show');
                
                // Close other dropdowns
                playerElement.querySelectorAll('.wppfy-minimal-speed-options, .wppfy-minimal-share-dropdown').forEach(dropdown => {
                    dropdown.classList.remove('show');
                });
            });

            // Double click to mute/unmute
            volumeBtn.addEventListener('dblclick', (e) => {
                e.stopPropagation();
                audio.muted = !audio.muted;
                updateVolumeDisplay(playerElement, audio);
                console.log('Podcastify: Audio muted:', audio.muted);
            });
        }

        // Volume slider interaction
        const volumeTrack = playerElement.querySelector('.wppfy-minimal-volume-track');
        const volumeHandle = playerElement.querySelector('.wppfy-minimal-volume-handle');
        
        if (volumeTrack) {
            let isDragging = false;
            
            // Click on track to set volume
            volumeTrack.addEventListener('click', (e) => {
                e.stopPropagation();
                const rect = volumeTrack.getBoundingClientRect();
                const clickY = e.clientY - rect.top;
                const percentage = 1 - (clickY / rect.height);
                const newVolume = Math.max(0, Math.min(1, percentage));
                
                audio.volume = newVolume;
                audio.muted = false; // Unmute when adjusting volume
                updateVolumeDisplay(playerElement, audio);
                
                console.log('Podcastify: Volume set to:', newVolume);
            });

            // Drag functionality for volume handle
            if (volumeHandle) {
                volumeHandle.addEventListener('mousedown', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    isDragging = true;
                    
                    const handleDrag = (e) => {
                        if (!isDragging) return;
                        
                        const rect = volumeTrack.getBoundingClientRect();
                        const clickY = e.clientY - rect.top;
                        const percentage = 1 - (clickY / rect.height);
                        const newVolume = Math.max(0, Math.min(1, percentage));
                        
                        audio.volume = newVolume;
                        audio.muted = false;
                        updateVolumeDisplay(playerElement, audio);
                    };
                    
                    const stopDrag = () => {
                        isDragging = false;
                        document.removeEventListener('mousemove', handleDrag);
                        document.removeEventListener('mouseup', stopDrag);
                    };
                    
                    document.addEventListener('mousemove', handleDrag);
                    document.addEventListener('mouseup', stopDrag);
                });
            }
        }

        // Speed control
        const speedBtn = playerElement.querySelector('.wppfy-minimal-speed-btn');
        const speedOptions = playerElement.querySelector('.wppfy-minimal-speed-options');
        const speedText = playerElement.querySelector('.wppfy-minimal-speed-text');
        
        if (speedBtn && speedOptions) {
            speedBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                console.log('Podcastify: Speed button clicked');
                speedOptions.classList.toggle('show');
                
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
                    
                    try {
                        audio.playbackRate = speed;
                        
                        if (speedText) {
                            speedText.textContent = `${speed}x`;
                        }
                        
                        // Update active state
                        speedOptions.querySelectorAll('button').forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                        
                        speedOptions.classList.remove('show');
                        
                        console.log('Podcastify: Playback rate set to:', audio.playbackRate);
                    } catch (error) {
                        console.error('Podcastify: Error setting playback rate:', error);
                    }
                });
            });
        }

        // Share control
        const shareBtn = playerElement.querySelector('.wppfy-minimal-share-toggle');
        const shareDropdown = playerElement.querySelector('.wppfy-minimal-share-dropdown');
        
        if (shareBtn && shareDropdown) {
            shareBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                console.log('Podcastify: Share button clicked');
                shareDropdown.classList.toggle('show');
                
                // Close other dropdowns
                playerElement.querySelectorAll('.wppfy-minimal-volume-slider, .wppfy-minimal-speed-options').forEach(dropdown => {
                    dropdown.classList.remove('show');
                });
            });

            // Share option links
            shareDropdown.querySelectorAll('.wppfy-minimal-share-option').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.stopPropagation();
                    // Let the default link behavior work, but close the dropdown
                    setTimeout(() => {
                        shareDropdown.classList.remove('show');
                    }, 100);
                });
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

        // Like button (if exists)
        const likeBtn = playerElement.querySelector('.wppfy-minimal-like');
        if (likeBtn) {
            likeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                console.log('Podcastify: Like button clicked');
                
                const episodeId = playerElement.dataset.episodeId;
                console.log('Podcastify: Episode ID:', episodeId);
                console.log('Podcastify: Window.Podcastify:', window.Podcastify);
                console.log('Podcastify: Ajax Security:', window.Podcastify ? window.Podcastify.ajaxSecurity : 'undefined');
                
                if (episodeId) {
                    // Add loading state
                    likeBtn.classList.add('loading');
                    likeBtn.style.opacity = '0.6';
                    
                    // Use WordPress AJAX
                    const formData = new FormData();
                    formData.append('action', 'podcastify_episode_like');
                    formData.append('episodeID', episodeId);
                    
                    // Add security nonce if available
                    const security = window.Podcastify && window.Podcastify.ajaxSecurity ? window.Podcastify.ajaxSecurity : '';
                    console.log('Podcastify: Using security nonce:', security);
                    if (security) {
                        formData.append('security', security);
                    }

                    fetch(window.Podcastify ? window.Podcastify.ajaxUrl : '/wp-admin/admin-ajax.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        console.log('Podcastify: Response status:', response.status);
                        return response.text();
                    })
                    .then(text => {
                        console.log('Podcastify: Raw response:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('Podcastify: Invalid JSON response:', text);
                            throw new Error('Invalid JSON response from server');
                        }
                    })
                    .then(result => {
                        likeBtn.classList.remove('loading');
                        likeBtn.style.opacity = '1';
                        
                        console.log('Podcastify: Like response:', result);
                        
                        if (result.success) {
                            const likeCount = playerElement.querySelector('.wppfy-like-count');
                            if (likeCount && result.data && result.data.likes) {
                                likeCount.textContent = result.data.likes;
                            }
                            // Add visual feedback
                            likeBtn.style.color = '#e74c3c';
                            setTimeout(() => {
                                likeBtn.style.color = '';
                            }, 300);
                        }
                    })
                    .catch(error => {
                        console.error('Podcastify: Like error:', error);
                        likeBtn.classList.remove('loading');
                        likeBtn.style.opacity = '1';
                    });
                } else {
                    console.error('Podcastify: No episode ID found for like');
                }
            });
        }

        // Initialize volume display
        updateVolumeDisplay(playerElement, audio);
        
        console.log('Podcastify: Minimal player initialized successfully');
    }

    /**
     * Initialize all minimal players on the page
     */
    function initializeMinimalPlayers() {
        const minimalPlayers = document.querySelectorAll('.wppfy-minimal-player');
        
        minimalPlayers.forEach(player => {
            // Check if already initialized
            if (player.hasAttribute('data-minimal-initialized')) {
                return;
            }
            
            // Wait for audio to be available
            let attempts = 0;
            const maxAttempts = 50; // 5 seconds total
            
            const checkAudio = () => {
                attempts++;
                const audio = getAudioPlayer(player);
                
                if (audio && (audio.src || audio.querySelector('source'))) {
                    console.log('Podcastify: Audio found, initializing minimal player');
                    initializeMinimalPlayer(player);
                    player.setAttribute('data-minimal-initialized', 'true');
                } else if (attempts < maxAttempts) {
                    // Retry after a short delay
                    setTimeout(checkAudio, 100);
                } else {
                    console.error('Podcastify: Failed to find audio after', maxAttempts, 'attempts');
                }
            };
            
            checkAudio();
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeMinimalPlayers);
    } else {
        initializeMinimalPlayers();
    }

    // Also initialize on dynamic content load
    if (window.MutationObserver) {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === 1) {
                        if (node.classList && node.classList.contains('wppfy-minimal-player')) {
                            initializeMinimalPlayers();
                        } else if (node.querySelector && node.querySelector('.wppfy-minimal-player')) {
                            initializeMinimalPlayers();
                        }
                    }
                });
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

})();