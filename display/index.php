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
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            font-family: 'Microsoft JhengHei', sans-serif;
            height: 100vh;
            overflow: hidden;
            position: relative;
        }

        .container {
            width: 100vw;
            height: 100vh;
            position: relative;
            overflow: hidden;
        }

        .floating-message {
            position: absolute;
            background: rgba(255, 255, 255, 0.9);
            padding: 15px 25px;
            border-radius: 25px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 16px;
            color: #333;
            max-width: 300px;
            word-wrap: break-word;
            animation: float-around 15s infinite linear;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        .floating-message.visible {
            opacity: 1;
        }

        .floating-message::before {
            content: '';
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4, #feca57);
            border-radius: 30px;
            z-index: -1;
            filter: blur(5px);
            opacity: 0.7;
        }

        @keyframes float-around {
            0% {
                transform: translateX(-100px) translateY(50vh) rotate(0deg);
            }
            25% {
                transform: translateX(25vw) translateY(20vh) rotate(90deg);
            }
            50% {
                transform: translateX(75vw) translateY(70vh) rotate(180deg);
            }
            75% {
                transform: translateX(50vw) translateY(10vh) rotate(270deg);
            }
            100% {
                transform: translateX(100vw) translateY(50vh) rotate(360deg);
            }
        }

        .loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 24px;
            text-align: center;
        }

        .spinner {
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
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
            color: #ff6b6b;
            font-size: 18px;
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }

        /* 不同大小的訊息氣泡 */
        .size-small { font-size: 14px; max-width: 200px; }
        .size-medium { font-size: 16px; max-width: 300px; }
        .size-large { font-size: 18px; max-width: 400px; }

        /* 不同速度的動畫 */
        .speed-slow { animation-duration: 25s; }
        .speed-normal { animation-duration: 15s; }
        .speed-fast { animation-duration: 10s; }

        /* 不同方向的動畫 */
        @keyframes float-reverse {
            0% {
                transform: translateX(100vw) translateY(30vh) rotate(0deg);
            }
            25% {
                transform: translateX(75vw) translateY(80vh) rotate(-90deg);
            }
            50% {
                transform: translateX(25vw) translateY(20vh) rotate(-180deg);
            }
            75% {
                transform: translateX(50vw) translateY(90vh) rotate(-270deg);
            }
            100% {
                transform: translateX(-100px) translateY(30vh) rotate(-360deg);
            }
        }

        .direction-reverse {
            animation-name: float-reverse;
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
        class MessageDisplay {
            constructor() {
                this.messages = [];
                this.container = document.getElementById('container');
                this.loading = document.getElementById('loading');
                this.error = document.getElementById('error');
                this.displayInterval = null;
                this.init();
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
                    const response = await fetch('https://16888gk.com/line-api-wolf/static/messages.json');
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();
                    
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
                this.loading.style.display = 'none';
                
                // 立即顯示第一條訊息
                this.createFloatingMessage();
                
                // 設定定時器，每隔 2-5 秒隨機顯示新訊息
                this.displayInterval = setInterval(() => {
                    this.createFloatingMessage();
                }, this.getRandomInterval(2000, 5000));
            }

            createFloatingMessage() {
                if (this.messages.length === 0) return;

                const message = this.getRandomMessage();
                const messageElement = document.createElement('div');
                messageElement.className = this.getRandomClasses();
                messageElement.textContent = message;

                // 設定隨機起始位置
                const startPositions = [
                    { left: '-200px', top: Math.random() * 80 + '%' }, // 從左邊進入
                    { left: '100vw', top: Math.random() * 80 + '%' },   // 從右邊進入
                    { left: Math.random() * 80 + '%', top: '-100px' }, // 從上方進入
                    { left: Math.random() * 80 + '%', top: '100vh' }   // 從下方進入
                ];
                
                const startPos = startPositions[Math.floor(Math.random() * startPositions.length)];
                messageElement.style.left = startPos.left;
                messageElement.style.top = startPos.top;

                this.container.appendChild(messageElement);

                // 顯示動畫
                setTimeout(() => {
                    messageElement.classList.add('visible');
                }, 100);

                // 清理舊訊息（避免 DOM 元素過多）
                setTimeout(() => {
                    if (messageElement && messageElement.parentNode) {
                        messageElement.style.opacity = '0';
                        setTimeout(() => {
                            if (messageElement && messageElement.parentNode) {
                                messageElement.parentNode.removeChild(messageElement);
                            }
                        }, 500);
                    }
                }, this.getAnimationDuration(messageElement) + 1000);
            }

            getRandomMessage() {
                return this.messages[Math.floor(Math.random() * this.messages.length)];
            }

            getRandomClasses() {
                const sizes = ['size-small', 'size-medium', 'size-large'];
                const speeds = ['speed-slow', 'speed-normal', 'speed-fast'];
                const directions = ['', 'direction-reverse'];

                const sizeClass = sizes[Math.floor(Math.random() * sizes.length)];
                const speedClass = speeds[Math.floor(Math.random() * speeds.length)];
                const directionClass = directions[Math.floor(Math.random() * directions.length)];

                return `floating-message ${sizeClass} ${speedClass} ${directionClass}`.trim();
            }

            getAnimationDuration(element) {
                if (element.classList.contains('speed-slow')) return 25000;
                if (element.classList.contains('speed-fast')) return 10000;
                return 15000; // normal speed
            }

            getRandomInterval(min, max) {
                return Math.floor(Math.random() * (max - min + 1)) + min;
            }

            showError() {
                this.loading.style.display = 'none';
                this.error.style.display = 'block';
            }

            // 重新載入訊息的方法
            async reload() {
                if (this.displayInterval) {
                    clearInterval(this.displayInterval);
                }
                
                // 清除現有訊息
                const existingMessages = this.container.querySelectorAll('.floating-message');
                existingMessages.forEach(msg => msg.remove());
                
                this.loading.style.display = 'block';
                this.error.style.display = 'none';
                
                await this.init();
            }
        }

        // 初始化
        const messageDisplay = new MessageDisplay();

        // 點擊頁面重新載入（可選功能）
        document.addEventListener('click', () => {
            if (document.getElementById('error').style.display !== 'none') {
                messageDisplay.reload();
            }
        });

        // 鍵盤快捷鍵重新載入
        document.addEventListener('keydown', (e) => {
            if (e.key === 'r' || e.key === 'R') {
                messageDisplay.reload();
            }
        });
    </script>
</body>
</html>