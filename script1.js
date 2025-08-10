document.addEventListener('DOMContentLoaded', function() {
    AOS.init({
        duration: 1000,
        easing: 'ease-in-out',
        once: true,
        mirror: false
    });
    initSmoothScrolling();

    createScrollIndicator();

    handleNavbarScroll();

    initParallaxEffects();

    initButtonAnimations();

    initFloatingAnimations();

    hideLoadingAnimation();
    initHoverEffects();
    initTypingAnimation();
    
    initCounterAnimations();
});

function initSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                const headerOffset = 80;
                const elementPosition = target.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
}

function scrollToSection(sectionId) {
    const target = document.getElementById(sectionId);
    if (target) {
        const headerOffset = 80;
        const elementPosition = target.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    }
}

function createScrollIndicator() {
    const scrollIndicator = document.createElement('div');
    scrollIndicator.className = 'scroll-indicator';
    scrollIndicator.innerHTML = '<div class="scroll-progress"></div>';
    document.body.appendChild(scrollIndicator);

    const scrollProgress = scrollIndicator.querySelector('.scroll-progress');

    window.addEventListener('scroll', () => {
        const scrollTop = window.pageYOffset;
        const docHeight = document.body.scrollHeight - window.innerHeight;
        const scrollPercent = (scrollTop / docHeight) * 100;
        scrollProgress.style.width = scrollPercent + '%';
    });
}

function handleNavbarScroll() {
    const navbar = document.querySelector('.navbar');
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 100) {
            navbar.style.background = 'rgba(26, 13, 61, 0.98)';
            navbar.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.1)';
        } else {
            navbar.style.background = 'rgba(26, 13, 61, 0.95)';
            navbar.style.boxShadow = 'none';
        }
    });
}

function initParallaxEffects() {
    const parallaxElements = document.querySelectorAll('.parallax-element');
    
    window.addEventListener('scroll', () => {
        const scrollTop = window.pageYOffset;
        
        parallaxElements.forEach((element, index) => {
            const speed = 0.1 + (index * 0.05);
            const yPos = -(scrollTop * speed);
            element.style.transform = `translate3d(0, ${yPos}px, 0)`;
        });
    });


    const floatingIcons = document.querySelectorAll('.icon');
    window.addEventListener('scroll', () => {
        const scrollTop = window.pageYOffset;
        
        floatingIcons.forEach((icon, index) => {
            const speed = 0.05 + (index * 0.02);
            const yPos = scrollTop * speed;
            icon.style.transform = `translateY(${yPos}px) rotate(${yPos * 0.1}deg)`;
        });
    });
}

function initButtonAnimations() {
    const buttons = document.querySelectorAll('.btn');
    
    buttons.forEach(button => {
        button.classList.add('btn-hover-effect');
        
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.6);
                transform: scale(0);
                animation: ripple 0.6s linear;
                left: ${x}px;
                top: ${y}px;
                width: ${size}px;
                height: ${size}px;
                pointer-events: none;
            `;
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    if (!document.querySelector('#ripple-style')) {
        const style = document.createElement('style');
        style.id = 'ripple-style';
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
}

function initFloatingAnimations() {
    const floatingElements = document.querySelectorAll('.icon, .shape');
    
    floatingElements.forEach((element, index) => {
        const floatHeight = 20 + (index * 5);
        const duration = 3 + (index * 0.5);
        
        element.style.animation = `float ${duration}s ease-in-out infinite`;
        element.style.animationDelay = `${index * 0.5}s`;
    });

    floatingElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            this.style.animationPlayState = 'paused';
            this.style.transform = 'scale(1.1) translateY(-10px)';
        });
        
        element.addEventListener('mouseleave', function() {
            this.style.animationPlayState = 'running';
            this.style.transform = '';
        });
    });
}

function hideLoadingAnimation() {
    const loadingSpinner = document.querySelector('.loading-spinner');
    if (loadingSpinner) {
        setTimeout(() => {
            loadingSpinner.style.opacity = '0';
            setTimeout(() => {
                loadingSpinner.remove();
            }, 500);
        }, 1000);
    }
}

function initHoverEffects() {
    const cards = document.querySelectorAll('.product-card, .bundle-card, .accessory-card, .faq-item');
    
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
            this.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.3)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = '';
            this.style.boxShadow = '';
        });
    });

    const socialLinks = document.querySelectorAll('.social-link');
    
    socialLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) rotate(5deg)';
        });
        
        link.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    });
}

function initTypingAnimation() {
    const heroTitle = document.querySelector('.hero-title');
    if (heroTitle) {
        const originalHTML = heroTitle.innerHTML;
        const lines = originalHTML.split('<br>');
        heroTitle.innerHTML = '';
        heroTitle.style.borderRight = '3px solid #ec4899';
        
        let currentLine = 0;
        let currentChar = 0;
        
        const typeWriter = () => {
            if (currentLine < lines.length) {
                if (currentChar < lines[currentLine].length) {
                    heroTitle.innerHTML += lines[currentLine].charAt(currentChar);
                    currentChar++;
                    setTimeout(typeWriter, 100);
                } else {
                    if (currentLine < lines.length - 1) {
                        heroTitle.innerHTML += '<br>';
                    }
                    currentLine++;
                    currentChar = 0;
                    setTimeout(typeWriter, 200);
                }
            } else {
                heroTitle.style.borderRight = 'none';
            }
        };
        
        setTimeout(typeWriter, 100);
    }
}

function initCounterAnimations() {
    const counters = document.querySelectorAll('.price');
    
    const animateCounter = (counter) => {
        const target = parseInt(counter.textContent.replace('$', ''));
        let current = 0;
        const increment = target / 50;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            counter.textContent = '$' + Math.floor(current);
        }, 50);
    };

    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                counterObserver.unobserve(entry.target);
            }
        });
    });

    counters.forEach(counter => {
        counterObserver.observe(counter);
    });
}

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

function createScrollToTopButton() {
    const scrollBtn = document.createElement('button');
    scrollBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    scrollBtn.className = 'scroll-to-top-btn';
    scrollBtn.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        background: linear-gradient(45deg, #8b5cf6, #ec4899);
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 1000;
        box-shadow: 0 5px 15px rgba(147, 51, 234, 0.4);
    `;
    
    scrollBtn.addEventListener('click', scrollToTop);
    document.body.appendChild(scrollBtn);

    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            scrollBtn.style.opacity = '1';
            scrollBtn.style.visibility = 'visible';
        } else {
            scrollBtn.style.opacity = '0';
            scrollBtn.style.visibility = 'hidden';
        }
    });
}

createScrollToTopButton();

function initCursorEffects() {
    const cursor = document.createElement('div');
    cursor.className = 'custom-cursor';
    cursor.style.cssText = `
        position: fixed;
        width: 20px;
        height: 20px;
        background: linear-gradient(45deg, #8b5cf6, #ec4899);
        border-radius: 50%;
        pointer-events: none;
        z-index: 9999;
        opacity: 0;
        transition: all 0.1s ease;
        mix-blend-mode: difference;
    `;
    document.body.appendChild(cursor);

    document.addEventListener('mousemove', (e) => {
        cursor.style.left = e.clientX - 10 + 'px';
        cursor.style.top = e.clientY - 10 + 'px';
        cursor.style.opacity = '1';
    });

    document.addEventListener('mouseleave', () => {
        cursor.style.opacity = '0';
    });

    const interactiveElements = document.querySelectorAll('a, button, .product-card, .bundle-card');
    
    interactiveElements.forEach(element => {
        element.addEventListener('mouseenter', () => {
            cursor.style.transform = 'scale(2)';
            cursor.style.background = 'linear-gradient(45deg, #ec4899, #f59e0b)';
        });
        
        element.addEventListener('mouseleave', () => {
            cursor.style.transform = 'scale(1)';
            cursor.style.background = 'linear-gradient(45deg, #8b5cf6, #ec4899)';
        });
    });
}

const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const fadeInObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

document.querySelectorAll('.section-title, .product-card, .bundle-card, .faq-item').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    fadeInObserver.observe(el);
});

document.querySelectorAll('.section-title, .hero-title').forEach(title => {
    title.classList.add('gradient-text');
});

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

const debouncedScrollHandler = debounce(() => {
}, 10);

window.addEventListener('scroll', debouncedScrollHandler);

function preloadImages() {
    const imageUrls = [
        'images/load2.gif'
        
    ];
    
    imageUrls.forEach(url => {
        const img = new Image();
        img.src = url;
    });
}

preloadImages();

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                console.log('SW registered: ', registration);
            })
            .catch(registrationError => {
                console.log('SW registration failed: ', registrationError);
            });
    });
}

window.addEventListener('error', (e) => {
    console.error('JavaScript error:', e.error);
});
function redirectToSignup() {
    const connectBtn = event.target;
    const originalText = connectBtn.innerHTML;
    
    connectBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>CONNECTING...';
    connectBtn.disabled = true;
    connectBtn.style.cursor = 'not-allowed';
    
    connectBtn.style.transform = 'scale(0.95)';
    
    setTimeout(() => {
        window.location.href = 'signup.html';
    }, 800);
    
    setTimeout(() => {
        connectBtn.innerHTML = originalText;
        connectBtn.disabled = false;
        connectBtn.style.cursor = 'pointer';
        connectBtn.style.transform = 'scale(1)';
    }, 3000);
}

function simpleRedirectToSignup() {
    window.location.href = 'signup.html';
}
console.log('%cWelcome to TheSnoozeClub! 🚀', 'color: #9333ea; font-size: 20px; font-weight: bold;');
console.log('%cBuilt with ❤️ using HTML, CSS, and JavaScript', 'color: #ec4899; font-size: 14px;');
