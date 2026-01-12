/**
 * ===================================================================
 * MAIN.JS - JavaScript principal 2betshop
 * ===================================================================
 */

// Esperar a que el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('2betshop - JavaScript cargado correctamente');
    
    // Inicializar funciones
    initMobileMenu();
    initScrollAnimations();
    initPromoBar();
});

/**
 * Mobile Menu Toggle
 */
function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    const backdrop = document.getElementById('mobileMenuBackdrop');
    
    if (menu && backdrop) {
        menu.classList.toggle('active');
        backdrop.classList.toggle('active');
        
        // Prevenir scroll del body cuando el menú está abierto
        if (menu.classList.contains('active')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }
}

/**
 * Inicializar Mobile Menu
 */
function initMobileMenu() {
    // Cerrar menú al hacer clic en un enlace
    const menuLinks = document.querySelectorAll('.mobile-menu-nav a');
    menuLinks.forEach(link => {
        link.addEventListener('click', () => {
            toggleMobileMenu();
        });
    });
    
    // Cerrar menú con tecla Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const menu = document.getElementById('mobileMenu');
            if (menu && menu.classList.contains('active')) {
                toggleMobileMenu();
            }
        }
    });
}

/**
 * Animaciones de scroll
 */
function initScrollAnimations() {
    // Intersection Observer para animaciones
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);
    
    // Observar elementos animables
    const animateElements = document.querySelectorAll('.section-header, .feature-item, .category-card, .product-card');
    animateElements.forEach(el => observer.observe(el));
}

/**
 * Inicializar Promo Bar
 */
function initPromoBar() {
    // El countdown ya está inicializado en promo-bar.php
    // Aquí podemos agregar funcionalidad adicional si es necesaria
}

/**
 * Smooth scroll para enlaces internos
 */
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

/**
 * Funciones globales (para compatibilidad)
 */
window.toggleMobileMenu = toggleMobileMenu;