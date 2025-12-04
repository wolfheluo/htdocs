<?php
// 處理 AJAX 請求
if (isset($_GET['action']) && $_GET['action'] === 'fetch_messages') {
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Headers: Content-Type');

    $backupFile = 'messages_backup.json';

    try {
        $ch = curl_init('https://16888gk.com/line-api-wolf/static/messages.json');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]);

        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($data === false || $error)
            throw new Exception("cURL Error: $error");
        if ($httpCode !== 200)
            throw new Exception("HTTP Error: $httpCode");
        if (json_decode($data) === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON: ' . json_last_error_msg());
        }

        file_put_contents($backupFile, $data);
        echo $data;
        exit;

    } catch (Exception $e) {
        if (file_exists($backupFile) && ($backupData = file_get_contents($backupFile)) !== false) {
            echo $backupData;
            exit;
        }
        http_response_code(500);
        echo json_encode(['error' => true, 'message' => '獲取訊息失敗: ' . $e->getMessage()]);
        exit;
    }
}

require_once 'counter.php';
?>
<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>心事小卡 💭</title>
    <meta name="description" content="一個收藏心情語錄的小角落，每一句話都是一張小卡片">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap"
        rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root,
        [data-theme="purple"] {
            --bg-primary: #0f0f1a;
            --bg-card: linear-gradient(145deg, #1e1e32, #16162a);
            --text-primary: #e8e8f0;
            --text-secondary: #a8a8c0;
            --text-muted: #6a6a8a;
            --accent-2: #8b5cf6;
            --accent-3: #ec4899;
            --accent-gradient: linear-gradient(135deg, #6366f1, #8b5cf6 50%, #ec4899);
            --glow-color: rgba(139, 92, 246, 0.3);
            --card-shadow: 0 8px 32px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.05);
            --card-hover-shadow: 0 16px 48px rgba(139, 92, 246, 0.2), 0 0 0 1px rgba(139, 92, 246, 0.2);
            --quote-color: rgba(139, 92, 246, 0.02);
            --bg-gradient: radial-gradient(ellipse at 20% 30%, rgba(99, 102, 241, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 70%, rgba(236, 72, 153, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 50%, rgba(139, 92, 246, 0.05) 0%, transparent 60%);
        }

        [data-theme="ocean"] {
            --bg-primary: #0a0f1f;
            --bg-card: linear-gradient(145deg, #101a2e, #0c1322);
            --text-primary: #d6e4ff;
            --text-secondary: #9db8e8;
            --text-muted: #6889b8;
            --accent-2: #5cabff;
            --accent-3: #06b6d4;
            --accent-gradient: linear-gradient(135deg, #3b82f6, #5cabff 50%, #06b6d4);
            --glow-color: rgba(92, 171, 255, 0.3);
            --card-shadow: 0 8px 32px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(92, 171, 255, 0.05);
            --card-hover-shadow: 0 16px 48px rgba(92, 171, 255, 0.2), 0 0 0 1px rgba(92, 171, 255, 0.2);
            --quote-color: rgba(92, 171, 255, 0.02);
            --bg-gradient: radial-gradient(ellipse at 20% 30%, rgba(59, 130, 246, 0.1) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 70%, rgba(6, 182, 212, 0.1) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 50%, rgba(92, 171, 255, 0.06) 0%, transparent 60%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Noto Sans TC', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            line-height: 1.8;
            overflow-x: hidden;
            transition: background 0.5s, color 0.5s;
        }

        .bg-animation {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .bg-animation::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: var(--bg-gradient);
            animation: bgFloat 20s ease-in-out infinite;
            transition: background 0.5s;
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

        .particles-canvas {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 1;
            opacity: 0;
            transition: opacity 0.5s;
        }

        [data-theme="ocean"] .particles-canvas {
            opacity: 1;
        }

        .theme-switcher {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 1000;
            display: flex;
            gap: 8px;
            padding: 6px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 50px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .theme-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border: none;
            border-radius: 50px;
            background: transparent;
            color: var(--text-secondary);
            font: inherit;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s;
            white-space: nowrap;
        }

        .theme-btn:hover {
            color: var(--text-primary);
            background: rgba(255, 255, 255, 0.05);
        }

        .theme-btn.active {
            background: var(--accent-gradient);
            color: #fff;
            box-shadow: 0 4px 15px var(--glow-color);
        }

        .theme-btn .icon {
            font-size: 1.1rem;
        }

        .header {
            position: relative;
            z-index: 10;
            padding: 60px 20px 40px;
            text-align: center;
        }

        .header h1 {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 700;
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 16px;
            text-shadow: 0 0 60px var(--glow-color);
        }

        .header p {
            font-size: 1.1rem;
            color: var(--text-secondary);
            font-weight: 300;
            letter-spacing: 0.5px;
        }

        .visitor-count {
            margin-top: 24px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 20px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 100px;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .visitor-count .heart {
            color: var(--accent-3);
            animation: heartbeat 1.5s ease-in-out infinite;
        }

        @keyframes heartbeat {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .masonry-grid {
            column-count: 4;
            column-gap: 24px;
        }

        .message-card {
            break-inside: avoid;
            margin-bottom: 24px;
            background: var(--bg-card);
            border-radius: 20px;
            padding: 28px;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(255, 255, 255, 0.04);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .message-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--accent-gradient);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .message-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--card-hover-shadow);
        }

        .message-card:hover::before {
            opacity: 1;
        }

        .message-card:nth-child(5n+1) {
            --card-accent: #6366f1;
        }

        .message-card:nth-child(5n+2) {
            --card-accent: #8b5cf6;
        }

        .message-card:nth-child(5n+3) {
            --card-accent: #ec4899;
        }

        .message-card:nth-child(5n+4) {
            --card-accent: #14b8a6;
        }

        .message-card:nth-child(5n+5) {
            --card-accent: #f59e0b;
        }

        [data-theme="ocean"] .message-card:nth-child(5n+1) {
            --card-accent: #3b82f6;
        }

        [data-theme="ocean"] .message-card:nth-child(5n+2) {
            --card-accent: #5cabff;
        }

        [data-theme="ocean"] .message-card:nth-child(5n+3) {
            --card-accent: #06b6d4;
        }

        [data-theme="ocean"] .message-card:nth-child(5n+4) {
            --card-accent: #0ea5e9;
        }

        [data-theme="ocean"] .message-card:nth-child(5n+5) {
            --card-accent: #22d3d1;
        }

        .message-card::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
            height: 80px;
            background: radial-gradient(circle at top right, var(--card-accent, var(--accent-2)), transparent 70%);
            opacity: 0.1;
            pointer-events: none;
        }

        .message-content {
            font-size: 1.05rem;
            line-height: 1.9;
            color: var(--text-primary);
            white-space: pre-line;
            position: relative;
            z-index: 1;
        }

        .message-content::first-letter {
            font-size: 1.4em;
            font-weight: 500;
            color: var(--accent-2);
        }

        .message-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .message-author {
            font-size: 0.85rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .message-author::before {
            content: '✦';
            font-size: 0.7rem;
            color: var(--accent-2);
        }

        .message-date {
            font-size: 0.8rem;
            color: var(--text-muted);
            opacity: 0.6;
        }

        .loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 300px;
            gap: 20px;
        }

        .loading-spinner {
            width: 48px;
            height: 48px;
            border: 3px solid rgba(139, 92, 246, 0.1);
            border-top-color: var(--accent-2);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .loading-text {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .error-message {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }

        .error-message h3 {
            color: var(--accent-3);
            margin-bottom: 12px;
        }

        .footer {
            text-align: center;
            padding: 60px 20px 40px;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .scroll-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--bg-card);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
            z-index: 100;
            box-shadow: var(--card-shadow);
        }

        .scroll-top.visible {
            opacity: 1;
            visibility: visible;
        }

        .scroll-top:hover {
            transform: translateY(-4px);
            background: var(--accent-gradient);
        }

        .scroll-top svg {
            width: 20px;
            height: 20px;
            fill: var(--text-primary);
        }

        .quote-decoration {
            position: fixed;
            font-size: 20rem;
            font-family: Georgia, serif;
            color: var(--quote-color);
            pointer-events: none;
            z-index: 0;
            transition: color 0.5s;
        }

        .quote-decoration.left {
            top: 10%;
            left: -5%;
        }

        .quote-decoration.right {
            bottom: 10%;
            right: -5%;
            transform: rotate(180deg);
        }

        @media (max-width: 1200px) {
            .masonry-grid {
                column-count: 3;
            }
        }

        @media (max-width: 900px) {
            .masonry-grid {
                column-count: 2;
            }
        }

        @media (max-width: 600px) {
            .masonry-grid {
                column-count: 1;
            }

            .theme-switcher {
                top: auto;
                bottom: 90px;
                right: 16px;
                padding: 4px;
            }

            .theme-btn {
                padding: 10px 14px;
                font-size: 0.8rem;
            }

            .theme-btn span:not(.icon) {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="bg-animation"></div>
    <canvas class="particles-canvas" id="particles"></canvas>
    <div class="quote-decoration left">"</div>
    <div class="quote-decoration right">"</div>

    <div class="theme-switcher">
        <button class="theme-btn active" data-theme="purple" aria-label="紫夜星光主題">
            <span class="icon">🌌</span><span>紫夜星光</span>
        </button>
        <button class="theme-btn" data-theme="ocean" aria-label="星海漂流主題">
            <span class="icon">🌊</span><span>星海漂流</span>
        </button>
    </div>

    <header class="header">
        <h1>💭 心事小卡</h1>
        <p>每一句話，都是一張翻動心靈的卡片</p>
        <div class="visitor-count">
            <span class="heart">♥</span>
            <span>第 <strong><?php echo formatCount($count); ?></strong> 位訪客</span>
        </div>
    </header>

    <main class="container">
        <div id="grid" class="masonry-grid">
            <div class="loading" id="loading">
                <div class="loading-spinner"></div>
                <p class="loading-text">正在翻開心事...</p>
            </div>
        </div>
    </main>

    <button class="scroll-top" id="scrollTop" aria-label="回到頂部">
        <svg viewBox="0 0 24 24">
            <path d="M12 4l-8 8h5v8h6v-8h5z" />
        </svg>
    </button>

    <footer class="footer">
        <p>用心收藏每一個瞬間 ✨</p>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        (() => {
            'use strict';

            // Constants
            const NAME_MAP = { '黃敬欽': 'wolfheluo' };
            const ANIMATIONS = ['fade-up', 'fade-up-right', 'fade-up-left', 'zoom-in'];
            const DAY_MS = 864e5;

            // DOM cache
            const $ = id => document.getElementById(id);
            const grid = $('grid');
            const loading = $('loading');
            const scrollBtn = $('scrollTop');
            const canvas = $('particles');
            const ctx = canvas.getContext('2d');
            const themeBtns = document.querySelectorAll('.theme-btn');

            // Initialize AOS
            AOS.init({ duration: 800, easing: 'ease-out-cubic', once: true, offset: 50 });

            // Utilities
            const escapeHtml = text => {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            };

            const shuffle = arr => {
                const a = [...arr];
                for (let i = a.length - 1; i > 0; i--) {
                    const j = Math.random() * (i + 1) | 0;
                    [a[i], a[j]] = [a[j], a[i]];
                }
                return a;
            };

            const formatDate = ts => {
                const days = (Date.now() - new Date(ts)) / DAY_MS | 0;
                if (days === 0) return '今天';
                if (days === 1) return '昨天';
                if (days < 7) return `${days} 天前`;
                if (days < 30) return `${days / 7 | 0} 週前`;
                return new Date(ts).toLocaleDateString('zh-TW', { year: 'numeric', month: 'short', day: 'numeric' });
            };

            const createCard = (msg, i) => `
            <article class="message-card" data-aos="${ANIMATIONS[i % 4]}" data-aos-delay="${(i % 10) * 50}">
                <div class="message-content">${escapeHtml(msg.message)}</div>
                <div class="message-meta">
                    <span class="message-author">${escapeHtml(NAME_MAP[msg.user_name] || msg.user_name)}</span>
                    <span class="message-date">${formatDate(msg.timestamp)}</span>
                </div>
            </article>`;

            // Load messages
            const loadMessages = async () => {
                try {
                    const res = await fetch('?action=fetch_messages');
                    if (!res.ok) throw new Error();
                    const data = await res.json();
                    const msgs = shuffle(data.filter(m => m.message_type === 'text' && m.message.trim()));
                    loading.remove();
                    grid.innerHTML = msgs.map(createCard).join('');
                    AOS.refresh();
                } catch {
                    grid.innerHTML = '<div class="error-message"><h3>😢 哎呀！</h3><p>暫時無法讀取心事，請稍後再試</p></div>';
                }
            };

            // Scroll to top
            let ticking = false;
            window.addEventListener('scroll', () => {
                if (!ticking) {
                    requestAnimationFrame(() => {
                        scrollBtn.classList.toggle('visible', scrollY > 300);
                        ticking = false;
                    });
                    ticking = true;
                }
            }, { passive: true });

            scrollBtn.addEventListener('click', () => scrollTo({ top: 0, behavior: 'smooth' }));

            // Particles
            let particles = [], animId = null;

            const resize = () => {
                canvas.width = innerWidth;
                canvas.height = innerHeight;
            };

            const initParticles = () => {
                particles = Array.from({ length: 100 }, () => ({
                    x: Math.random() * canvas.width,
                    y: Math.random() * canvas.height,
                    s: Math.random() * 2.5 + 0.5,
                    v: Math.random() * 0.6 + 0.2,
                    o: Math.random() * 0.5 + 0.3
                }));
            };

            const animate = () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                for (const p of particles) {
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.s, 0, Math.PI * 2);
                    ctx.fillStyle = `rgba(120,170,255,${p.o})`;
                    ctx.fill();
                    p.y -= p.v;
                    if (p.y < -10) { p.y = canvas.height + 10; p.x = Math.random() * canvas.width; }
                }
                animId = requestAnimationFrame(animate);
            };

            const toggleParticles = on => {
                if (on && !animId) animate();
                else if (!on && animId) {
                    cancelAnimationFrame(animId);
                    animId = null;
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                }
            };

            // Theme
            const setTheme = theme => {
                document.body.dataset.theme = theme;
                localStorage.setItem('theme', theme);
                themeBtns.forEach(b => b.classList.toggle('active', b.dataset.theme === theme));
                toggleParticles(theme === 'ocean');
            };

            themeBtns.forEach(b => b.addEventListener('click', () => setTheme(b.dataset.theme)));

            // Init
            resize();
            initParticles();
            window.addEventListener('resize', () => { resize(); initParticles(); });
            setTheme(localStorage.getItem('theme') || 'purple');
            document.addEventListener('DOMContentLoaded', loadMessages);
        })();
    </script>
</body>

</html>