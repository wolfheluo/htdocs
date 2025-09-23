<?php
// 處理 AJAX 請求
if (isset($_GET['action']) && $_GET['action'] === 'fetch_messages') {
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Headers: Content-Type');
    
    try {
        $url = 'https://16888gk.com/line-api-wolf/static/messages.json';
        
        // 使用 cURL 獲取數據
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($data === false || !empty($error)) {
            throw new Exception('cURL Error: ' . $error);
        }
        
        if ($httpCode !== 200) {
            throw new Exception('HTTP Error: ' . $httpCode);
        }
        
        // 驗證 JSON 格式
        $jsonData = json_decode($data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON: ' . json_last_error_msg());
        }
        
        // 直接輸出獲取到的 JSON 數據
        echo $data;
        exit;
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'error' => true,
            'message' => '獲取訊息失敗: ' . $e->getMessage()
        ]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文字環繞展示</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f8f8f8;
            font-family: 'Microsoft JhengHei', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            overflow: hidden;
            position: relative;
        }

        /* 簡約線條背景 */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(90deg, rgba(0,0,0,0.02) 1px, transparent 1px),
                linear-gradient(rgba(0,0,0,0.02) 1px, transparent 1px),
                linear-gradient(45deg, transparent 35%, rgba(0,0,0,0.01) 35%, rgba(0,0,0,0.01) 65%, transparent 65%);
            background-size: 60px 60px, 60px 60px, 120px 120px;
            z-index: -1;
        }

        .container {
            width: 100vw;
            height: 100vh;
            position: relative;
            overflow: hidden;
        }

        /* 彈幕樣式 */
        .danmaku-message {
            position: absolute;
            background: rgba(255, 255, 255, 0.95);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 16px;
            color: #333;
            max-width: 400px;
            word-wrap: break-word;
            word-break: break-word;
            line-height: 1.4;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            animation: danmaku-scroll linear;
            opacity: 0;
            transition: all 0.3s ease;
            cursor: pointer;
            z-index: 10;
        }

        .danmaku-message.visible {
            opacity: 0.9;
        }

        .danmaku-message:hover {
            opacity: 1 !important;
            transform: scale(1.05);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            background: rgba(255, 255, 255, 1);
            z-index: 100;
        }

        /* 彈幕動畫 */
        @keyframes danmaku-scroll {
            0% {
                transform: translateX(100vw);
            }
            100% {
                transform: translateX(-100%);
            }
        }

        .loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #666;
            font-size: 20px;
            text-align: center;
        }

        .spinner {
            border: 3px solid rgba(0, 0, 0, 0.1);
            border-top: 3px solid #666;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .error {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #999;
            font-size: 16px;
            text-align: center;
            background: rgba(255, 255, 255, 0.9);
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* 彈幕軌道定義 - 調整間距適應多行顯示 */
        .track-1 { top: 5%; }
        .track-2 { top: 18%; }
        .track-3 { top: 31%; }
        .track-4 { top: 44%; }
        .track-5 { top: 57%; }
        .track-6 { top: 70%; }
        .track-7 { top: 83%; }
        .track-8 { top: 96%; transform: translateY(-100%); } /* 最後一個軌道向上偏移 */

        /* 不同速度 */
        .speed-slow { animation-duration: 20s; }
        .speed-normal { animation-duration: 12s; }
        .speed-fast { animation-duration: 8s; }

        /* 不同顏色主題 */
        .theme-blue { 
            background: rgba(59, 130, 246, 0.1); 
            border-color: rgba(59, 130, 246, 0.2);
            color: #1e40af;
        }
        .theme-green { 
            background: rgba(34, 197, 94, 0.1); 
            border-color: rgba(34, 197, 94, 0.2);
            color: #15803d;
        }
        .theme-purple { 
            background: rgba(168, 85, 247, 0.1); 
            border-color: rgba(168, 85, 247, 0.2);
            color: #7c3aed;
        }
        .theme-pink { 
            background: rgba(236, 72, 153, 0.1); 
            border-color: rgba(236, 72, 153, 0.2);
            color: #be185d;
        }

        /* 點擊波紋效果 */
        @keyframes ripple {
            0% {
                transform: scale(0);
                opacity: 0.8;
            }
            100% {
                transform: scale(4);
                opacity: 0;
            }
        }

        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.1);
            animation: ripple 0.6s linear;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="container" id="container">
        <div class="loading" id="loading">
            <div class="spinner"></div>
            載入訊息中...
        </div>
        <div class="error" id="error" style="display: none;">
            載入訊息失敗，請稍後重試
        </div>
    </div>

    <script>
        class DanmakuDisplay {
            constructor() {
                this.messages = [];
                this.container = document.getElementById('container');
                this.loading = null;
                this.error = null;
                this.displayInterval = null;
                this.tracks = Array(8).fill(false); // 8個軌道的占用狀態
                
                this.createUI();
                this.init();
            }

            createUI() {
                // 創建載入提示
                this.loading = document.createElement('div');
                this.loading.className = 'loading';
                this.loading.innerHTML = `
                    <div class="spinner"></div>
                    載入訊息中...
                `;
                this.container.appendChild(this.loading);

                // 創建錯誤提示
                this.error = document.createElement('div');
                this.error.className = 'error';
                this.error.style.display = 'none';
                this.error.textContent = '載入訊息失敗，點擊重試';
                this.container.appendChild(this.error);

                // 錯誤點擊重試
                this.error.addEventListener('click', () => {
                    this.reload();
                });
            }

            async init() {
                try {
                    await this.fetchMessages();
                    this.startDisplay();
                } catch (error) {
                    console.error('初始化失敗:', error);
                    this.showError();
                }
            }

            async fetchMessages() {
                try {
                    console.log('開始獲取訊息...');
                    const response = await fetch('?action=fetch_messages');
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();
                    
                    // 檢查是否有錯誤回應
                    if (data.error) {
                        throw new Error(data.message || '未知錯誤');
                    }
                    
                    // 只提取 message 字段，並過濾空訊息
                    this.messages = data
                        .filter(item => item.message && item.message.trim() !== '')
                        .map(item => item.message.trim());
                    
                    if (this.messages.length === 0) {
                        throw new Error('沒有找到有效的訊息');
                    }
                    
                    console.log(`成功載入 ${this.messages.length} 條訊息`);
                } catch (error) {
                    console.error('獲取訊息失敗:', error);
                    throw error;
                }
            }

            startDisplay() {
                console.log('開始顯示彈幕...');
                
                // 確保載入提示被完全移除 - 直接從container中查找並移除
                const loadingElements = this.container.querySelectorAll('.loading');
                loadingElements.forEach(element => {
                    element.remove();
                });
                this.loading = null;
                
                // 立即顯示第一條彈幕
                this.createDanmaku();
                
                // 設定定時器，每隔 1-3 秒隨機顯示新彈幕
                this.displayInterval = setInterval(() => {
                    this.createDanmaku();
                }, this.getRandomInterval(1000, 3000));
            }

            createDanmaku() {
                if (this.messages.length === 0) return;

                // 額外安全檢查：確保沒有殘留的載入元素
                const loadingElements = this.container.querySelectorAll('.loading');
                if (loadingElements.length > 0) {
                    loadingElements.forEach(element => element.remove());
                }

                const message = this.getRandomMessage();
                const danmaku = document.createElement('div');
                const track = this.getAvailableTrack();
                
                if (track === -1) return; // 沒有可用軌道

                danmaku.className = this.getDanmakuClasses(track);
                
                // 處理換行符和其他轉義字符
                const processedMessage = this.processMessageContent(message);
                danmaku.innerHTML = processedMessage;

                // 標記軌道為占用
                this.tracks[track] = true;

                this.container.appendChild(danmaku);

                // 顯示動畫
                setTimeout(() => {
                    danmaku.classList.add('visible');
                }, 100);

                // 清理和釋放軌道
                const duration = this.getAnimationDuration(danmaku);
                setTimeout(() => {
                    this.tracks[track] = false; // 釋放軌道
                }, duration * 0.3); // 30% 動畫完成後就可以釋放軌道

                setTimeout(() => {
                    if (danmaku && danmaku.parentNode) {
                        danmaku.style.opacity = '0';
                        setTimeout(() => {
                            if (danmaku && danmaku.parentNode) {
                                danmaku.parentNode.removeChild(danmaku);
                            }
                        }, 300);
                    }
                }, duration);
            }

            getAvailableTrack() {
                // 找到第一個可用的軌道
                for (let i = 0; i < this.tracks.length; i++) {
                    if (!this.tracks[i]) {
                        return i;
                    }
                }
                return -1; // 沒有可用軌道
            }

            getDanmakuClasses(track) {
                const speeds = ['speed-slow', 'speed-normal', 'speed-fast'];
                const themes = ['theme-blue', 'theme-green', 'theme-purple', 'theme-pink', ''];

                const speedClass = speeds[Math.floor(Math.random() * speeds.length)];
                const themeClass = themes[Math.floor(Math.random() * themes.length)];
                const trackClass = `track-${track + 1}`;

                return `danmaku-message ${trackClass} ${speedClass} ${themeClass}`.trim();
            }

            getAnimationDuration(element) {
                if (element.classList.contains('speed-slow')) return 20000;
                if (element.classList.contains('speed-fast')) return 8000;
                return 12000; // normal speed
            }

            getRandomMessage() {
                return this.messages[Math.floor(Math.random() * this.messages.length)];
            }

            processMessageContent(message) {
                // 首先進行HTML轉義，防止XSS攻擊
                const escapeHtml = (text) => {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                };
                
                let processedMessage = escapeHtml(message);
                
                // 處理各種換行符和轉義字符
                processedMessage = processedMessage
                    .replace(/\\n/g, '<br>')           // 處理 \n
                    .replace(/\\r\\n/g, '<br>')       // 處理 \r\n
                    .replace(/\\r/g, '<br>')          // 處理 \r
                    .replace(/\n/g, '<br>')           // 處理實際的換行符
                    .replace(/\r\n/g, '<br>')         // 處理實際的 \r\n
                    .replace(/\r/g, '<br>')           // 處理實際的 \r
                    .replace(/\\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;') // 處理 \t (轉為4個空格)
                    .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;');   // 處理實際的tab
                
                return processedMessage;
            }

            getRandomInterval(min, max) {
                return Math.floor(Math.random() * (max - min + 1)) + min;
            }

            showError() {
                // 確保載入提示被完全移除 - 直接從container中查找並移除
                const loadingElements = this.container.querySelectorAll('.loading');
                loadingElements.forEach(element => {
                    element.remove();
                });
                this.loading = null;
                
                this.error.style.display = 'block';
            }

            async reload() {
                if (this.displayInterval) {
                    clearInterval(this.displayInterval);
                }
                
                // 清除現有彈幕
                const existingDanmaku = this.container.querySelectorAll('.danmaku-message');
                existingDanmaku.forEach(d => d.remove());
                
                // 重置軌道狀態
                this.tracks.fill(false);
                
                // 重新創建載入提示（如果需要）
                if (!this.loading) {
                    this.loading = document.createElement('div');
                    this.loading.className = 'loading';
                    this.loading.innerHTML = `
                        <div class="spinner"></div>
                        載入訊息中...
                    `;
                    this.container.appendChild(this.loading);
                }
                
                this.error.style.display = 'none';
                
                await this.init();
            }
        }

        // DOM載入完成後初始化
        document.addEventListener('DOMContentLoaded', function() {
            const danmakuDisplay = new DanmakuDisplay();
            
            // 鍵盤快捷鍵
            document.addEventListener('keydown', (e) => {
                if (e.key === 'r' || e.key === 'R') {
                    danmakuDisplay.reload();
                }
                if (e.key === ' ') { // 空格鍵暫停/恢復
                    e.preventDefault();
                    const danmakus = document.querySelectorAll('.danmaku-message');
                    danmakus.forEach(d => {
                        if (d.style.animationPlayState === 'paused') {
                            d.style.animationPlayState = 'running';
                        } else {
                            d.style.animationPlayState = 'paused';
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>