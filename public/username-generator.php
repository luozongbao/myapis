<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Username Generator</title>
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

        select, input[type="number"], input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1em;
            transition: border-color 0.3s ease;
        }

        select:focus, input:focus {
            outline: none;
            border-color: #667eea;
        }

        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(165px, 1fr));
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

        .username-grid {
            display: grid;
            gap: 10px;
        }

        .username-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            background: #f8f9fa;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .username-item:hover {
            border-color: #667eea;
            background: #f0f4ff;
        }

        .username-text {
            font-family: 'Courier New', monospace;
            font-size: 1.1em;
            font-weight: 600;
            color: #333;
        }

        .copy-btn {
            padding: 6px 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8em;
            transition: background 0.3s ease;
        }

        .copy-btn:hover {
            background: #5a67d8;
        }

        .copy-btn.copied {
            background: #28a745;
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

        .theme-description {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
            font-style: italic;
        }

        .option-description {
            font-size: 0.9em;
            color: #666;
            margin-top: 15px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
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
                    <span>Username Generator</span>
                    <div style="margin-left: auto; display: flex; gap: 10px; flex-wrap: wrap;">
                        <a href="/api/username-generator/" style="color: #667eea; text-decoration: none; padding: 6px 12px; background: white; border-radius: 5px; border: 1px solid #ddd;">🔗 API</a>
                        <a href="api-specs/username-generator.php" style="color: #667eea; text-decoration: none; padding: 6px 12px; background: white; border-radius: 5px; border: 1px solid #ddd;">📚 API Docs</a>
                    </div>
                </div>
            </div>
            
            <h1>🎯 Username Generator</h1>
            <p>Create unique usernames with customizable themes and options</p>
        </div>

        <div class="form-section">
            <form id="usernameForm">
                <div class="form-group">
                    <label>Themes (select one or more):</label>
                    <div id="themesCheckboxGroup" class="checkbox-group">
                        <!-- Themes will be populated here -->
                    </div>
                    <div id="themeDescription" class="theme-description">
                        💡 <strong>Multi-Theme Selection:</strong> Combine multiple themes for more diverse username combinations. Words will be mixed from all selected themes.
                    </div>
                </div>

                <div class="form-group">
                    <label for="useCase">Use Case:</label>
                    <select id="useCase" name="use_case" required>
                        <option value="gaming">Gaming & Entertainment</option>
                        <option value="professional">Professional & Business</option>
                        <option value="social">Social Media</option>
                        <option value="general">General Purpose</option>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="minLength">Min Length:</label>
                        <input type="number" id="minLength" name="min_length" value="6" min="3" max="50" required>
                    </div>
                    <div class="form-group">
                        <label for="maxLength">Max Length:</label>
                        <input type="number" id="maxLength" name="max_length" value="20" min="3" max="50" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="count">Number of usernames to generate:</label>
                    <input type="number" id="count" name="count" value="10" min="1" max="50" required>
                </div>

                <div class="form-group">
                    <label for="customWords">Custom Words (optional):</label>
                    <input type="text" id="customWords" name="custom_words" placeholder="Enter words separated by commas">
                </div>

                <div class="form-group">
                    <label>Options:</label>
                    <div class="checkbox-group">
                        <div class="checkbox-item" data-checkbox="includeNumbers">
                            <input type="checkbox" id="includeNumbers" name="include_numbers">
                            <label for="includeNumbers">Include Numbers</label>
                        </div>
                        <div class="checkbox-item" data-checkbox="includeSymbols">
                            <input type="checkbox" id="includeSymbols" name="include_symbols">
                            <label for="includeSymbols">Include Symbols</label>
                        </div>
                        <div class="checkbox-item" data-checkbox="useAllAdjectives">
                            <input type="checkbox" id="useAllAdjectives" name="use_all_adjectives">
                            <label for="useAllAdjectives">Use All Adjectives</label>
                        </div>
                        <div class="checkbox-item" data-checkbox="useGeneralAdjectives">
                            <input type="checkbox" id="useGeneralAdjectives" name="use_general_adjectives">
                            <label for="useGeneralAdjectives">General Adjectives</label>
                        </div>
                        <div class="checkbox-item" data-checkbox="capitalize">
                            <input type="checkbox" id="capitalize" name="capitalize" checked>
                            <label for="capitalize">Capitalize Words</label>
                        </div>
                        <div class="checkbox-item" data-checkbox="avoidRepetition">
                            <input type="checkbox" id="avoidRepetition" name="avoid_repetition" checked>
                            <label for="avoidRepetition">Avoid Repetition</label>
                        </div>
                    </div>
                    <div class="option-description">
                        💡 <strong>Use All Adjectives:</strong> Combines adjectives from all themes with your selected theme's nouns for creative combinations like "GalacticPuppy" or "CyberBunny"<br>
                        🎨 <strong>General Adjectives:</strong> Adds colors, shapes, sizes, and common descriptive words like "RedWarrior", "BigData", or "SmoothAlgorithm"
                    </div>
                </div>

                <button type="submit" class="generate-btn" id="generateBtn">
                    🚀 Generate Usernames
                </button>
            </form>
        </div>

        <div class="results-section">
            <div class="results-header">
                <h2>Generated Usernames</h2>
                <button class="copy-all-btn" id="copyAllBtn" style="display: none;">Copy All</button>
            </div>

            <div class="error" id="error">
                <div id="errorMessage"></div>
            </div>

            <div class="loading" id="loading">
                <div class="loading-spinner"></div>
                <p>Generating awesome usernames...</p>
            </div>

            <div id="generationInfo" class="generation-info" style="display: none;"></div>

            <div class="username-grid" id="usernameGrid">
                <div style="text-align: center; color: #666; padding: 40px;">
                    <p>🎯 Configure your preferences and click "Generate Usernames" to get started!</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let themes = {};

        // Initialize the app
        document.addEventListener('DOMContentLoaded', function() {
            loadThemes();
            setupEventListeners();
        });

        async function loadThemes() {
            try {
                const response = await fetch('/api/username-generator/?action=themes');
                const data = await response.json();
                
                if (data.success) {
                    themes = data.theme_descriptions;
                    populateThemeSelect(data.themes, data.theme_descriptions);
                }
            } catch (error) {
                console.error('Failed to load themes:', error);
            }
        }

        function populateThemeSelect(themeList, descriptions) {
            const themesContainer = document.getElementById('themesCheckboxGroup');
            themesContainer.innerHTML = '';
            
            themeList.forEach(theme => {
                const checkboxItem = document.createElement('div');
                checkboxItem.className = 'checkbox-item';
                checkboxItem.innerHTML = `
                    <input type="checkbox" id="theme_${theme}" name="themes" value="${theme}">
                    <label for="theme_${theme}">${theme}</label>
                `;
                themesContainer.appendChild(checkboxItem);
            });

            // Set Fantasy as default selected
            const fantasyCheckbox = document.getElementById('theme_Fantasy');
            if (fantasyCheckbox) {
                fantasyCheckbox.checked = true;
            }

            // Setup event listeners for theme checkboxes
            setupThemeCheckboxListeners();
        }

        function setupEventListeners() {
            // Form submission
            document.getElementById('usernameForm').addEventListener('submit', generateUsernames);
            
            // Copy all button
            document.getElementById('copyAllBtn').addEventListener('click', copyAllUsernames);
            
            // Setup option checkboxes
            setupOptionCheckboxes();
        }

        function setupOptionCheckboxes() {
            // Checkbox styling for options (not themes)
            document.querySelectorAll('.checkbox-item:not([id="themesCheckboxGroup"] .checkbox-item)').forEach(item => {
                const checkbox = item.querySelector('input[type="checkbox"]');
                if (!checkbox) return;
                
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
        }

        function setupThemeCheckboxListeners() {
            // Setup theme checkbox styling and events
            document.querySelectorAll('#themesCheckboxGroup .checkbox-item').forEach(item => {
                const checkbox = item.querySelector('input[type="checkbox"]');
                if (!checkbox) return;
                
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
        }

        function updateCheckboxStyle(item, checked) {
            if (checked) {
                item.classList.add('checked');
            } else {
                item.classList.remove('checked');
            }
        }

        async function generateUsernames(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const options = {};
            
            // Handle multiple themes
            const selectedThemes = [];
            document.querySelectorAll('input[name="themes"]:checked').forEach(checkbox => {
                selectedThemes.push(checkbox.value);
            });
            
            if (selectedThemes.length === 0) {
                showError('Please select at least one theme.');
                return;
            }
            
            options.themes = selectedThemes;
            
            // Convert form data to options object
            for (let [key, value] of formData.entries()) {
                if (key === 'themes') {
                    // Already handled above
                    continue;
                } else if (['include_numbers', 'include_symbols', 'use_all_adjectives', 'use_general_adjectives', 'capitalize', 'avoid_repetition'].includes(key)) {
                    options[key] = true; // Checkbox is checked if it exists in formData
                } else {
                    options[key] = value;
                }
            }

            // Set unchecked checkboxes to false
            ['include_numbers', 'include_symbols', 'use_all_adjectives', 'use_general_adjectives', 'capitalize', 'avoid_repetition'].forEach(key => {
                if (!(key in options)) {
                    options[key] = false;
                }
            });

            showLoading();
            hideError();
            hideResults();

            try {
                const response = await fetch('/api/username-generator/', {
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
                showError('Failed to connect to the username generator API. Please try again.');
                console.error('Error:', error);
            } finally {
                hideLoading();
            }
        }

        function displayResults(data) {
            const grid = document.getElementById('usernameGrid');
            const info = document.getElementById('generationInfo');
            const copyAllBtn = document.getElementById('copyAllBtn');
            
            // Show generation info
            const genInfo = data.generation_info;
            const themesText = genInfo.themes ? genInfo.themes.join(', ') : 'N/A';
            info.innerHTML = `
                <strong>Generated:</strong> ${data.data.count} usernames | 
                <strong>Themes:</strong> ${themesText} (${genInfo.theme_count}) | 
                <strong>Length:</strong> ${genInfo.length_range} | 
                <strong>Features:</strong> ${Object.entries(genInfo.features).map(([k,v]) => `${k}: ${v}`).join(', ')}
            `;
            info.style.display = 'block';
            
            // Clear and populate grid
            grid.innerHTML = '';
            
            data.data.usernames.forEach((username, index) => {
                const item = document.createElement('div');
                item.className = 'username-item';
                item.innerHTML = `
                    <span class="username-text">${username}</span>
                    <button class="copy-btn" onclick="copyUsername('${username}', this)">Copy</button>
                `;
                grid.appendChild(item);
            });
            
            // Show copy all button
            copyAllBtn.style.display = 'block';
        }

        function copyUsername(username, button) {
            navigator.clipboard.writeText(username).then(() => {
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

        function copyAllUsernames() {
            const usernames = Array.from(document.querySelectorAll('.username-text'))
                .map(el => el.textContent)
                .join('\n');
            
            navigator.clipboard.writeText(usernames).then(() => {
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
