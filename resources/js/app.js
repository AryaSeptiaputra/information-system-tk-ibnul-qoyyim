import './bootstrap';

// Tab switcher for profil
function switchTab(tabId) {
    // Remove active class from all buttons and content
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    
    // Add active class to clicked button and corresponding content
    document.querySelector(`[data-tab="${tabId}"]`)?.classList.add('active');
    document.getElementById(tabId)?.classList.add('active');
}

// Attach event listeners to tab buttons
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const tabId = this.getAttribute('data-tab');
            switchTab(tabId);
        });
    });
});

// Mobile menu toggle
function toggleMenu() {
    const nav = document.querySelector('.nav-links');
    if (nav.style.display === 'flex') {
        nav.style.display = '';
    } else {
        nav.style.cssText = 'display:flex;flex-direction:column;position:fixed;top:70px;left:0;right:0;background:white;padding:20px 5%;border-bottom:3px solid var(--green-light);z-index:999;gap:4px;box-shadow:0 10px 30px rgba(0,0,0,0.1)';
    }
}

// Scroll reveal animation
const observer = new IntersectionObserver((entries) => {
    entries.forEach((e, i) => {
        if (e.isIntersecting) {
            setTimeout(() => e.target.classList.add('visible'), i * 80);
        }
    });
}, { threshold: 0.1 });

document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
