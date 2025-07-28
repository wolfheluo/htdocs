// 響應式網頁增強功能 - 優化版本

document.addEventListener('DOMContentLoaded', function() {
    
    // 平滑滾動功能
    function smoothScroll() {
        const links = document.querySelectorAll('a[href^="#"]');
        
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    const navHeight = window.innerWidth <= 640 ? 80 : 100;
                    const offsetTop = targetElement.offsetTop - navHeight;
                    
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }
    
    // 導航欄滾動效果
    function navbarScrollEffect() {
        const navbar = document.querySelector('.menubar');
        let lastScrollY = window.scrollY;
        
        window.addEventListener('scroll', function() {
            const currentScrollY = window.scrollY;
            
            if (currentScrollY > 50) {
                navbar.style.backgroundColor = 'rgba(238, 238, 238, 0.98)';
                navbar.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
            } else {
                navbar.style.backgroundColor = 'rgba(238, 238, 238, 0.95)';
                navbar.style.boxShadow = 'none';
            }
            
            // 向下滾動時隱藏導航欄，向上滾動時顯示（僅在移動設備）
            if (window.innerWidth <= 640) {
                if (currentScrollY > lastScrollY && currentScrollY > 100) {
                    navbar.style.transform = 'translateY(-100%)';
                } else {
                    navbar.style.transform = 'translateY(0)';
                }
            }
            
            lastScrollY = currentScrollY;
        });
    }
    
    // 表單驗證增強版
    function formValidation() {
        const form = document.querySelector('form[action="mail.php"]');
        
        if (form) {
            // 實時驗證
            const fields = {
                name: document.getElementById('name'),
                email: document.getElementById('email'),
                content: document.getElementById('anliegen')
            };
            
            // 添加實時驗證
            Object.values(fields).forEach(field => {
                if (field) {
                    field.addEventListener('blur', function() {
                        validateField(this);
                    });
                    
                    field.addEventListener('input', function() {
                        // 清除錯誤狀態
                        this.style.borderColor = '#ddd';
                        const errorMsg = this.parentNode.querySelector('.error-message');
                        if (errorMsg) {
                            errorMsg.remove();
                        }
                    });
                }
            });
            
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                // 清除之前的錯誤樣式
                Object.values(fields).forEach(field => {
                    if (field) {
                        field.style.borderColor = '#ddd';
                        const errorMsg = field.parentNode.querySelector('.error-message');
                        if (errorMsg) {
                            errorMsg.remove();
                        }
                    }
                });
                
                // 驗證所有欄位
                Object.entries(fields).forEach(([key, field]) => {
                    if (field && !validateField(field)) {
                        isValid = false;
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    // 滾動到第一個錯誤欄位
                    const firstError = form.querySelector('input[style*="rgb(255, 107, 107)"], textarea[style*="rgb(255, 107, 107)"]');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstError.focus();
                    }
                }
            });
        }
        
        function validateField(field) {
            const value = field.value.trim();
            let isValid = true;
            let errorMessage = '';
            
            // 移除舊的錯誤消息
            const existingError = field.parentNode.querySelector('.error-message');
            if (existingError) {
                existingError.remove();
            }
            
            switch (field.id) {
                case 'name':
                    if (!value) {
                        errorMessage = '請輸入您的姓名';
                        isValid = false;
                    } else if (value.length < 2) {
                        errorMessage = '姓名至少需要2個字符';
                        isValid = false;
                    }
                    break;
                    
                case 'email':
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!value) {
                        errorMessage = '請輸入您的郵箱地址';
                        isValid = false;
                    } else if (!emailRegex.test(value)) {
                        errorMessage = '請輸入有效的郵箱地址';
                        isValid = false;
                    }
                    break;
                    
                case 'anliegen':
                    if (!value) {
                        errorMessage = '請輸入您的訊息內容';
                        isValid = false;
                    } else if (value.length < 10) {
                        errorMessage = '訊息內容至少需要10個字符';
                        isValid = false;
                    }
                    break;
            }
            
            if (!isValid) {
                field.style.borderColor = '#ff6b6b';
                field.style.boxShadow = '0 0 5px rgba(255, 107, 107, 0.3)';
                
                // 添加錯誤消息
                const errorElement = document.createElement('div');
                errorElement.className = 'error-message';
                errorElement.style.cssText = `
                    color: #ff6b6b;
                    font-size: 12px;
                    margin-top: 5px;
                    margin-bottom: 10px;
                `;
                errorElement.textContent = errorMessage;
                field.parentNode.insertBefore(errorElement, field.nextSibling);
            }
            
            return isValid;
        }
    }
    
    // 懶加載圖片優化
    function lazyLoadImages() {
        const images = document.querySelectorAll('img[src], img[data-src]');
        
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    
                    // 預加載效果
                    img.style.opacity = '0';
                    img.style.transition = 'opacity 0.3s ease';
                    
                    const loadImage = () => {
                        img.style.opacity = '1';
                    };
                    
                    if (img.complete) {
                        loadImage();
                    } else {
                        img.onload = loadImage;
                        img.onerror = () => {
                            img.style.opacity = '0.5';
                            console.warn('Failed to load image:', img.src);
                        };
                    }
                    
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px'
        });
        
        images.forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    // 滾動動畫優化
    function scrollAnimations() {
        const animatedElements = document.querySelectorAll('.headingbox, .col-1, .col-2');
        
        const animationObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    entry.target.classList.add('animated');
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        animatedElements.forEach(element => {
            // 避免重複設置
            if (!element.classList.contains('animation-ready')) {
                element.style.opacity = '0';
                element.style.transform = 'translateY(30px)';
                element.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
                element.classList.add('animation-ready');
                animationObserver.observe(element);
            }
        });
    }
    
    // 背景音樂控制優化
    function audioControl() {
        const audio = document.getElementById('bgaudio');
        
        if (audio) {
            // 創建音樂控制按鈕
            const audioBtn = document.createElement('button');
            audioBtn.innerHTML = '🔇';
            audioBtn.setAttribute('aria-label', '背景音樂控制');
            audioBtn.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                border: none;
                background: rgba(0, 122, 204, 0.8);
                color: white;
                font-size: 18px;
                cursor: pointer;
                z-index: 10000;
                transition: all 0.3s ease;
                box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            `;
            
            let isPlaying = false;
            let userInteracted = false;
            
            audioBtn.addEventListener('click', function() {
                userInteracted = true;
                
                if (isPlaying) {
                    audio.pause();
                    this.innerHTML = '🔇';
                    this.setAttribute('aria-label', '播放背景音樂');
                    isPlaying = false;
                } else {
                    audio.play().then(() => {
                        this.innerHTML = '🔊';
                        this.setAttribute('aria-label', '暫停背景音樂');
                        isPlaying = true;
                    }).catch(e => {
                        console.log('Audio play failed:', e);
                        this.innerHTML = '❌';
                    });
                }
            });
            
            audioBtn.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.1)';
                this.style.boxShadow = '0 4px 15px rgba(0,122,204,0.4)';
            });
            
            audioBtn.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
                this.style.boxShadow = '0 2px 10px rgba(0,0,0,0.2)';
            });
            
            document.body.appendChild(audioBtn);
            
            // 音頻設置
            audio.volume = 0.2;
            audio.loop = true;
            
            // 監聽音頻事件
            audio.addEventListener('ended', function() {
                if (userInteracted) {
                    audioBtn.innerHTML = '🔇';
                    isPlaying = false;
                }
            });
            
            audio.addEventListener('error', function() {
                audioBtn.innerHTML = '❌';
                audioBtn.style.opacity = '0.5';
            });
        }
    }
    
    // 鍵盤導航支持
    function keyboardNavigation() {
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                // ESC鍵關閉任何開啟的對話框或重置焦點
                const focusedElement = document.activeElement;
                if (focusedElement && focusedElement !== document.body) {
                    focusedElement.blur();
                }
            }
            
            // Tab鍵確保焦點可見
            if (e.key === 'Tab') {
                document.body.classList.add('keyboard-navigation');
            }
        });
        
        // 滑鼠點擊時移除鍵盤導航樣式
        document.addEventListener('mousedown', function() {
            document.body.classList.remove('keyboard-navigation');
        });
    }
    
    // 性能監控
    function performanceMonitoring() {
        // 監控長任務
        if ('PerformanceObserver' in window) {
            const observer = new PerformanceObserver((list) => {
                list.getEntries().forEach((entry) => {
                    if (entry.duration > 50) {
                        console.warn('Long task detected:', entry.duration, 'ms');
                    }
                });
            });
            
            try {
                observer.observe({ entryTypes: ['longtask'] });
            } catch (e) {
                // 某些瀏覽器可能不支持
                console.log('Performance monitoring not supported');
            }
        }
    }
    
    // 初始化所有功能
    smoothScroll();
    navbarScrollEffect();
    formValidation();
    keyboardNavigation();
    performanceMonitoring();
    
    // 檢查瀏覽器支持
    if ('IntersectionObserver' in window) {
        lazyLoadImages();
        scrollAnimations();
    } else {
        // 降級處理
        console.warn('IntersectionObserver not supported, using fallback');
        const elements = document.querySelectorAll('.headingbox, .col-1, .col-2');
        elements.forEach(el => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        });
    }
    
    audioControl();
    
    // 性能優化：防抖函數
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
    
    // 節流函數
    function throttle(func, wait) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, wait);
            }
        }
    }
    
    // 視窗大小改變時的處理
    window.addEventListener('resize', debounce(function() {
        // 重新計算視窗高度（防止移動設備地址欄影響）
        const vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
        
        // 重新初始化某些功能
        if (window.innerWidth <= 640) {
            // 移動設備特定邏輯
        }
    }, 250));
    
    // 初始設置視窗高度
    const vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);
    
    // 頁面可見性 API
    if ('visibilityState' in document) {
        document.addEventListener('visibilitychange', function() {
            const audio = document.getElementById('bgaudio');
            if (audio && !audio.paused) {
                if (document.hidden) {
                    audio.pause();
                } else {
                    // 用戶返回頁面時可選擇性恢復播放
                }
            }
        });
    }
    
    // 頁面加載完成提示
    console.log('Wonderland enhanced interactions loaded successfully! 🎉');
});
