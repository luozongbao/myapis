<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BMI Calculator</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 500px;
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #333;
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 1.1em;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 1.1em;
        }

        input[type="number"], select {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1.1em;
            transition: border-color 0.3s ease;
        }

        input[type="number"]:focus, select:focus {
            outline: none;
            border-color: #667eea;
        }

        .unit-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .unit-option {
            flex: 1;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }

        .unit-option.active {
            border-color: #667eea;
            background: #667eea;
            color: white;
        }

        .calculate-btn {
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
        }

        .calculate-btn:hover {
            transform: translateY(-2px);
        }

        .calculate-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .result {
            margin-top: 30px;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            display: none;
        }

        .result.show {
            display: block;
        }

        .result.normal {
            background: #d4edda;
            border: 2px solid #c3e6cb;
            color: #155724;
        }

        .result.underweight {
            background: #cce7ff;
            border: 2px solid #99d6ff;
            color: #004085;
        }

        .result.overweight {
            background: #fff3cd;
            border: 2px solid #ffecb5;
            color: #856404;
        }

        .result.obese {
            background: #f8d7da;
            border: 2px solid #f5c6cb;
            color: #721c24;
        }

        .bmi-value {
            font-size: 3em;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .bmi-category {
            font-size: 1.5em;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .bmi-advice {
            font-size: 1.1em;
            line-height: 1.5;
        }

        .loading {
            display: none;
            text-align: center;
            margin-top: 20px;
        }

        .loading.show {
            display: block;
        }

        .error {
            background: #f8d7da;
            border: 2px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            display: none;
        }

        .error.show {
            display: block;
        }

        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
            }
            
            .header h1 {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>BMI Calculator</h1>
            <p>Calculate your Body Mass Index and get health recommendations</p>
        </div>

        <form id="bmiForm">
            <div class="unit-selector">
                <div class="unit-option active" data-unit="metric">
                    Metric (kg/cm)
                </div>
                <div class="unit-option" data-unit="imperial">
                    Imperial (lbs/in)
                </div>
            </div>

            <div class="form-group">
                <label for="weight">Weight (<span id="weightUnit">kg</span>):</label>
                <input type="number" id="weight" name="weight" step="0.1" min="1" max="1000" required>
            </div>

            <div class="form-group">
                <label for="height">Height (<span id="heightUnit">cm</span>):</label>
                <input type="number" id="height" name="height" step="0.1" min="1" max="300" required>
            </div>

            <button type="submit" class="calculate-btn" id="calculateBtn">
                Calculate BMI
            </button>
        </form>

        <div class="loading" id="loading">
            <p>Calculating your BMI...</p>
        </div>

        <div class="error" id="error">
            <p id="errorMessage"></p>
        </div>

        <div class="result" id="result">
            <div class="bmi-value" id="bmiValue"></div>
            <div class="bmi-category" id="bmiCategory"></div>
            <div class="bmi-advice" id="bmiAdvice"></div>
        </div>
    </div>

    <script>
        let currentUnit = 'metric';

        // Unit selector functionality
        document.querySelectorAll('.unit-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove active class from all options
                document.querySelectorAll('.unit-option').forEach(opt => opt.classList.remove('active'));
                
                // Add active class to clicked option
                this.classList.add('active');
                
                // Update current unit
                currentUnit = this.dataset.unit;
                
                // Update labels
                updateLabels();
                
                // Clear previous results
                hideResults();
            });
        });

        function updateLabels() {
            const weightUnit = document.getElementById('weightUnit');
            const heightUnit = document.getElementById('heightUnit');
            const weightInput = document.getElementById('weight');
            const heightInput = document.getElementById('height');

            if (currentUnit === 'metric') {
                weightUnit.textContent = 'kg';
                heightUnit.textContent = 'cm';
                weightInput.placeholder = 'e.g. 70';
                heightInput.placeholder = 'e.g. 175';
                heightInput.max = '300';
            } else {
                weightUnit.textContent = 'lbs';
                heightUnit.textContent = 'inches';
                weightInput.placeholder = 'e.g. 154';
                heightInput.placeholder = 'e.g. 69';
                heightInput.max = '120';
            }
        }

        // Form submission
        document.getElementById('bmiForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const weight = parseFloat(document.getElementById('weight').value);
            const height = parseFloat(document.getElementById('height').value);
            
            if (!weight || !height) {
                showError('Please enter both weight and height.');
                return;
            }

            if (weight <= 0 || height <= 0) {
                showError('Weight and height must be positive values.');
                return;
            }

            showLoading();
            hideResults();
            hideError();

            try {
                const response = await fetch('./api/', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        weight: weight,
                        height: height,
                        unit: currentUnit
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showResult(data.data);
                } else {
                    showError(data.message || 'An error occurred while calculating BMI.');
                }
            } catch (error) {
                showError('Failed to connect to the BMI calculator API. Please try again.');
                console.error('Error:', error);
            } finally {
                hideLoading();
            }
        });

        function showLoading() {
            document.getElementById('loading').classList.add('show');
            document.getElementById('calculateBtn').disabled = true;
        }

        function hideLoading() {
            document.getElementById('loading').classList.remove('show');
            document.getElementById('calculateBtn').disabled = false;
        }

        function showResult(data) {
            const resultDiv = document.getElementById('result');
            const bmiValue = document.getElementById('bmiValue');
            const bmiCategory = document.getElementById('bmiCategory');
            const bmiAdvice = document.getElementById('bmiAdvice');

            bmiValue.textContent = data.bmi;
            bmiCategory.textContent = data.category;
            bmiAdvice.textContent = data.advice;

            // Remove all category classes
            resultDiv.classList.remove('normal', 'underweight', 'overweight', 'obese');
            
            // Add appropriate category class
            const categoryClass = data.category.toLowerCase().replace(' ', '');
            if (categoryClass === 'normalweight') {
                resultDiv.classList.add('normal');
            } else {
                resultDiv.classList.add(categoryClass);
            }

            resultDiv.classList.add('show');
        }

        function hideResults() {
            document.getElementById('result').classList.remove('show');
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

        // Initialize labels
        updateLabels();
    </script>
</body>
</html>
