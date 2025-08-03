/**
 * Podcastify Frontend JavaScript
 * Fixed Player Controls
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all audio players on the page
    const players = document.querySelectorAll('.wppfy-single-player');
    
    players.forEach(function(player, index) {
        initializePlayer(player, index);
    });
});

function initializePlayer(player, index) {
    const audioElement = player.querySelector('audio');
    const playBtn = player.querySelector('.wppfy-play');
    const pauseBtn = player.querySelector('.wppfy-pause');
    const stopBtn = player.querySelector('.wppfy-stop');
    const forwardBtn = player.querySelector('.wppfy-forward');
    const backwardBtn = player.querySelector('.wppfy-backward');
    const volumeSlider = player.querySelector('.wppfy-volume-control');
    const progressBar = player.querySelector('.wppfy-progress');
    const currentTimeDisplay = player.querySelector('.current-time');
    const totalTimeDisplay = player.querySelector('.total-time');
    
    if (!audioElement) {
        console.error('Audio element not found in player', index);
        return;
    }
    
    // Initialize progress bar
    if (progressBar) {
        const currentSlide = progressBar.querySelector('.current-slide');
        const dragger = progressBar.querySelector('.dragger');
        
        // Update progress bar as audio plays
        audioElement.addEventListener('timeupdate', function() {
            if (audioElement.duration) {
                const progress = (audioElement.currentTime / audioElement.duration) * 100;
                
                if (currentSlide) {
                    currentSlide.style.width = progress + '%';
                }
                
                if (dragger) {
                    dragger.style.left = progress + '%';
                }
                
                // Update time displays
                if (currentTimeDisplay) {
                    currentTimeDisplay.textContent = formatTime(audioElement.currentTime);
                }
            }
        });
        
        // Handle progress bar clicks
        progressBar.addEventListener('click', function(e) {
            if (audioElement.duration) {
                const rect = progressBar.getBoundingClientRect();
                const clickX = e.clientX - rect.left;
                const width = rect.width;
                const clickPercent = (clickX / width) * 100;
                const seekTime = (clickPercent / 100) * audioElement.duration;
                audioElement.currentTime = seekTime;
            }
        });
    }
    
    // Set total time when metadata loads
    audioElement.addEventListener('loadedmetadata', function() {
        if (totalTimeDisplay && audioElement.duration) {
            totalTimeDisplay.textContent = formatTime(audioElement.duration);
        }
    });
    
    // Play button
    if (playBtn) {
        playBtn.addEventListener('click', function(e) {
            e.preventDefault();
            audioElement.play();
            playBtn.style.display = 'none';
            if (pauseBtn) pauseBtn.style.display = 'inline-block';
        });
    }
    
    // Pause button
    if (pauseBtn) {
        pauseBtn.addEventListener('click', function(e) {
            e.preventDefault();
            audioElement.pause();
            pauseBtn.style.display = 'none';
            if (playBtn) playBtn.style.display = 'inline-block';
        });
    }
    
    // Stop button
    if (stopBtn) {
        stopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            audioElement.pause();
            audioElement.currentTime = 0;
            if (pauseBtn) pauseBtn.style.display = 'none';
            if (playBtn) playBtn.style.display = 'inline-block';
        });
    }
    
    // Forward button (skip 30 seconds)
    if (forwardBtn) {
        forwardBtn.addEventListener('click', function(e) {
            e.preventDefault();
            audioElement.currentTime = Math.min(audioElement.currentTime + 30, audioElement.duration || 0);
        });
    }
    
    // Backward button (skip back 15 seconds)
    if (backwardBtn) {
        backwardBtn.addEventListener('click', function(e) {
            e.preventDefault();
            audioElement.currentTime = Math.max(audioElement.currentTime - 15, 0);
        });
    }
    
    // Volume control (simplified - could be enhanced with a slider)
    const volumeUpBtn = player.querySelector('.wppfy-volume-up');
    const volumeOffBtn = player.querySelector('.wppfy-volume-off');
    
    if (volumeUpBtn) {
        volumeUpBtn.addEventListener('click', function(e) {
            e.preventDefault();
            audioElement.muted = false;
            audioElement.volume = 1;
            volumeUpBtn.style.display = 'none';
            if (volumeOffBtn) volumeOffBtn.style.display = 'inline-block';
        });
    }
    
    if (volumeOffBtn) {
        volumeOffBtn.addEventListener('click', function(e) {
            e.preventDefault();
            audioElement.muted = true;
            volumeOffBtn.style.display = 'none';
            if (volumeUpBtn) volumeUpBtn.style.display = 'inline-block';
        });
    }
    
    // Handle audio ended event
    audioElement.addEventListener('ended', function() {
        if (pauseBtn) pauseBtn.style.display = 'none';
        if (playBtn) playBtn.style.display = 'inline-block';
    });
}

// Format time in MM:SS format
function formatTime(seconds) {
    if (isNaN(seconds)) return '0:00';
    
    const minutes = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return minutes + ':' + (secs < 10 ? '0' : '') + secs;
}

// Handle AJAX requests if needed
if (typeof Podcastify !== 'undefined' && Podcastify.ajaxUrl) {
    // Add any AJAX functionality here if needed
}