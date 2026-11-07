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
    initPricingCalculator();
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

// 4. Photo Gallery Filtering (Clean display without promotional text between images)
function initGalleryFilters() {
    const filterButtons = document.querySelectorAll('.gallery-filter-btn');
    const cards = document.querySelectorAll('.gallery-card');

    if (!filterButtons.length || !cards.length) return;

    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            filterButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const filter = btn.getAttribute('data-filter');

            cards.forEach(card => {
                const category = card.getAttribute('data-category');
                if (filter === 'all' || category === filter) {
                    card.style.display = 'block';
                    card.style.animation = 'fadeIn 0.35s ease forwards';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
}

// 5. Full-Screen Photo Lightbox
function initLightbox() {
    const lightbox = document.getElementById('gallery-lightbox');
    if (!lightbox) return;

    const cards = Array.from(document.querySelectorAll('.gallery-card'));
    const imgEl = lightbox.querySelector('.lightbox-img');
    const closeBtn = lightbox.querySelector('.lightbox-close');
    const prevBtn = lightbox.querySelector('.lightbox-prev');
    const nextBtn = lightbox.querySelector('.lightbox-next');

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

        imgEl.src = fullSrc;
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
    if (prevBtn) prevBtn.addEventListener('click', () => showImage(currentIndex - 1));
    if (nextBtn) nextBtn.addEventListener('click', () => showImage(currentIndex + 1));

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

    // Mobile touch swipe support
    let touchStartX = 0;
    let touchEndX = 0;

    lightbox.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    lightbox.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, { passive: true });

    function handleSwipe() {
        const threshold = 50;
        if (touchEndX < touchStartX - threshold) {
            showImage(currentIndex + 1);
        }
        if (touchEndX > touchStartX + threshold) {
            showImage(currentIndex - 1);
        }
    }
}

// 6. Interactive Party Hall Booking Calculator
// Rule: ₹1,000 per hour + ₹200 one-time cleaning fee per booking (applied only once!)
function initPricingCalculator() {
    const hourButtons = document.querySelectorAll('.calc-hour-btn');
    const totalAmountEl = document.getElementById('calc-total-amount');
    const totalSubEl = document.getElementById('calc-total-sub');
    const whatsappBtn = document.getElementById('calc-whatsapp-btn');

    if (!hourButtons.length || !totalAmountEl) return;

    const hourlyRate = 1000;
    const cleaningFee = 200; // Applied ONLY once per booking

    hourButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            hourButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const hours = parseInt(btn.getAttribute('data-hours'), 10) || 1;
            const hallRental = hours * hourlyRate;
            const total = hallRental + cleaningFee;

            totalAmountEl.textContent = '₹' + total.toLocaleString('en-IN');
            totalSubEl.textContent = `${hours} Hour${hours > 1 ? 's' : ''} (₹${hallRental.toLocaleString('en-IN')}) + ₹200 one-time cleaning`;

            // Update WhatsApp pre-filled text with selected hours and calculated price
            if (whatsappBtn) {
                const phone = '918157022220';
                const msg = `Hello Cafe Coffea! I would like to book the Party Hall for ${hours} Hour${hours > 1 ? 's' : ''} (Total: ₹${total.toLocaleString('en-IN')}).%0A• Date: %0A• Time Slot: %0A• Guest Count: %0A• Occasion: %0A• Cake Pre-booking: `;
                whatsappBtn.href = `https://wa.me/${phone}?text=${encodeURIComponent(msg)}`;
            }
        });
    });
}

// 7. Video Gallery playback control
function initVideoPlayer() {
    const videoCards = document.querySelectorAll('.video-card');
    videoCards.forEach(card => {
        const video = card.querySelector('video');
        if (!video) return;

        video.addEventListener('play', () => {
            document.querySelectorAll('video').forEach(v => {
                if (v !== video && !v.paused) {
                    v.pause();
                }
            });
        });
    });
}
