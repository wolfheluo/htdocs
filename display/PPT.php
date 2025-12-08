<?php
// ===== 設定常數 =====
define('API_URL', 'https://16888gk.com/line-api-wolf/static/messages.json');
define('BACKUP_FILE', 'messages_backup.json');
define('BG_FOLDER', 'BG');

// ===== 處理 AJAX 請求 =====
if (($_GET['action'] ?? '') === 'fetch_messages') {
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Headers: Content-Type');

    try {
        $ch = curl_init(API_URL);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]);

        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($data === false || $error) {
            throw new Exception("cURL Error: $error");
        }
        if ($httpCode !== 200) {
            throw new Exception("HTTP Error: $httpCode");
        }
        json_decode($data);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON: ' . json_last_error_msg());
        }

        file_put_contents(BACKUP_FILE, $data);
        echo $data;

    } catch (Exception $e) {
        if (file_exists(BACKUP_FILE) && ($backupData = file_get_contents(BACKUP_FILE)) !== false) {
            echo $backupData;
        } else {
            http_response_code(500);
            echo json_encode(['error' => true, 'message' => '獲取訊息失敗: ' . $e->getMessage()]);
        }
    }
    exit;
}

require_once 'counter.php';

// ===== 立繪偽隨機（使用 Session 追蹤） =====
$images = glob(BG_FOLDER . '/*.{png,jpg,jpeg,gif,webp}', GLOB_BRACE) ?: [];

// 初始化或重置佇列
if (!empty($images)) {
    if (empty($_SESSION['girl_queue'])) {
        $_SESSION['girl_queue'] = $images;
        shuffle($_SESSION['girl_queue']);
    }
    $randomGirl = array_pop($_SESSION['girl_queue']);
} else {
    $randomGirl = null;
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>愛不能只靠感動</title>

    <style>
        :root {
            --color-primary: #c742ff;
            --color-text: #f2d9ff;
            --color-bg-dark: #08030b;
            --color-bg-accent: #2a0a2f;
            --color-glow: rgba(255, 60, 180, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        body {
            background: radial-gradient(circle at top, var(--color-bg-accent) 0%, var(--color-bg-dark) 60%);
            color: var(--color-text);
            font-family: "Noto Sans TC", sans-serif;
        }

        .particles {
            position: fixed;
            inset: 0;
            z-index: 1;
            pointer-events: none;
            opacity: 0.5;
            will-change: transform;
        }

        .content {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 2;
            max-width: 700px;
            width: 90%;
            padding: 20px;
            text-align: center;
        }

        .sentence {
            font-size: clamp(18px, 4vw, 30px);
            line-height: 1.8;
            letter-spacing: 1.5px;
            opacity: 0;
            animation: reveal 1.8s ease forwards;
            text-shadow: 0 0 20px var(--color-glow);
            white-space: pre-line;
        }

        @keyframes reveal {
            from {
                opacity: 0;
                transform: translateY(25px) scale(0.98);
                filter: blur(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
                filter: blur(0);
            }
        }

        .refresh-btn {
            position: fixed;
            bottom: 8vh;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            padding: 12px 30px;
            font-size: 16px;
            color: var(--color-text);
            background: rgba(199, 66, 255, 0.2);
            border: 1px solid rgba(199, 66, 255, 0.5);
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .refresh-btn:hover {
            background: rgba(199, 66, 255, 0.4);
            box-shadow: 0 0 20px rgba(199, 66, 255, 0.5);
            transform: translateX(-50%) scale(1.05);
        }

        .girl {
            position: fixed;
            right: 20px;
            bottom: 0;
            width: clamp(180px, 25vw, 360px);
            z-index: 3;
            opacity: 0.9;
            filter: drop-shadow(0 0 25px var(--color-primary));
        }

        .loading {
            font-size: 20px;
            opacity: 0.6;
        }
    </style>
</head>

<body>
    <canvas class="particles"></canvas>

    <div class="content">
        <div class="sentence" id="sentence">
            <span class="loading">載入中...</span>
        </div>
    </div>

    <button class="refresh-btn" id="refreshBtn">換一句</button>
    <?php if ($randomGirl): ?>
        <img src="<?= htmlspecialchars($randomGirl) ?>" class="girl" loading="lazy" alt="暗魅少女">
    <?php endif; ?>

    <script>
        (function () {
            'use strict';

            // ===== 快取 DOM 元素 =====
            const canvas = document.querySelector('.particles');
            const ctx = canvas.getContext('2d');
            const sentenceEl = document.getElementById('sentence');
            const refreshBtn = document.getElementById('refreshBtn');

            // ===== 粒子系統 =====
            const PARTICLE_COUNT = 80;
            const particles = [];

            const resizeCanvas = () => {
                canvas.width = innerWidth;
                canvas.height = innerHeight;
            };

            const createParticle = () => ({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                size: Math.random() * 2.2 + 0.8,
                speed: Math.random() * 0.4 + 0.1,
                color: `rgba(${150 + Math.random() * 80},0,${120 + Math.random() * 80},0.5)`
            });

            const initParticles = () => {
                for (let i = 0; i < PARTICLE_COUNT; i++) {
                    particles.push(createParticle());
                }
            };

            const renderParticles = () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                for (const p of particles) {
                    ctx.beginPath();
                    ctx.fillStyle = p.color;
                    ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
                    ctx.fill();

                    p.y -= p.speed;
                    if (p.y < 0) p.y = canvas.height;
                }

                requestAnimationFrame(renderParticles);
            };

            // ===== 句子系統（偽隨機 + 自動換句） =====
            let messagesData = [];
            let shuffledQueue = [];
            let autoSwitchTimer = null;
            const AUTO_SWITCH_INTERVAL = 30000; // 30 秒

            // Fisher-Yates 洗牌演算法
            const shuffleArray = (array) => {
                const shuffled = [...array];
                for (let i = shuffled.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
                }
                return shuffled;
            };

            const fetchMessages = async () => {
                try {
                    const response = await fetch('?action=fetch_messages');
                    const data = await response.json();

                    messagesData = data.filter(item =>
                        item.message?.trim() && item.message_type === 'text'
                    );

                    // 初始化洗牌佇列
                    shuffledQueue = shuffleArray(messagesData);
                    showNextSentence();
                    startAutoSwitch();
                } catch (error) {
                    sentenceEl.textContent = '無法載入句子...';
                    console.error('Error fetching messages:', error);
                }
            };

            const showNextSentence = () => {
                if (!messagesData.length) return;

                // 佇列空了就重新洗牌
                if (shuffledQueue.length === 0) {
                    shuffledQueue = shuffleArray(messagesData);
                }

                // 從佇列取出下一句
                const { message } = shuffledQueue.pop();

                // 重新觸發動畫
                sentenceEl.style.animation = 'none';
                sentenceEl.offsetHeight; // 強制重繪
                sentenceEl.style.animation = '';

                sentenceEl.textContent = `「${message}」`;
            };

            // 自動換句功能
            const startAutoSwitch = () => {
                if (autoSwitchTimer) clearInterval(autoSwitchTimer);
                autoSwitchTimer = setInterval(showNextSentence, AUTO_SWITCH_INTERVAL);
            };

            // 手動換句時重置計時器
            const handleManualSwitch = () => {
                showNextSentence();
                startAutoSwitch(); // 重置 30 秒計時
            };

            // ===== 初始化 =====
            resizeCanvas();
            let resizeTimeout;
            addEventListener('resize', () => {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(resizeCanvas, 100);
            });
            initParticles();
            renderParticles();
            fetchMessages();

            // 綁定按鈕事件
            refreshBtn.addEventListener('click', handleManualSwitch);
        })();
    </script>

</body>

</html>