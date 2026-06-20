// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const menuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    if (menuBtn && mobileMenu) {
        menuBtn.addEventListener('click', function() { mobileMenu.classList.toggle('hidden'); });
    }

    // Auto-hide flash messages
    document.querySelectorAll('[data-flash]').forEach(el => {
        setTimeout(() => { el.style.transition = 'opacity 0.5s'; el.style.opacity = '0'; setTimeout(() => el.remove(), 500); }, 4000);
    });

    // Modal handling
    document.querySelectorAll('[data-modal-toggle]').forEach(btn => {
        btn.addEventListener('click', function() {
            const target = document.getElementById(this.dataset.modalToggle);
            if (target) target.classList.remove('hidden');
        });
    });
    document.querySelectorAll('[data-modal-close]').forEach(btn => {
        btn.addEventListener('click', function() {
            const modal = this.closest('.modal-overlay');
            if (modal) modal.classList.add('hidden');
        });
    });
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) this.classList.add('hidden');
        });
    });

    // Tab switching (for tenant page)
    document.querySelectorAll('[data-tab]').forEach(btn => {
        btn.addEventListener('click', function() {
            const tab = this.dataset.tab;
            const params = new URLSearchParams(window.location.search);
            params.set('tab', tab);
            window.location.search = params.toString();
        });
    });
});
