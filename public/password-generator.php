<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Generator</title>
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

        .form-section, .results-section {
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
        }

        .header p {
            color: #666;
            font-size: 1.2em;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 1.1em;
        }

        input[type="number"], input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1em;
            transition: border-color 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: #667eea;
        }

        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .checkbox-item:hover {
            border-color: #667eea;
            background: #f0f4ff;
        }

        .checkbox-item input[type="checkbox"] {
            width: auto;
            margin: 0;
        }

        .checkbox-item.checked {
            border-color: #667eea;
            background: #667eea;
            color: white;
        }

        .generate-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.2em;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
            margin-top: 20px;
        }

        .generate-btn:hover {
            transform: translateY(-2px);
        }

        .generate-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .results-header h2 {
            color: #333;
            font-size: 1.5em;
        }

        .copy-all-btn {
            padding: 8px 16px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9em;
        }

        .password-grid {
            display: grid;
            gap: 15px;
        }

        .password-item {
            padding: 15px;
            background: #f8f9fa;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .password-item:hover {
            border-color: #667eea;
            background: #f0f4ff;
        }

        .password-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .password-text {
            font-family: 'Courier New', monospace;
            font-size: 1.2em;
            font-weight: 600;
            color: #333;
            word-break: break-all;
            user-select: all;
        }

        .password-actions {
            display: flex;
            gap: 10px;
        }

        .copy-btn, .analyze-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8em;
            transition: background 0.3s ease;
        }

        .copy-btn {
            background: #667eea;
            color: white;
        }

        .copy-btn:hover {
            background: #5a67d8;
        }

        .copy-btn.copied {
            background: #28a745;
        }

        .analyze-btn {
            background: #17a2b8;
            color: white;
        }

        .analyze-btn:hover {
            background: #138496;
        }

        .password-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            font-size: 0.9em;
            color: #666;
        }

        .strength-indicator {
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8em;
        }

        .strength-weak {
            background: #f8d7da;
            color: #721c24;
        }

        .strength-medium {
            background: #fff3cd;
            color: #856404;
        }

        .strength-strong {
            background: #d4edda;
            color: #155724;
        }

        .strength-very-strong {
            background: #cce7ff;
            color: #004085;
        }

        .password-analysis {
            background: #e7f3ff;
            border: 2px solid #b8daff;
            color: #004085;
            padding: 15px;
            border-radius: 10px;
            margin-top: 10px;
            font-size: 0.9em;
            display: none;
        }

        .analysis-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            margin-bottom: 10px;
        }

        .analysis-item {
            text-align: center;
            padding: 5px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 4px;
        }

        .analysis-tips {
            margin-top: 10px;
        }

        .analysis-tips ul {
            padding-left: 20px;
        }

        .loading {
            text-align: center;
            padding: 40px;
            display: none;
        }

        .loading.show {
            display: block;
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .error {
            background: #f8d7da;
            border: 2px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: none;
        }

        .error.show {
            display: block;
        }

        .generation-info {
            background: #e7f3ff;
            border: 2px solid #b8daff;
            color: #004085;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.9em;
        }

        .security-tips {
            background: #d4edda;
            border: 2px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            font-size: 0.9em;
        }

        .security-tips h3 {
            margin-bottom: 10px;
        }

        .security-tips ul {
            padding-left: 20px;
        }

        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .checkbox-group {
                grid-template-columns: 1fr;
            }
            
            .header h1 {
                font-size: 2em;
            }
            
            .form-section, .results-section {
                padding: 20px;
            }

            .password-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Navigation -->
            <div style="background: #f8f9fa; padding: 15px; border-radius: 15px; border-bottom: 1px solid #e9ecef; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 10px; font-size: 0.9em; color: #666; flex-wrap: wrap;">
                    <a href="index.php" style="color: #667eea; text-decoration: none;">← Back to Main</a>
                    <span>/</span>
                    <span>Password Generator</span>
                    <div style="margin-left: auto; display: flex; gap: 10px; flex-wrap: wrap;">
                        <a href="/api/password-generator/" style="color: #667eea; text-decoration: none; padding: 6px 12px; background: white; border-radius: 5px; border: 1px solid #ddd;">🔗 API</a>
                        <a href="api-specs/password-generator.php" style="color: #667eea; text-decoration: none; padding: 6px 12px; background: white; border-radius: 5px; border: 1px solid #ddd;">📚 API Docs</a>
                    </div>
                </div>
            </div>
            
            <h1>🔐 Password Generator</h1>
            <p>Generate secure passwords with customizable options and strength analysis</p>
        </div>

        <div class="form-section">
            <form id="passwordForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="minLength">Min Length:</label>
                        <input type="number" id="minLength" name="min_length" value="8" min="1" max="128" required>
                    </div>
                    <div class="form-group">
                        <label for="maxLength">Max Length:</label>
                        <input type="number" id="maxLength" name="max_length" value="16" min="1" max="128" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="count">Number of passwords to generate:</label>
                    <input type="number" id="count" name="count" value="5" min="1" max="100" required>
                </div>

                <div class="form-group">
                    <label>Character Types:</label>
                    <div class="checkbox-group">
                        <div class="checkbox-item" data-checkbox="includeLowercase">
                            <input type="checkbox" id="includeLowercase" name="include_lowercase" checked>
                            <label for="includeLowercase">Lowercase (a-z)</label>
                        </div>
                        <div class="checkbox-item" data-checkbox="includeUppercase">
                            <input type="checkbox" id="includeUppercase" name="include_uppercase" checked>
                            <label for="includeUppercase">Uppercase (A-Z)</label>
                        </div>
                        <div class="checkbox-item" data-checkbox="includeNumbers">
                            <input type="checkbox" id="includeNumbers" name="include_numbers" checked>
                            <label for="includeNumbers">Numbers (0-9)</label>
                        </div>
                        <div class="checkbox-item" data-checkbox="includeSymbols">
                            <input type="checkbox" id="includeSymbols" name="include_symbols">
                            <label for="includeSymbols">Symbols (!@#$%)</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Security Options:</label>
                    <div class="checkbox-group">
                        <div class="checkbox-item" data-checkbox="excludeAmbiguous">
                            <input type="checkbox" id="excludeAmbiguous" name="exclude_ambiguous">
                            <label for="excludeAmbiguous">Exclude Ambiguous</label>
                        </div>
                        <div class="checkbox-item" data-checkbox="noRepeatedChars">
                            <input type="checkbox" id="noRepeatedChars" name="no_repeated_chars">
                            <label for="noRepeatedChars">No Repeated Chars</label>
                        </div>
                        <div class="checkbox-item" data-checkbox="mustIncludeEach">
                            <input type="checkbox" id="mustIncludeEach" name="must_include_each_type" checked>
                            <label for="mustIncludeEach">Include Each Type</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="customSymbols">Custom Symbols (optional):</label>
                    <input type="text" id="customSymbols" name="custom_symbols" placeholder="Enter custom symbols">
                </div>

                <button type="submit" class="generate-btn" id="generateBtn">
                    🛡️ Generate Secure Passwords
                </button>
            </form>

            <div class="security-tips">
                <h3>🔒 Security Tips</h3>
                <ul>
                    <li>Use at least 12 characters for strong passwords</li>
                    <li>Include all character types (uppercase, lowercase, numbers, symbols)</li>
                    <li>Never reuse passwords across different accounts</li>
                    <li>Consider using a password manager</li>
                    <li>Change passwords regularly for important accounts</li>
                </ul>
            </div>
        </div>

        <div class="results-section">
            <div class="results-header">
                <h2>Generated Passwords</h2>
                <button class="copy-all-btn" id="copyAllBtn" style="display: none;">Copy All</button>
            </div>

            <div class="error" id="error">
                <div id="errorMessage"></div>
            </div>

            <div class="loading" id="loading">
                <div class="loading-spinner"></div>
                <p>Generating secure passwords...</p>
            </div>

            <div id="generationInfo" class="generation-info" style="display: none;"></div>

            <div class="password-grid" id="passwordGrid">
                <div style="text-align: center; color: #666; padding: 40px;">
                    <p>🔐 Configure your preferences and click "Generate Secure Passwords" to get started!</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize the app
        document.addEventListener('DOMContentLoaded', function() {
            setupEventListeners();
        });

        function setupEventListeners() {
            // Checkbox styling
            document.querySelectorAll('.checkbox-item').forEach(item => {
                const checkbox = item.querySelector('input[type="checkbox"]');
                
                item.addEventListener('click', function(e) {
                    if (e.target.type !== 'checkbox') {
                        checkbox.checked = !checkbox.checked;
                    }
                    updateCheckboxStyle(item, checkbox.checked);
                });

                // Initial state
                updateCheckboxStyle(item, checkbox.checked);
                
                // Listen for direct checkbox changes
                checkbox.addEventListener('change', function() {
                    updateCheckboxStyle(item, this.checked);
                });
            });

            // Form submission
            document.getElementById('passwordForm').addEventListener('submit', generatePasswords);
            
            // Copy all button
            document.getElementById('copyAllBtn').addEventListener('click', copyAllPasswords);
        }

        function updateCheckboxStyle(item, checked) {
            if (checked) {
                item.classList.add('checked');
            } else {
                item.classList.remove('checked');
            }
        }

        async function generatePasswords(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const options = {};
            
            // Convert form data to options object
            for (let [key, value] of formData.entries()) {
                if (['include_lowercase', 'include_uppercase', 'include_numbers', 'include_symbols', 
                     'exclude_ambiguous', 'no_repeated_chars', 'must_include_each_type'].includes(key)) {
                    options[key] = true; // Checkbox is checked if it exists in formData
                } else {
                    options[key] = value;
                }
            }

            // Set unchecked checkboxes to false
            ['include_lowercase', 'include_uppercase', 'include_numbers', 'include_symbols', 
             'exclude_ambiguous', 'no_repeated_chars', 'must_include_each_type'].forEach(key => {
                if (!(key in options)) {
                    options[key] = false;
                }
            });

            showLoading();
            hideError();
            hideResults();

            try {
                const response = await fetch('/api/password-generator/', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(options)
                });

                const data = await response.json();

                if (data.success) {
                    displayResults(data);
                } else {
                    showError(data.messages ? data.messages.join(', ') : data.message);
                }
            } catch (error) {
                showError('Failed to connect to the password generator API. Please try again.');
                console.error('Error:', error);
            } finally {
                hideLoading();
            }
        }

        function displayResults(data) {
            const grid = document.getElementById('passwordGrid');
            const info = document.getElementById('generationInfo');
            const copyAllBtn = document.getElementById('copyAllBtn');
            
            // Show generation info
            const genInfo = data.generation_info;
            info.innerHTML = `
                <strong>Generated:</strong> ${data.data.count} passwords | 
                <strong>Length:</strong> ${genInfo.length_range} | 
                <strong>Types:</strong> ${Object.entries(genInfo.character_types).filter(([k,v]) => v === 'included').map(([k,v]) => k).join(', ')}
            `;
            info.style.display = 'block';
            
            // Clear and populate grid
            grid.innerHTML = '';
            
            data.data.passwords.forEach((passwordData, index) => {
                const item = document.createElement('div');
                item.className = 'password-item';
                item.innerHTML = `
                    <div class="password-header">
                        <div class="password-text">${passwordData.password}</div>
                        <div class="password-actions">
                            <button class="copy-btn" onclick="copyPassword('${passwordData.password}', this)">Copy</button>
                            <button class="analyze-btn" onclick="analyzePassword('${passwordData.password}', this)">Analyze</button>
                        </div>
                    </div>
                    <div class="password-info">
                        <span>Length: ${passwordData.length}</span>
                        <span class="strength-indicator strength-${passwordData.strength.replace(' ', '-')}">${passwordData.strength}</span>
                    </div>
                    <div class="password-analysis" id="analysis-${index}"></div>
                `;
                grid.appendChild(item);
            });
            
            // Show copy all button
            copyAllBtn.style.display = 'block';
        }

        function copyPassword(password, button) {
            navigator.clipboard.writeText(password).then(() => {
                const originalText = button.textContent;
                button.textContent = 'Copied!';
                button.classList.add('copied');
                
                setTimeout(() => {
                    button.textContent = originalText;
                    button.classList.remove('copied');
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        }

        async function analyzePassword(password, button) {
            const analysisDiv = button.closest('.password-item').querySelector('.password-analysis');
            
            if (analysisDiv.style.display === 'block') {
                analysisDiv.style.display = 'none';
                button.textContent = 'Analyze';
                return;
            }

            button.textContent = 'Analyzing...';
            
            try {
                const response = await fetch(`./api/?action=analyze&password=${encodeURIComponent(password)}`);
                const data = await response.json();

                if (data.success) {
                    const analysis = data.analysis;
                    const tips = data.tips;

                    analysisDiv.innerHTML = `
                        <div class="analysis-details">
                            <div class="analysis-item">
                                <strong>Length</strong><br>${analysis.length}
                            </div>
                            <div class="analysis-item">
                                <strong>Lowercase</strong><br>${analysis.has_lowercase ? '✅' : '❌'}
                            </div>
                            <div class="analysis-item">
                                <strong>Uppercase</strong><br>${analysis.has_uppercase ? '✅' : '❌'}
                            </div>
                            <div class="analysis-item">
                                <strong>Numbers</strong><br>${analysis.has_numbers ? '✅' : '❌'}
                            </div>
                            <div class="analysis-item">
                                <strong>Symbols</strong><br>${analysis.has_symbols ? '✅' : '❌'}
                            </div>
                            <div class="analysis-item">
                                <strong>Score</strong><br>${analysis.score}/8
                            </div>
                        </div>
                        <div class="analysis-tips">
                            <strong>Tips:</strong>
                            <ul>
                                ${tips.map(tip => `<li>${tip}</li>`).join('')}
                            </ul>
                        </div>
                    `;
                    analysisDiv.style.display = 'block';
                    button.textContent = 'Hide Analysis';
                } else {
                    showError('Failed to analyze password');
                }
            } catch (error) {
                showError('Failed to analyze password');
                console.error('Error:', error);
            }
        }

        function copyAllPasswords() {
            const passwords = Array.from(document.querySelectorAll('.password-text'))
                .map(el => el.textContent)
                .join('\n');
            
            navigator.clipboard.writeText(passwords).then(() => {
                const button = document.getElementById('copyAllBtn');
                const originalText = button.textContent;
                button.textContent = 'All Copied!';
                
                setTimeout(() => {
                    button.textContent = originalText;
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy all: ', err);
            });
        }

        function showLoading() {
            document.getElementById('loading').classList.add('show');
            document.getElementById('generateBtn').disabled = true;
        }

        function hideLoading() {
            document.getElementById('loading').classList.remove('show');
            document.getElementById('generateBtn').disabled = false;
        }

        function showError(message) {
            const errorDiv = document.getElementById('error');
            const errorMessage = document.getElementById('errorMessage');
            
            errorMessage.textContent = message;
            errorDiv.classList.add('show');
        }

        function hideError() {
            document.getElementById('error').classList.remove('show');
        }

        function hideResults() {
            document.getElementById('generationInfo').style.display = 'none';
            document.getElementById('copyAllBtn').style.display = 'none';
        }
    </script>
</body>
</html>
