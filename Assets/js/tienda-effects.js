/**
 * Efectos visuales y animaciones para la tienda
 * Mejoras de UX e interactividad
 */

$(document).ready(function() {
    // Inicializar efectos visuales
    initializeVisualEffects();
    
    // Lazy loading de imágenes
    initializeLazyLoading();
    
    // Efectos de hover mejorados
    initializeHoverEffects();
    
    // Smooth scrolling
    initializeSmoothScrolling();
    
    // Intersection Observer para animaciones
    initializeScrollAnimations();
});

/**
 * Inicializar efectos visuales generales
 */
function initializeVisualEffects() {
    // Efecto de aparición gradual para las tarjetas de productos
    $('.farmacity-product-card').each(function(index) {
        $(this).css({
            'opacity': '0',
            'transform': 'translateY(30px)'
        }).delay(index * 100).animate({
            'opacity': '1'
        }, 600).css('transform', 'translateY(0)');
    });
    
    // Efecto de typing para el título
    typeWriterEffect('.hero-title', $('.hero-title').text(), 100);
    
    // Contador animado para resultados
    animateCounter('.results-count');
}

/**
 * Efecto de escritura tipo máquina de escribir
 */
function typeWriterEffect(selector, text, speed) {
    const element = $(selector);
    if (element.length === 0) return;
    
    element.text('');
    let i = 0;
    
    function typeWriter() {
        if (i < text.length) {
            element.text(element.text() + text.charAt(i));
            i++;
            setTimeout(typeWriter, speed);
        }
    }
    
    typeWriter();
}

/**
 * Animación de contador
 */
function animateCounter(selector) {
    $(selector).each(function() {
        const $this = $(this);
        const text = $this.text();
        const match = text.match(/(\d+)/);
        
        if (match) {
            const targetNumber = parseInt(match[1]);
            const prefix = text.substring(0, match.index);
            const suffix = text.substring(match.index + match[1].length);
            
            $({ countNum: 0 }).animate({
                countNum: targetNumber
            }, {
                duration: 2000,
                easing: 'swing',
                step: function() {
                    $this.text(prefix + Math.floor(this.countNum) + suffix);
                },
                complete: function() {
                    $this.text(prefix + targetNumber + suffix);
                }
            });
        }
    });
}

/**
 * Lazy loading de imágenes
 */
function initializeLazyLoading() {
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const src = img.getAttribute('data-src');
                    
                    if (src) {
                        img.setAttribute('src', src);
                        img.removeAttribute('data-src');
                        img.classList.add('loaded');
                    }
                    
                    observer.unobserve(img);
                }
            });
        });
        
        // Observar todas las imágenes con data-src
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
}

/**
 * Efectos de hover mejorados
 */
function initializeHoverEffects() {
    // Efecto de paralaje sutil en las tarjetas
    $('.farmacity-product-card').on('mousemove', function(e) {
        const card = $(this);
        const rect = this.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        
        const rotateX = (y - centerY) / 10;
        const rotateY = (centerX - x) / 10;
        
        card.css({
            'transform': `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateZ(10px)`
        });
    });
    
    $('.farmacity-product-card').on('mouseleave', function() {
        $(this).css({
            'transform': 'perspective(1000px) rotateX(0) rotateY(0) translateZ(0)',
            'transition': 'transform 0.5s ease'
        });
    });
    
    // Efecto de onda en botones
    $('.product-add-button, .load-more-btn').on('click', function(e) {
        const button = $(this);
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        const ripple = $('<span class="ripple"></span>').css({
            width: size,
            height: size,
            left: x,
            top: y
        });
        
        button.append(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    });
}

/**
 * Smooth scrolling mejorado
 */
function initializeSmoothScrolling() {
    // Smooth scroll para anclas
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        
        const target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 800, 'easeInOutQuart');
        }
    });
    
    // Scroll suave al aplicar filtros
    const originalFilterProducts = window.filterProducts;
    window.filterProducts = function() {
        if (originalFilterProducts) {
            originalFilterProducts();
            
            // Scroll suave a los productos después del filtrado
            setTimeout(() => {
                const productsSection = $('.products-section');
                if (productsSection.length) {
                    $('html, body').animate({
                        scrollTop: productsSection.offset().top - 120
                    }, 500);
                }
            }, 300);
        }
    };
}

/**
 * Animaciones basadas en scroll
 */
function initializeScrollAnimations() {
    if ('IntersectionObserver' in window) {
        const animationObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        // Observar elementos para animación
        document.querySelectorAll('.farmacity-product-card, .filters-sidebar, .products-header').forEach(el => {
            animationObserver.observe(el);
        });
    }
}

/**
 * Efectos de partículas en el hero
 */
function createHeroParticles() {
    const hero = $('.hero-section');
    if (hero.length === 0) return;
    
    for (let i = 0; i < 20; i++) {
        const particle = $('<div class="particle"></div>').css({
            position: 'absolute',
            width: Math.random() * 4 + 2 + 'px',
            height: Math.random() * 4 + 2 + 'px',
            background: 'rgba(255, 255, 255, 0.3)',
            borderRadius: '50%',
            left: Math.random() * 100 + '%',
            top: Math.random() * 100 + '%',
            animation: `float ${Math.random() * 6 + 4}s ease-in-out infinite`
        });
        
        hero.append(particle);
    }
}

/**
 * Efecto de búsqueda en tiempo real
 */
function enhanceSearch() {
    let searchTimeout;
    const searchInput = $('.search-input');
    const suggestionsContainer = $('<div class="search-suggestions"></div>');
    
    searchInput.parent().append(suggestionsContainer);
    
    searchInput.on('input', function() {
        const query = $(this).val().trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length > 2) {
            searchTimeout = setTimeout(() => {
                // Aquí se implementaría la búsqueda AJAX
                showSearchSuggestions(query);
            }, 300);
        } else {
            suggestionsContainer.hide();
        }
    });
    
    // Cerrar sugerencias al hacer clic fuera
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.search-container').length) {
            suggestionsContainer.hide();
        }
    });
}

/**
 * Mostrar sugerencias de búsqueda
 */
function showSearchSuggestions(query) {
    // Ejemplo de sugerencias (se conectaría con el backend)
    const suggestions = [
        'Cables de red',
        'Conectores RJ45',
        'Switch ethernet',
        'Router wifi',
        'Adaptadores'
    ].filter(item => item.toLowerCase().includes(query.toLowerCase()));
    
    const suggestionsContainer = $('.search-suggestions');
    suggestionsContainer.empty();
    
    suggestions.forEach(suggestion => {
        const item = $(`<div class="search-suggestion">${suggestion}</div>`);
        item.on('click', function() {
            $('.search-input').val(suggestion);
            suggestionsContainer.hide();
            // Trigger search
            filterProducts();
        });
        suggestionsContainer.append(item);
    });
    
    if (suggestions.length > 0) {
        suggestionsContainer.show();
    }
}

/**
 * Efecto de carga mejorado
 */
function showLoadingEffect() {
    const productGrid = $('.product-grid');
    productGrid.addClass('loading');
    
    // Crear skeleton loaders
    const skeletonHTML = `
        <div class="product-loading skeleton">
            <div class="skeleton skeleton-image"></div>
            <div class="skeleton skeleton-text skeleton-title"></div>
            <div class="skeleton skeleton-text skeleton-price"></div>
        </div>
    `;
    
    productGrid.html(Array(8).fill(skeletonHTML).join(''));
    
    setTimeout(() => {
        productGrid.removeClass('loading');
        // Aquí se cargarían los productos reales
    }, 1500);
}

/**
 * Configurar intersection observer personalizado
 */
function setupCustomObserver() {
    const observerOptions = {
        threshold: [0, 0.25, 0.5, 0.75, 1],
        rootMargin: '-50px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const element = entry.target;
            const ratio = entry.intersectionRatio;
            
            // Efectos basados en el ratio de visibilidad
            if (ratio > 0.5) {
                element.classList.add('fully-visible');
            } else {
                element.classList.remove('fully-visible');
            }
        });
    }, observerOptions);
    
    // Observar tarjetas de productos
    document.querySelectorAll('.farmacity-product-card').forEach(card => {
        observer.observe(card);
    });
}

/**
 * Inicializar todos los efectos cuando el DOM esté listo
 */
$(document).ready(function() {
    // Crear partículas en el hero
    createHeroParticles();
    
    // Mejorar la búsqueda
    enhanceSearch();
    
    // Configurar observer personalizado
    if ('IntersectionObserver' in window) {
        setupCustomObserver();
    }
    
    // Agregar clases para animaciones CSS
    setTimeout(() => {
        $('.hero-section').addClass('loaded');
        $('.main-content').addClass('loaded');
    }, 500);
});

// Agregar easing personalizado para jQuery
$.easing.easeInOutQuart = function (x, t, b, c, d) {
    if ((t/=d/2) < 1) return c/2*t*t*t*t + b;
    return -c/2 * ((t-=2)*t*t*t - 2) + b;
};