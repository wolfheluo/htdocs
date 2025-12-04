<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wonderland - Fantasy World</title>
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Modern UI Enhancements */
        :root {
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-dark: linear-gradient(135deg, #0c1445 0%, #1a1a2e 50%, #16213e 100%);
            --glow-purple: rgba(102, 126, 234, 0.4);
            --glow-pink: rgba(236, 72, 153, 0.4);
        }

        /* Animated Background */
        .hero-background::after {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse at 20% 30%, rgba(102, 126, 234, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 70%, rgba(236, 72, 153, 0.15) 0%, transparent 50%);
            animation: bgPulse 8s ease-in-out infinite alternate;
            z-index: 0;
        }

        @keyframes bgPulse {
            0% {
                opacity: 0.5;
                transform: scale(1);
            }

            100% {
                opacity: 1;
                transform: scale(1.1);
            }
        }

        /* Floating Particles */
        .particles-container {
            position: absolute;
            inset: 0;
            overflow: hidden;
            z-index: 1;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 6px;
            height: 6px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            animation: floatUp 15s infinite linear;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
        }

        @keyframes floatUp {
            0% {
                transform: translateY(100vh) scale(0);
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: 1;
            }

            100% {
                transform: translateY(-100vh) scale(1);
                opacity: 0;
            }
        }

        /* Enhanced Hero Content */
        .hero-content {
            text-align: center;
            color: var(--text-light);
            z-index: 10;
            position: relative;
            animation: fadeInUp 1s ease-out;
        }

        .hero-title {
            font-size: clamp(2.5rem, 6vw, 4rem);
            font-weight: 700;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #fff 0%, #e0e7ff 50%, #c7d2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: none;
            animation: titleGlow 3s ease-in-out infinite alternate;
        }

        @keyframes titleGlow {
            0% {
                filter: drop-shadow(0 0 20px var(--glow-purple));
            }

            100% {
                filter: drop-shadow(0 0 40px var(--glow-pink));
            }
        }

        .hero-subtitle {
            font-size: 1.3rem;
            margin-bottom: 50px;
            opacity: 0.95;
            letter-spacing: 2px;
            animation: fadeInUp 1s ease-out 0.3s both;
        }

        /* Feature Cards Section */
        .features-section {
            padding: 100px 0;
            background: var(--gradient-dark);
            position: relative;
            overflow: hidden;
        }

        .features-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background:
                radial-gradient(circle at 30% 70%, rgba(102, 126, 234, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 70% 30%, rgba(236, 72, 153, 0.08) 0%, transparent 40%);
            animation: bgFloat 20s ease-in-out infinite;
        }

        @keyframes bgFloat {

            0%,
            100% {
                transform: translate(0, 0) rotate(0deg);
            }

            33% {
                transform: translate(2%, 2%) rotate(1deg);
            }

            66% {
                transform: translate(-1%, -1%) rotate(-1deg);
            }
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            position: relative;
            z-index: 1;
        }

        .feature-card {
            background: linear-gradient(145deg, rgba(30, 30, 50, 0.9), rgba(22, 22, 42, 0.9));
            border-radius: 24px;
            padding: 40px 30px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            cursor: pointer;
            text-decoration: none;
            display: block;
            color: inherit;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .feature-card::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle at top right, var(--glow-purple), transparent 70%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .feature-card:hover {
            transform: translateY(-12px) scale(1.02);
            border-color: rgba(102, 126, 234, 0.3);
            box-shadow:
                0 20px 60px rgba(0, 0, 0, 0.4),
                0 0 40px var(--glow-purple);
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-card:hover::after {
            opacity: 0.3;
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            background: var(--gradient-primary);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            box-shadow: 0 8px 24px var(--glow-purple);
            transition: all 0.4s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 12px 36px var(--glow-purple);
        }

        .feature-card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 12px;
        }

        .feature-card p {
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.7;
            font-size: 1rem;
        }

        .feature-badge {
            display: inline-block;
            margin-top: 16px;
            padding: 8px 20px;
            background: rgba(102, 126, 234, 0.2);
            border: 1px solid rgba(102, 126, 234, 0.4);
            border-radius: 50px;
            font-size: 0.85rem;
            color: #a5b4fc;
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-badge {
            background: var(--gradient-primary);
            border-color: transparent;
            color: #fff;
            transform: scale(1.05);
        }

        /* Section Header */
        .section-header-modern {
            text-align: center;
            margin-bottom: 60px;
            position: relative;
            z-index: 1;
        }

        .section-header-modern h2 {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 700;
            background: linear-gradient(135deg, #fff 0%, #c7d2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 16px;
        }

        .section-header-modern p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 1.1rem;
            max-width: 500px;
            margin: 0 auto;
        }

        /* Interactive CTA Button */
        .cta-container {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 1s ease-out 0.5s both;
        }

        .cta-btn {
            position: relative;
            padding: 18px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            overflow: hidden;
            transition: all 0.4s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
        }

        .cta-btn-primary {
            background: var(--gradient-primary);
            color: white;
            box-shadow: 0 8px 30px var(--glow-purple);
        }

        .cta-btn-primary:hover {
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 12px 40px var(--glow-purple);
        }

        .cta-btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .cta-btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-4px);
        }

        .cta-btn .btn-icon {
            font-size: 1.3rem;
            transition: transform 0.3s ease;
        }

        .cta-btn:hover .btn-icon {
            transform: translateX(4px);
        }

        /* Mouse Scroll Indicator */
        .scroll-indicator {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            animation: bounce 2s infinite;
            cursor: pointer;
        }

        .scroll-indicator .mouse {
            width: 30px;
            height: 50px;
            border: 2px solid rgba(255, 255, 255, 0.6);
            border-radius: 25px;
            position: relative;
        }

        .scroll-indicator .wheel {
            width: 4px;
            height: 10px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 2px;
            position: absolute;
            top: 8px;
            left: 50%;
            transform: translateX(-50%);
            animation: scroll 2s infinite;
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateX(-50%) translateY(0);
            }

            40% {
                transform: translateX(-50%) translateY(-10px);
            }

            60% {
                transform: translateX(-50%) translateY(-5px);
            }
        }

        @keyframes scroll {
            0% {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }

            100% {
                opacity: 0;
                transform: translateX(-50%) translateY(15px);
            }
        }

        /* Contact Section Enhancement */
        .contact {
            background: var(--gradient-dark);
            position: relative;
            overflow: hidden;
        }

        .contact::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse at 50% 0%, rgba(102, 126, 234, 0.2) 0%, transparent 50%);
        }

        .contact .section-title {
            font-size: clamp(2rem, 4vw, 2.5rem);
            background: linear-gradient(135deg, #fff 0%, #c7d2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .contact-item {
            max-width: 500px;
            margin: 0 auto;
            background: linear-gradient(145deg, rgba(30, 30, 50, 0.8), rgba(22, 22, 42, 0.8));
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
        }

        .contact-item:hover {
            border-color: rgba(102, 126, 234, 0.3);
            box-shadow: 0 8px 32px rgba(102, 126, 234, 0.2);
        }

        /* Footer Enhancement */
        .footer {
            background: #0a0a1a;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .footer p {
            color: rgba(255, 255, 255, 0.5);
        }

        /* Cursor Trail Effect */
        .cursor-trail {
            position: fixed;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: radial-gradient(circle, var(--glow-purple) 0%, transparent 70%);
            pointer-events: none;
            z-index: 9999;
            mix-blend-mode: screen;
            opacity: 0;
            transition: opacity 0.3s;
        }

        body:hover .cursor-trail {
            opacity: 1;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .cta-container {
                flex-direction: column;
                align-items: center;
            }

            .cta-btn {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .scroll-indicator {
                display: none;
            }
        }
    </style>
</head>

<body>
    <!-- Cursor Trail Effect -->
    <div class="cursor-trail" id="cursorTrail"></div>

    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <a href="#home">
                    <img src="assets/images/logo.svg" alt="Wonderland Logo" class="logo">
                </a>
            </div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="#home" class="nav-link">HOME</a>
                </li>
                <li class="nav-item">
                    <a href="#features" class="nav-link">EXPLORE</a>
                </li>
                <li class="nav-item">
                    <a href="#contact" class="nav-link">CONTACT</a>
                </li>
            </ul>
        </div>
    </nav>


    <!-- Home Section -->
    <section id="home" class="hero">
        <div class="hero-background">
            <div class="slideshow-container">
                <div class="slide active">
                    <img src="assets/images/homeBG.webp" alt="Fantasy Background">
                </div>
            </div>
        </div>

        <!-- Floating Particles -->
        <div class="particles-container" id="particlesContainer"></div>

        <div class="hero-content">
            <h1 class="hero-title">Welcome to Wonderland</h1>
        </div>

        <!-- Scroll Indicator -->
        <div class="scroll-indicator"
            onclick="document.getElementById('features').scrollIntoView({behavior: 'smooth'})">
            <div class="mouse">
                <div class="wheel"></div>
            </div>
        </div>
    </section>

    <!-- Features Section (Display Pages) -->
    <section id="features" class="features-section">
        <div class="section-header-modern">
            <h2>✨ 探索心靈角落</h2>
            <p>發現專屬於你的療癒空間</p>
        </div>

        <div class="features-grid">
            <!-- 心事小卡 Card -->
            <a href="display/index.php" class="feature-card">
                <div class="feature-icon">💭</div>
                <h3>心事小卡</h3>
                <p>每一句話，都是一張翻動心靈的卡片。收藏無數心情語錄，在卡片海洋中尋找共鳴。</p>
                <span class="feature-badge">進入體驗 →</span>
            </a>

            <!-- 暗夜句庫 Card -->
            <a href="display/PPT.php" class="feature-card">
                <div class="feature-icon">🌙</div>
                <h3>暗夜句庫</h3>
                <p>沉浸在紫色星光中，讓每一句話隨著粒子飄揚。30秒自動切換，帶來不一樣的心靈觸動。</p>
                <span class="feature-badge">進入體驗 →</span>
            </a>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">CONTACT</h2>
                <div class="title-underline"></div>
            </div>
            <div class="contact-content">
                <div class="contact-info">
                    <h3>Get in Touch</h3>
                    <p>歡迎透過以下方式聯繫我們，一起探索更多可能性。</p>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div class="contact-details">
                            <span class="contact-label">Line 官方帳號</span>
                            <a href="https://lin.ee/cdZA0Ok" target="_blank" class="contact-link">
                                點擊加入官方帳號
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2017 - Powered by wolfheluo.</p>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
    <script>
        // Floating Particles
        function createParticles() {
            const container = document.getElementById('particlesContainer');
            const particleCount = 30;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 15 + 's';
                particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
                particle.style.opacity = Math.random() * 0.5 + 0.3;
                particle.style.width = (Math.random() * 4 + 2) + 'px';
                particle.style.height = particle.style.width;
                container.appendChild(particle);
            }
        }

        // Cursor Trail Effect
        function initCursorTrail() {
            const trail = document.getElementById('cursorTrail');
            let mouseX = 0, mouseY = 0;
            let trailX = 0, trailY = 0;

            document.addEventListener('mousemove', (e) => {
                mouseX = e.clientX;
                mouseY = e.clientY;
            });

            function animateTrail() {
                trailX += (mouseX - trailX) * 0.15;
                trailY += (mouseY - trailY) * 0.15;
                trail.style.left = trailX - 10 + 'px';
                trail.style.top = trailY - 10 + 'px';
                requestAnimationFrame(animateTrail);
            }

            animateTrail();
        }

        // Interactive Card Tilt Effect
        function initCardTilt() {
            const cards = document.querySelectorAll('.feature-card');

            cards.forEach(card => {
                card.addEventListener('mousemove', (e) => {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;
                    const rotateX = (y - centerY) / 20;
                    const rotateY = (centerX - x) / 20;

                    card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-12px) scale(1.02)`;
                });

                card.addEventListener('mouseleave', () => {
                    card.style.transform = '';
                });
            });
        }

        // Smooth Scroll Enhancement
        function initSmoothScroll() {
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
        }

        // Intersection Observer for Animations
        function initScrollAnimations() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.feature-card').forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = `all 0.6s ease ${index * 0.15}s`;
                observer.observe(card);
            });
        }

        // Initialize all interactive features
        document.addEventListener('DOMContentLoaded', () => {
            createParticles();
            initCursorTrail();
            initCardTilt();
            initSmoothScroll();
            initScrollAnimations();
        });
    </script>
</body>

</html>