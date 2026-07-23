<script>
    const menuBtn = document.getElementById('menuBtn');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const closeBtn = document.getElementById('closeSidebarBtn');

    function openDrawer() {
        if (window.innerWidth < 1025) {
            sidebar.classList.add('drawer-active');
            overlay.classList.add('active');
            document.body.classList.add('noscroll');
            sidebar.style.pointerEvents = 'auto';
            closeBtn.style.display = 'block';
            menuBtn.style.display = 'none';
            
            // Set focus to close button for accessibility
            setTimeout(() => closeBtn.focus(), 100);
        }
    }

    function closeDrawer() {
        sidebar.classList.remove('drawer-active');
        overlay.classList.remove('active');
        document.body.classList.remove('noscroll');
        closeBtn.style.display = 'none';
        if (window.innerWidth < 1025) {
            menuBtn.style.display = '';
            // Return focus to menu button
            setTimeout(() => menuBtn.focus(), 100);
        }
    }

    // Event listeners
    menuBtn.addEventListener('click', openDrawer);
    overlay.addEventListener('click', closeDrawer);
    closeBtn.addEventListener('click', closeDrawer);

    // Close sidebar on mobile before following links so taps don't get stuck
    sidebar.addEventListener('click', (e) => {
        if (window.innerWidth >= 1025) {
            return;
        }

        const link = e.target.closest('a[href]');
        const submitButton = e.target.closest('button[type="submit"]');

        if (link) {
            e.preventDefault();
            const href = link.getAttribute('href');
            closeDrawer();
            window.setTimeout(() => {
                window.location.href = href;
            }, 30);
            return;
        }

        if (submitButton) {
            closeDrawer();
        }
    }, true);

    // Keyboard navigation - Escape key to close
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebar.classList.contains('drawer-active')) {
            closeDrawer();
        }
    });

    // Swipe to close (improved)
    let startX = 0;
    let currentX = 0;
    let isDragging = false;

    sidebar.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
        isDragging = true;
    });

    sidebar.addEventListener('touchmove', (e) => {
        if (!isDragging) return;
        currentX = e.touches[0].clientX;
    });

    sidebar.addEventListener('touchend', (e) => {
        if (!isDragging) return;
        isDragging = false;
        const diff = startX - currentX;
        // Swipe left to close (threshold: 80px)
        if (diff > 80) {
            closeDrawer();
        }
    });

    // Resize handler with debounce
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (window.innerWidth >= 1025) {
                sidebar.classList.remove('drawer-active');
                overlay.classList.remove('active');
                document.body.classList.remove('noscroll');
                closeBtn.style.display = 'none';
                menuBtn.style.display = 'none';
                sidebar.style.display = '';
            } else {
                if (!sidebar.classList.contains('drawer-active')) {
                    menuBtn.style.display = '';
                }
            }
        }, 100);
    });

    // Initial state
    if (window.innerWidth >= 1025) {
        menuBtn.style.display = 'none';
        closeBtn.style.display = 'none';
    } else {
        menuBtn.style.display = '';
        closeBtn.style.display = 'none';
    }
</script>
