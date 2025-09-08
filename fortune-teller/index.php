<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ดูดวงออนไลน์ - Fortune Teller</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }

        .controls {
            padding: 30px;
            text-align: center;
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }

        .language-toggle {
            display: inline-flex;
            background: #fff;
            border-radius: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .language-btn {
            padding: 12px 24px;
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
            min-width: 80px;
        }

        .language-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
        }

        .language-btn:hover:not(.active) {
            background: #f1f3f4;
        }

        .fortune-btn {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(238, 90, 36, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .fortune-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(238, 90, 36, 0.4);
        }

        .fortune-btn:active {
            transform: translateY(0);
        }

        .fortune-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .fortune-display {
            padding: 40px;
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .fortune-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(245, 87, 108, 0.3);
            text-align: center;
            width: 100%;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease;
        }

        .fortune-card.show {
            opacity: 1;
            transform: translateY(0);
        }

        .fortune-text {
            font-size: 1.3em;
            line-height: 1.6;
            margin-bottom: 20px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }

        .fortune-id {
            font-size: 0.9em;
            opacity: 0.8;
            font-weight: 300;
        }

        .placeholder {
            color: #666;
            font-size: 1.2em;
            text-align: center;
        }

        .loading {
            display: none;
            color: #667eea;
            font-size: 1.1em;
            text-align: center;
        }

        .loading::after {
            content: '';
            animation: dots 1.5s infinite;
        }

        @keyframes dots {
            0%, 20% { content: ''; }
            40% { content: '.'; }
            60% { content: '..'; }
            80%, 100% { content: '...'; }
        }

        .fortune-categories {
            background: #e3f2fd;
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;
            border-left: 4px solid #2196f3;
        }

        .categories-title {
            font-weight: bold;
            color: #1976d2;
            margin-bottom: 10px;
            font-size: 1.1em;
        }

        .categories-list {
            color: #424242;
            line-height: 1.5;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2em;
            }
            
            .controls {
                padding: 20px;
            }
            
            .fortune-display {
                padding: 20px;
            }
            
            .fortune-text {
                font-size: 1.1em;
            }
            
            .language-btn {
                padding: 10px 16px;
                font-size: 14px;
                min-width: 60px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔮 ดูดวงออนไลน์</h1>
            <p>คำพยากรณ์ 52 บท ครอบคลุมทุกเรื่องในชีวิต</p>
        </div>

        <div class="controls">
            <div class="language-toggle">
                <button class="language-btn active" data-lang="thai">ไทย</button>
                <button class="language-btn" data-lang="chinese">中文</button>
                <button class="language-btn" data-lang="english">English</button>
            </div>
            <br>
            <button class="fortune-btn" onclick="getFortune()">ขอคำพยากรณ์</button>
            
            <div class="fortune-categories">
                <div class="categories-title">คำพยากรณ์ครอบคลุม 5 เรื่องหลัก:</div>
                <div class="categories-list">
                    💼 ทั่วไป • 💰 เงินทอง • 🎯 การงาน • 💕 ความรัก • 🏥 สุขภาพ
                </div>
            </div>
        </div>

        <div class="fortune-display">
            <div class="placeholder">
                กดปุ่ม "ขอคำพยากรณ์" เพื่อดูดวงของคุณ<br>
                <small style="color: #999; margin-top: 10px; display: block;">
                    คำพยากรณ์จะแสดงในภาษาที่เลือก
                </small>
            </div>
            <div class="loading">กำลังดูดวง</div>
        </div>
    </div>

    <script>
        let currentLanguage = 'thai';
        let currentFortune = null;

        // Language toggle functionality
        document.querySelectorAll('.language-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('.language-btn').forEach(b => b.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Update current language
                currentLanguage = this.getAttribute('data-lang');
                
                // Update button text based on language
                updateButtonText();
                
                // Clear previous fortune display and show placeholder
                clearFortuneDisplay();
                
                // If there's a current fortune, update the display with new language
                if (currentFortune) {
                    displayFortune(currentFortune);
                }
            });
        });

        function updateButtonText() {
            const fortuneBtn = document.querySelector('.fortune-btn');
            const placeholder = document.querySelector('.placeholder');
            
            switch(currentLanguage) {
                case 'thai':
                    fortuneBtn.textContent = 'ขอคำพยากรณ์';
                    if (!currentFortune) {
                        placeholder.innerHTML = `
                            กดปุ่ม "ขอคำพยากรณ์" เพื่อดูดวงของคุณ<br>
                            <small style="color: #999; margin-top: 10px; display: block;">
                                คำพยากรณ์จะแสดงในภาษาที่เลือก
                            </small>
                        `;
                    }
                    break;
                case 'chinese':
                    fortuneBtn.textContent = '求签';
                    if (!currentFortune) {
                        placeholder.innerHTML = `
                            点击"求签"按钮查看您的运势<br>
                            <small style="color: #999; margin-top: 10px; display: block;">
                                预测将以所选语言显示
                            </small>
                        `;
                    }
                    break;
                case 'english':
                    fortuneBtn.textContent = 'Get Fortune';
                    if (!currentFortune) {
                        placeholder.innerHTML = `
                            Click "Get Fortune" button to see your prediction<br>
                            <small style="color: #999; margin-top: 10px; display: block;">
                                Fortune will be displayed in selected language
                            </small>
                        `;
                    }
                    break;
            }
        }

        function clearFortuneDisplay() {
            const existingCard = document.querySelector('.fortune-card');
            const placeholder = document.querySelector('.placeholder');
            const loading = document.querySelector('.loading');
            
            // Hide existing elements
            loading.style.display = 'none';
            
            // Remove existing fortune card if any
            if (existingCard) {
                existingCard.classList.remove('show');
                setTimeout(() => existingCard.remove(), 300);
            }
            
            // Show placeholder with updated text
            placeholder.style.display = 'block';
            updateButtonText();
        }

        async function getFortune() {
            const fortuneBtn = document.querySelector('.fortune-btn');
            const placeholder = document.querySelector('.placeholder');
            const loading = document.querySelector('.loading');
            const existingCard = document.querySelector('.fortune-card');

            // Disable button and show loading
            fortuneBtn.disabled = true;
            placeholder.style.display = 'none';
            loading.style.display = 'block';
            
            // Hide existing card if any
            if (existingCard) {
                existingCard.classList.remove('show');
                setTimeout(() => existingCard.remove(), 300);
            }

            try {
                const response = await fetch('api/');
                const data = await response.json();

                if (data.success) {
                    currentFortune = data.fortune;
                    setTimeout(() => {
                        loading.style.display = 'none';
                        displayFortune(currentFortune);
                    }, 1000); // Show loading for 1 second for better UX
                } else {
                    throw new Error('Failed to get fortune');
                }
            } catch (error) {
                console.error('Error:', error);
                loading.style.display = 'none';
                placeholder.innerHTML = '<span style="color: #e74c3c;">เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง</span>';
                placeholder.style.display = 'block';
            } finally {
                fortuneBtn.disabled = false;
            }
        }

        function displayFortune(fortune) {
            const fortuneDisplay = document.querySelector('.fortune-display');
            
            // Remove any existing cards
            const existingCard = document.querySelector('.fortune-card');
            if (existingCard) {
                existingCard.remove();
            }
            
            // Create fortune card
            const fortuneCard = document.createElement('div');
            fortuneCard.className = 'fortune-card';
            
            // Get text for selected language only
            const fortuneText = fortune[currentLanguage] || fortune.thai;
            
            // Create fortune ID text based on language
            let fortuneIdText = '';
            switch(currentLanguage) {
                case 'thai':
                    fortuneIdText = `คำพยากรณ์ที่ ${fortune.id} จาก 52 บท`;
                    break;
                case 'chinese':
                    fortuneIdText = `第 ${fortune.id} 签，共 52 签`;
                    break;
                case 'english':
                    fortuneIdText = `Fortune ${fortune.id} of 52`;
                    break;
            }
            
            fortuneCard.innerHTML = `
                <div class="fortune-text">${fortuneText}</div>
                <div class="fortune-id">${fortuneIdText}</div>
            `;
            
            fortuneDisplay.appendChild(fortuneCard);
            
            // Trigger animation
            setTimeout(() => {
                fortuneCard.classList.add('show');
            }, 100);
        }

        // Initialize
        updateButtonText();
    </script>
</body>
</html>
