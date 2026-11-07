/**
 * Cafe Coffea – Cups & Moments
 * Interactive Frontend JavaScript
 * Mobile-First, Zero-Dependency, Vercel-Ready
 */

document.addEventListener('DOMContentLoaded', () => {
    initHeaderScroll();
    initMobileNav();
    initGalleryFilters();
    initLightbox();
    initVideoPlayer();
    initSmoothScroll();
});

// 1. Header scroll blur and background state
function initHeaderScroll() {
    const header = document.querySelector('.site-header');
    if (!header) return;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 40) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    }, { passive: true });
}

// 2. Mobile Navigation Drawer & Hamburger
function initMobileNav() {
    const toggleBtn = document.querySelector('.mobile-nav-toggle');
    const drawer = document.querySelector('.mobile-drawer');
    const navLinks = document.querySelectorAll('.mobile-nav-link, .mobile-drawer .btn');

    if (!toggleBtn || !drawer) return;

    toggleBtn.addEventListener('click', () => {
        const isOpen = drawer.classList.contains('open');
        if (isOpen) {
            closeDrawer();
        } else {
            openDrawer();
        }
    });

    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            closeDrawer();
        });
    });

    function openDrawer() {
        drawer.classList.add('open');
        toggleBtn.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
        drawer.classList.remove('open');
        toggleBtn.classList.remove('open');
        document.body.style.overflow = '';
    }
}

// 3. Smooth Scrolling for Anchor Links
function initSmoothScroll() {
    const links = document.querySelectorAll('a[href^="#"]');
    links.forEach(link => {
        link.addEventListener('click', (e) => {
            const targetId = link.getAttribute('href');
            if (targetId === '#' || !targetId.startsWith('#')) return;

            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                const headerOffset = 70;
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
}

// 4. Photo Gallery Filtering & "View More Photos" Feature
function initGalleryFilters() {
    const filterButtons = document.querySelectorAll('.gallery-filter-btn');
    const cards = document.querySelectorAll('.gallery-card');
    const moreBtn = document.getElementById('gallery-more-btn');
    const moreWrap = document.getElementById('gallery-more-wrap');

    // Function to expand and lazily load all remaining photos
    function expandGallery() {
        const extraCards = document.querySelectorAll('.gallery-card-extra');
        extraCards.forEach(card => {
            const img = card.querySelector('img');
            if (img && img.getAttribute('data-src')) {
                img.src = img.getAttribute('data-src');
                img.removeAttribute('data-src');
            }
            card.style.display = 'inline-block';
            card.style.animation = 'fadeIn 0.35s ease forwards';
        });
        if (moreWrap) {
            moreWrap.style.display = 'none';
        }
    }

    if (moreBtn) {
        moreBtn.addEventListener('click', expandGallery);
    }

    if (filterButtons.length && cards.length) {
        filterButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                // Clicking a filter automatically expands full collection
                expandGallery();

                filterButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const filter = btn.getAttribute('data-filter');

                cards.forEach(card => {
                    const category = card.getAttribute('data-category');
                    if (filter === 'all' || category === filter) {
                        card.style.display = 'inline-block';
                        card.style.animation = 'fadeIn 0.35s ease forwards';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    }
}

// 5. Full-Screen Photo Lightbox with Mobile Swipe Navigation
function initLightbox() {
    const lightbox = document.getElementById('gallery-lightbox');
    if (!lightbox) return;

    const cards = Array.from(document.querySelectorAll('.gallery-card'));
    const imgEl = lightbox.querySelector('.lightbox-img');
    const closeBtn = lightbox.querySelector('.lightbox-close');
    const prevBtn = lightbox.querySelector('.lightbox-prev');
    const nextBtn = lightbox.querySelector('.lightbox-next');
    const counterEl = lightbox.querySelector('.lightbox-counter');

    let currentIndex = 0;
    let visibleCards = [];

    function updateVisibleCards() {
        visibleCards = cards.filter(c => c.style.display !== 'none');
    }

    function showImage(index) {
        updateVisibleCards();
        if (visibleCards.length === 0) return;

        if (index < 0) index = visibleCards.length - 1;
        if (index >= visibleCards.length) index = 0;

        currentIndex = index;
        const currentCard = visibleCards[currentIndex];
        const fullSrc = currentCard.getAttribute('data-full');

        // Smooth image switch
        imgEl.style.opacity = '0.3';
        imgEl.src = fullSrc;
        imgEl.onload = () => {
            imgEl.style.opacity = '1';
        };

        if (counterEl) {
            counterEl.textContent = `${currentIndex + 1} / ${visibleCards.length}`;
        }
    }

    cards.forEach(card => {
        card.addEventListener('click', () => {
            updateVisibleCards();
            const index = visibleCards.indexOf(card);
            showImage(index >= 0 ? index : 0);
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    });

    function closeLightbox() {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (closeBtn) closeBtn.addEventListener('click', closeLightbox);
    if (prevBtn) prevBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        showImage(currentIndex - 1);
    });
    if (nextBtn) nextBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        showImage(currentIndex + 1);
    });

    lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox || e.target.classList.contains('lightbox-content-wrap')) {
            closeLightbox();
        }
    });

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (!lightbox.classList.contains('active')) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') showImage(currentIndex - 1);
        if (e.key === 'ArrowRight') showImage(currentIndex + 1);
    });

    // Mobile touch swipe support (swipe-left for next, swipe-right for prev, swipe-down to close)
    let touchStartX = 0;
    let touchStartY = 0;
    let touchEndX = 0;
    let touchEndY = 0;

    lightbox.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].clientX;
        touchStartY = e.changedTouches[0].clientY;
    }, { passive: true });

    lightbox.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].clientX;
        touchEndY = e.changedTouches[0].clientY;
        handleSwipe();
    }, { passive: true });

    function handleSwipe() {
        const diffX = touchEndX - touchStartX;
        const diffY = touchEndY - touchStartY;
        const threshold = 40;

        // Check if horizontal swipe is dominant
        if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > threshold) {
            if (diffX < 0) {
                // Swipe Left -> Next Image
                showImage(currentIndex + 1);
            } else {
                // Swipe Right -> Previous Image
                showImage(currentIndex - 1);
            }
        } else if (diffY > 80 && Math.abs(diffX) < 60) {
            // Swipe Down -> Close
            closeLightbox();
        }
    }
}

// 6. Video Gallery Playback & Dedicated Theater Modal with Accessible Controls
function initVideoPlayer() {
    const videoCards = document.querySelectorAll('.video-card');
    const modal = document.getElementById('video-theater-modal');
    const modalVideo = document.getElementById('video-theater-element');
    const modalTitle = document.getElementById('video-theater-title');
    const closeBtn = document.getElementById('video-theater-close');
    const backdrop = document.getElementById('video-theater-backdrop');
    const playPauseBtn = document.getElementById('vt-play-pause-btn');
    const progressContainer = document.getElementById('vt-progress-container');
    const progressBar = document.getElementById('vt-progress-bar');
    const timeDisplay = document.getElementById('vt-time-display');
    const muteBtn = document.getElementById('vt-mute-btn');
    const fsBtn = document.getElementById('vt-fullscreen-btn');
    const mediaWrap = document.querySelector('.video-theater-media-wrap');
    const centerPlayToggle = document.getElementById('video-theater-play-toggle');

    function formatTime(seconds) {
        if (isNaN(seconds) || seconds < 0) return '0:00';
        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
    }

    function openTheater(videoSrc, title) {
        if (!modal || !modalVideo) return;

        // Pause any inline videos currently playing
        document.querySelectorAll('video').forEach(v => {
            if (!v.paused) v.pause();
        });

        if (modalTitle) modalTitle.textContent = title || 'Party Hall Video Tour';
        modalVideo.src = videoSrc;
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Reset progress & icons
        if (progressBar) progressBar.style.width = '0%';
        if (timeDisplay) timeDisplay.textContent = '0:00 / 0:00';
        if (playPauseBtn) playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';

        modalVideo.play().then(() => {
            if (playPauseBtn) playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
        }).catch(() => {
            // Autoplay with sound may be blocked by browser; user can tap play
            if (playPauseBtn) playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
        });
    }

    function closeTheater() {
        if (!modal || !modalVideo) return;
        modalVideo.pause();
        modalVideo.currentTime = 0;
        modalVideo.src = '';
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    // Connect Video Cards to Theater Modal
    videoCards.forEach(card => {
        const videoSrc = card.getAttribute('data-video');
        const title = card.getAttribute('data-title');
        const overlay = card.querySelector('.video-play-overlay');
        const watchBtn = card.querySelector('.video-fs-btn');
        const inlineVideo = card.querySelector('.video-element');

        if (overlay) {
            overlay.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                openTheater(videoSrc, title);
            });
        }

        if (watchBtn) {
            watchBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                openTheater(videoSrc, title);
            });
        }

        if (inlineVideo) {
            inlineVideo.addEventListener('play', () => {
                // Pause other videos
                document.querySelectorAll('.video-element').forEach(v => {
                    if (v !== inlineVideo && !v.paused) v.pause();
                });
            });
        }
    });

    // Modal Close Button (Prominently visible at top-right, always accessible)
    if (closeBtn) {
        closeBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            closeTheater();
        });
    }

    if (backdrop) {
        backdrop.addEventListener('click', closeTheater);
    }

    // Keyboard ESC to close
    document.addEventListener('keydown', (e) => {
        if (modal && modal.classList.contains('active') && e.key === 'Escape') {
            closeTheater();
        }
    });

    if (modalVideo) {
        // Toggle play/pause on video click
        if (mediaWrap) {
            mediaWrap.addEventListener('click', (e) => {
                if (e.target.closest('.video-theater-controls')) return;
                togglePlayPause();
            });
        }

        function togglePlayPause() {
            if (modalVideo.paused) {
                modalVideo.play().catch(() => {});
                showCenterToggle('play');
            } else {
                modalVideo.pause();
                showCenterToggle('pause');
            }
        }

        function showCenterToggle(state) {
            if (!centerPlayToggle) return;
            centerPlayToggle.innerHTML = state === 'play' ? '<i class="fas fa-play"></i>' : '<i class="fas fa-pause"></i>';
            centerPlayToggle.classList.add('show');
            setTimeout(() => {
                centerPlayToggle.classList.remove('show');
            }, 500);
        }

        if (playPauseBtn) {
            playPauseBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                togglePlayPause();
            });
        }

        modalVideo.addEventListener('play', () => {
            if (playPauseBtn) playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
        });

        modalVideo.addEventListener('pause', () => {
            if (playPauseBtn) playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
        });

        modalVideo.addEventListener('ended', () => {
            if (playPauseBtn) playPauseBtn.innerHTML = '<i class="fas fa-redo"></i>';
        });

        // Update progress bar & time
        modalVideo.addEventListener('timeupdate', () => {
            if (!modalVideo.duration) return;
            const current = modalVideo.currentTime;
            const total = modalVideo.duration;
            const pct = (current / total) * 100;

            if (progressBar) progressBar.style.width = pct + '%';
            if (timeDisplay) timeDisplay.textContent = `${formatTime(current)} / ${formatTime(total)}`;
        });

        // Interactive Progress Scrubber (Click & Touch)
        function seekVideo(e) {
            if (!modalVideo.duration || !progressContainer) return;
            const rect = progressContainer.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const offsetX = Math.max(0, Math.min(clientX - rect.left, rect.width));
            const pct = offsetX / rect.width;
            modalVideo.currentTime = pct * modalVideo.duration;
        }

        if (progressContainer) {
            progressContainer.addEventListener('click', (e) => {
                e.stopPropagation();
                seekVideo(e);
            });

            progressContainer.addEventListener('touchmove', (e) => {
                seekVideo(e);
            }, { passive: true });
        }

        // Volume / Mute Button
        if (muteBtn) {
            muteBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                modalVideo.muted = !modalVideo.muted;
                muteBtn.innerHTML = modalVideo.muted ? '<i class="fas fa-volume-mute"></i>' : '<i class="fas fa-volume-up"></i>';
            });
        }

        // Fullscreen Toggle
        if (fsBtn) {
            fsBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (!document.fullscreenElement) {
                    if (modal.requestFullscreen) {
                        modal.requestFullscreen().catch(() => {});
                    } else if (modalVideo.webkitEnterFullscreen) {
                        // iOS Safari
                        modalVideo.webkitEnterFullscreen();
                    }
                    fsBtn.innerHTML = '<i class="fas fa-compress"></i>';
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen().catch(() => {});
                    }
                    fsBtn.innerHTML = '<i class="fas fa-expand"></i>';
                }
            });
        }
    }
}
