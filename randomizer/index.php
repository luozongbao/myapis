<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Random Generator</title>
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
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            align-items: start;
        }

        .control-section, .results-section {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            grid-column: 1 / -1;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 40px;
        }

        .header h1 {
            color: #333;
            font-size: 2.5em;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header p {
            color: #666;
            font-size: 1.1em;
            line-height: 1.6;
        }

        .type-selector {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .type-button {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border: 2px solid transparent;
            border-radius: 15px;
            padding: 20px 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            color: #333;
            position: relative;
            overflow: hidden;
        }

        .type-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            transition: left 0.3s ease;
            z-index: -1;
        }

        .type-button.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .type-button.active::before {
            left: 0;
        }

        .type-button:hover:not(.active) {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .type-icon {
            font-size: 2em;
            display: block;
            margin-bottom: 10px;
        }

        .controls-container {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .controls-container.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .control-group {
            margin-bottom: 20px;
        }

        .control-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .control-group input,
        .control-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1em;
            transition: all 0.3s ease;
            background: white;
        }

        .control-group input:focus,
        .control-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin: 0;
        }

        .generate-btn {
            width: 100%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 15px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .generate-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .generate-btn:active {
            transform: translateY(0);
        }

        .generate-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .loading {
            position: relative;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .result-container {
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .result-display {
            text-align: center;
            padding: 40px;
            border-radius: 15px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin-bottom: 20px;
            width: 100%;
            min-height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        .result-value {
            font-size: 3em;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
            animation: slideIn 0.5s ease;
        }

        .result-info {
            color: #666;
            font-size: 1.1em;
            margin-top: 10px;
        }

        @keyframes slideIn {
            from { 
                opacity: 0; 
                transform: translateY(30px) scale(0.8); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0) scale(1); 
            }
        }

        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% { transform: translateY(0); }
            40%, 43% { transform: translateY(-10px); }
            70% { transform: translateY(-5px); }
        }

        @keyframes flip {
            0% { transform: rotateY(0); }
            50% { transform: rotateY(180deg); }
            100% { transform: rotateY(360deg); }
        }

        @keyframes roll {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes shuffle {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px) rotate(-5deg); }
            75% { transform: translateX(10px) rotate(5deg); }
        }

        .animate-bounce {
            animation: bounce 0.8s ease;
        }

        .animate-flip {
            animation: flip 1s ease;
        }

        .animate-roll {
            animation: roll 1s ease;
        }

        .animate-shuffle {
            animation: shuffle 0.8s ease;
        }

        .card-display {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .card-visual {
            width: 120px;
            height: 168px;
            background: white;
            border: 2px solid #333;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 10px;
            position: relative;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .card-visual.red {
            color: #d32f2f;
        }

        .card-visual.black {
            color: #333;
        }

        .card-rank {
            font-size: 1.2em;
            font-weight: bold;
        }

        .card-suit {
            font-size: 2em;
            text-align: center;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .dice-container {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .dice-visual {
            width: 60px;
            height: 60px;
            background: white;
            border: 2px solid #333;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5em;
            font-weight: bold;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .coin-visual {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ffd700, #ffed4e);
            border: 3px solid #333;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2em;
            font-weight: bold;
            color: #333;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .placeholder {
            color: #999;
            font-style: italic;
        }

        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            border-left: 4px solid #c62828;
        }

        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .type-selector {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .header h1 {
                font-size: 2em;
            }
            
            .result-value {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎲 Random Generator</h1>
            <p>Generate random numbers, roll dice, flip coins, and draw cards with beautiful animations</p>
        </div>

        <div class="control-section">
            <h2 style="margin-bottom: 20px; color: #333;">Choose Generator Type</h2>
            
            <div class="type-selector">
                <div class="type-button active" data-type="number">
                    <span class="type-icon">🔢</span>
                    Number
                </div>
                <div class="type-button" data-type="dice">
                    <span class="type-icon">🎲</span>
                    Dice
                </div>
                <div class="type-button" data-type="coin">
                    <span class="type-icon">🪙</span>
                    Coin
                </div>
                <div class="type-button" data-type="card">
                    <span class="type-icon">🃏</span>
                    Card
                </div>
            </div>

            <!-- Number Generator Controls -->
            <div id="number-controls" class="controls-container active">
                <div class="control-group">
                    <label for="min-number">Minimum Value:</label>
                    <input type="number" id="min-number" value="1" min="-2147483648" max="2147483647">
                </div>
                <div class="control-group">
                    <label for="max-number">Maximum Value:</label>
                    <input type="number" id="max-number" value="100" min="-2147483648" max="2147483647">
                </div>
            </div>

            <!-- Dice Controls -->
            <div id="dice-controls" class="controls-container">
                <div class="control-group">
                    <label for="dice-sides">Number of Sides:</label>
                    <select id="dice-sides">
                        <option value="4">4-sided (D4)</option>
                        <option value="6" selected>6-sided (D6)</option>
                        <option value="8">8-sided (D8)</option>
                        <option value="10">10-sided (D10)</option>
                        <option value="12">12-sided (D12)</option>
                        <option value="20">20-sided (D20)</option>
                        <option value="100">100-sided (D100)</option>
                    </select>
                </div>
                <div class="control-group">
                    <label for="dice-count">Number of Dice:</label>
                    <input type="number" id="dice-count" value="1" min="1" max="10">
                </div>
            </div>

            <!-- Coin Controls -->
            <div id="coin-controls" class="controls-container">
                <div class="control-group">
                    <label for="coin-count">Number of Coins:</label>
                    <input type="number" id="coin-count" value="1" min="1" max="10">
                </div>
            </div>

            <!-- Card Controls -->
            <div id="card-controls" class="controls-container">
                <div class="control-group">
                    <label for="card-count">Number of Cards:</label>
                    <input type="number" id="card-count" value="1" min="1" max="52">
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" id="include-jokers" aria-describedby="include-jokers-label">
                    <label for="include-jokers" id="include-jokers-label">Include Jokers</label>
                </div>
            </div>

            <button class="generate-btn" onclick="generate()">
                <span class="btn-text">Generate Random</span>
            </button>
        </div>

        <div class="results-section">
            <h2 style="margin-bottom: 20px; color: #333;">Result</h2>
            <div class="result-container">
                <div class="result-display">
                    <div class="placeholder">Click "Generate Random" to start!</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentType = 'number';
        let isGenerating = false;

        // Type selector event listeners
        document.querySelectorAll('.type-button').forEach(button => {
            button.addEventListener('click', () => {
                // Update active button
                document.querySelectorAll('.type-button').forEach(b => b.classList.remove('active'));
                button.classList.add('active');
                
                // Update current type
                currentType = button.dataset.type;
                
                // Show/hide controls
                document.querySelectorAll('.controls-container').forEach(container => {
                    container.classList.remove('active');
                });
                document.getElementById(`${currentType}-controls`).classList.add('active');
                
                // Update button text
                updateButtonText();
            });
        });

        function updateButtonText() {
            const btnText = document.querySelector('.btn-text');
            const typeNames = {
                'number': 'Generate Number',
                'dice': 'Roll Dice',
                'coin': 'Flip Coin',
                'card': 'Draw Card'
            };
            btnText.textContent = typeNames[currentType] || 'Generate Random';
        }

        async function generate() {
            if (isGenerating) return;
            
            isGenerating = true;
            const button = document.querySelector('.generate-btn');
            const resultDisplay = document.querySelector('.result-display');
            
            // Show loading state
            button.classList.add('loading');
            button.disabled = true;
            resultDisplay.innerHTML = '<div class="placeholder">Generating...</div>';
            
            try {
                // Prepare API request
                const params = new URLSearchParams();
                params.append('type', currentType);
                
                // Add type-specific parameters
                switch (currentType) {
                    case 'number':
                        params.append('min', document.getElementById('min-number').value);
                        params.append('max', document.getElementById('max-number').value);
                        break;
                    case 'dice':
                        params.append('sides', document.getElementById('dice-sides').value);
                        params.append('count', document.getElementById('dice-count').value);
                        break;
                    case 'coin':
                        params.append('count', document.getElementById('coin-count').value);
                        break;
                    case 'card':
                        params.append('count', document.getElementById('card-count').value);
                        params.append('with_jokers', document.getElementById('include-jokers').checked);
                        break;
                }
                
                // Make API call
                const response = await fetch(`api/index.php?${params.toString()}`);
                const data = await response.json();
                
                if (data.success) {
                    displayResult(data);
                } else {
                    throw new Error(data.error || 'Generation failed');
                }
                
            } catch (error) {
                console.error('Error:', error);
                resultDisplay.innerHTML = `
                    <div class="error-message">
                        <strong>Error:</strong> ${error.message}
                    </div>
                `;
            } finally {
                // Reset loading state
                button.classList.remove('loading');
                button.disabled = false;
                isGenerating = false;
            }
        }

        function displayResult(data) {
            const resultDisplay = document.querySelector('.result-display');
            
            switch (data.type) {
                case 'number':
                    resultDisplay.innerHTML = `
                        <div class="result-value animate-bounce">${data.result}</div>
                        <div class="result-info">Random number between ${data.range.min} and ${data.range.max}</div>
                    `;
                    break;
                    
                case 'dice':
                    const diceHtml = Array.isArray(data.result) 
                        ? data.result.map(roll => `<div class="dice-visual animate-roll">${roll}</div>`).join('')
                        : `<div class="dice-visual animate-roll">${data.result}</div>`;
                    
                    resultDisplay.innerHTML = `
                        <div class="dice-container">${diceHtml}</div>
                        <div class="result-info">
                            ${data.dice_config.count} × D${data.dice_config.sides}
                            ${Array.isArray(data.result) ? ` | Total: ${data.total}` : ''}
                        </div>
                    `;
                    break;
                    
                case 'coin':
                    if (Array.isArray(data.result)) {
                        const coinsHtml = data.result.map(flip => 
                            `<div class="coin-visual animate-flip">${flip}</div>`
                        ).join('');
                        resultDisplay.innerHTML = `
                            <div style="display: flex; gap: 15px; flex-wrap: wrap; justify-content: center;">
                                ${coinsHtml}
                            </div>
                            <div class="result-info">
                                ${data.statistics.heads} Heads, ${data.statistics.tails} Tails
                            </div>
                        `;
                    } else {
                        resultDisplay.innerHTML = `
                            <div class="coin-visual animate-flip">${data.result}</div>
                            <div class="result-info">Coin flip result</div>
                        `;
                    }
                    break;
                    
                case 'card':
                    if (Array.isArray(data.result)) {
                        const cardsHtml = data.result.map(card => `
                            <div class="card-display animate-shuffle">
                                <div class="card-visual ${card.color}">
                                    <div class="card-rank">${card.rank}</div>
                                    <div class="card-suit">${card.symbol}</div>
                                    <div class="card-rank" style="transform: rotate(180deg); align-self: flex-end;">${card.rank}</div>
                                </div>
                                <div>${card.display}</div>
                            </div>
                        `).join('');
                        resultDisplay.innerHTML = `
                            <div style="display: flex; gap: 15px; flex-wrap: wrap; justify-content: center;">
                                ${cardsHtml}
                            </div>
                            <div class="result-info">${data.deck_info.cards_drawn} card(s) drawn</div>
                        `;
                    } else {
                        const card = data.result;
                        resultDisplay.innerHTML = `
                            <div class="card-display animate-shuffle">
                                <div class="card-visual ${card.color}">
                                    <div class="card-rank">${card.rank}</div>
                                    <div class="card-suit">${card.symbol}</div>
                                    <div class="card-rank" style="transform: rotate(180deg); align-self: flex-end;">${card.rank}</div>
                                </div>
                                <div class="result-info">${card.display}</div>
                            </div>
                        `;
                    }
                    break;
            }
        }

        // Initialize
        updateButtonText();

        // Allow generating with Enter key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !isGenerating) {
                generate();
            }
        });

        // Input validation
        document.getElementById('min-number').addEventListener('change', function() {
            const max = document.getElementById('max-number');
            if (parseInt(this.value) > parseInt(max.value)) {
                max.value = this.value;
            }
        });

        document.getElementById('max-number').addEventListener('change', function() {
            const min = document.getElementById('min-number');
            if (parseInt(this.value) < parseInt(min.value)) {
                min.value = this.value;
            }
        });
    </script>
</body>
</html>
