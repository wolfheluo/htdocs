// DOM 載入完成後執行
$(document).ready(function() {
    // 初始化功能
    initSmoothScrolling();
    initMusicControl();
    initServicesLoader();
    initScrollEffects();
    initNavigationHighlight();
});

// 平滑滾動功能
function initSmoothScrolling() {
    $('.nav-link, .cta-button').on('click', function(e) {
        const href = $(this).attr('href');
        if (href && href.startsWith('#')) {
            e.preventDefault();
            const target = $(href);
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 70
                }, 800);
            }
        }
    });
}

// 音樂控制功能
function initMusicControl() {
    const audio = document.getElementById('backgroundMusic');
    const musicToggle = document.getElementById('musicToggle');
    let isPlaying = false;

    // 預設音樂為暫停狀態
    audio.volume = 0.3;

    musicToggle.addEventListener('click', function() {
        if (isPlaying) {
            audio.pause();
            musicToggle.innerHTML = '🎵';
            musicToggle.style.background = 'var(--gradient-bg)';
            isPlaying = false;
        } else {
            audio.play().then(() => {
                musicToggle.innerHTML = '🎶';
                musicToggle.style.background = 'var(--gradient-accent)';
                isPlaying = true;
            }).catch((error) => {
                console.log('Music playback failed:', error);
                // Show friendly error message
                showNotification('Music playback requires user interaction, please try clicking the button again');
            });
        }
    });

    // 音樂結束時重置按鈕
    audio.addEventListener('ended', function() {
        musicToggle.innerHTML = '🎵';
        musicToggle.style.background = 'var(--gradient-bg)';
        isPlaying = false;
    });
}

// Load services data
function initServicesLoader() {
    $.getJSON('services.json')
        .done(function(data) {
            const servicesGrid = $('#servicesGrid');
            
            if (data.services && data.services.length > 0) {
                data.services.forEach(function(service, index) {
                    const serviceCard = createServiceCard(service, index);
                    servicesGrid.append(serviceCard);
                });
                
                // Add entrance animation
                $('.service-card').each(function(index) {
                    $(this).css('animation-delay', (index * 0.1) + 's');
                    $(this).addClass('fade-in-up');
                });
            } else {
                servicesGrid.html('<p class="no-services">No services available at the moment</p>');
            }
        })
        .fail(function() {
            $('#servicesGrid').html('<p class="error-message">Failed to load services data, please try again later</p>');
        });
}

// Create service card
function createServiceCard(service, index) {
    return $(`
        <div class="service-card" style="animation-delay: ${index * 0.1}s">
            <h3>${escapeHtml(service.name)}</h3>
            <a href="${escapeHtml(service.url)}" target="_blank" class="service-url">${escapeHtml(service.url)}</a>
        </div>
    `);
}

// HTML escape function
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// 滾動效果
function initScrollEffects() {
    $(window).on('scroll', function() {
        const scrollTop = $(window).scrollTop();
        
        // 導覽列背景效果
        if (scrollTop > 50) {
            $('.navbar').addClass('scrolled');
        } else {
            $('.navbar').removeClass('scrolled');
        }
        
        // 元素進入視窗動畫
        $('.about-item, .service-card').each(function() {
            const elementTop = $(this).offset().top;
            const windowBottom = $(window).scrollTop() + $(window).height();
            
            if (elementTop < windowBottom - 100) {
                $(this).addClass('animate-in');
            }
        });
    });
}

// 導覽列高亮效果
function initNavigationHighlight() {
    $(window).on('scroll', function() {
        const scrollTop = $(window).scrollTop();
        
        $('.nav-link').each(function() {
            const href = $(this).attr('href');
            if (href && href.startsWith('#')) {
                const section = $(href);
                if (section.length) {
                    const sectionTop = section.offset().top - 100;
                    const sectionBottom = sectionTop + section.outerHeight();
                    
                    if (scrollTop >= sectionTop && scrollTop < sectionBottom) {
                        $('.nav-link').removeClass('active');
                        $(this).addClass('active');
                    }
                }
            }
        });
    });
}

// 滾動到指定區塊
function scrollToSection(sectionId) {
    const target = $('#' + sectionId);
    if (target.length) {
        $('html, body').animate({
            scrollTop: target.offset().top - 70
        }, 800);
    }
}

// 顯示通知訊息
function showNotification(message) {
    // 創建通知元素
    const notification = $(`
        <div class="notification">
            <span>${message}</span>
            <button class="notification-close">&times;</button>
        </div>
    `);
    
    // 添加到頁面
    $('body').append(notification);
    
    // 顯示動畫
    setTimeout(() => {
        notification.addClass('show');
    }, 100);
    
    // 自動隱藏
    setTimeout(() => {
        hideNotification(notification);
    }, 3000);
    
    // 點擊關閉
    notification.find('.notification-close').on('click', function() {
        hideNotification(notification);
    });
}

// 隱藏通知
function hideNotification(notification) {
    notification.removeClass('show');
    setTimeout(() => {
        notification.remove();
    }, 300);
}

// 添加額外的 CSS 樣式
const additionalStyles = `
    <style>
        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }
        
        .nav-link.active {
            color: var(--primary-color);
        }
        
        .nav-link.active::after {
            width: 100%;
        }
        
        .fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
            transform: translateY(30px);
        }
        
        .animate-in {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--gradient-bg);
            color: white;
            padding: 15px 20px;
            border-radius: var(--border-radius);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            z-index: 10000;
            transform: translateX(400px);
            opacity: 0;
            transition: all 0.3s ease;
            max-width: 300px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        
        .notification.show {
            transform: translateX(0);
            opacity: 1;
        }
        
        .notification-close {
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            padding: 0;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .no-services, .error-message {
            text-align: center;
            color: var(--text-gray);
            font-style: italic;
            grid-column: 1 / -1;
            padding: 40px;
        }
        
        .error-message {
            color: #f44336;
        }
        
        @media (max-width: 480px) {
            .notification {
                right: 10px;
                left: 10px;
                max-width: none;
                transform: translateY(-100px);
            }
            
            .notification.show {
                transform: translateY(0);
            }
        }
    </style>
`;

// 將額外樣式添加到頁面
$('head').append(additionalStyles);
