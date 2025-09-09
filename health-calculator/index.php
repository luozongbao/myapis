<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Calculator</title>
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
            max-width: 600px;
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

        .calculator-selector {
            display: flex;
            gap: 8px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .calc-option {
            flex: 1;
            padding: 10px 8px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            font-size: 0.9em;
            font-weight: 600;
            min-width: 120px;
        }

        .calc-option.active {
            border-color: #667eea;
            background: #667eea;
            color: white;
        }

        .calculator-section {
            transition: all 0.3s ease;
        }

        .calculator-section.hidden {
            display: none;
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

        .result-section.hidden {
            display: none;
        }

        .bmr-title, .intake-title {
            font-size: 1.5em;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }

        .bmr-value, .intake-calories {
            font-size: 2.5em;
            font-weight: bold;
            margin-bottom: 10px;
            color: #667eea;
        }

        .bmr-detail, .intake-breakdown {
            font-size: 1.1em;
            margin-bottom: 15px;
            color: #666;
        }

        .bmr-advice, .intake-advice {
            font-size: 1.1em;
            line-height: 1.5;
            color: #333;
        }

        .water-title {
            font-size: 1.5em;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }

        .water-amount {
            font-size: 2.5em;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2196F3;
        }

        .water-breakdown {
            font-size: 1.1em;
            margin-bottom: 15px;
            color: #666;
        }

        .water-advice {
            font-size: 1.1em;
            line-height: 1.5;
            color: #333;
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
            <h1>Health Calculator</h1>
            <p>Calculate BMI, BMR, Daily Intake, and Water Intake</p>
        </div>

        <!-- Calculator Type Selector -->
        <div class="calculator-selector">
            <div class="calc-option active" data-calc="bmi">
                BMI Calculator
            </div>
            <div class="calc-option" data-calc="bmr">
                BMR Calculator
            </div>
            <div class="calc-option" data-calc="intake">
                Daily Intake
            </div>
            <div class="calc-option" data-calc="water">
                Water Intake
            </div>
        </div>

        <form id="healthForm">
            <div class="unit-selector">
                <div class="unit-option active" data-unit="metric">
                    Metric (kg/cm)
                </div>
                <div class="unit-option" data-unit="imperial">
                    Imperial (lbs/in)
                </div>
            </div>

            <!-- BMI Calculator Fields -->
            <div class="calculator-section" id="bmi-section">
                <div class="form-group">
                    <label for="weight">Weight (<span id="weightUnit">kg</span>):</label>
                    <input type="number" id="weight" name="weight" step="0.1" min="1" max="1000" required>
                </div>

                <div class="form-group">
                    <label for="height">Height (<span id="heightUnit">cm</span>):</label>
                    <input type="number" id="height" name="height" step="0.1" min="1" max="300" required>
                </div>
            </div>

            <!-- BMR Calculator Fields -->
            <div class="calculator-section hidden" id="bmr-section">
                <div class="form-group">
                    <label for="bmr-weight">Weight (<span id="bmrWeightUnit">kg</span>):</label>
                    <input type="number" id="bmr-weight" name="bmr-weight" step="0.1" min="1" max="1000">
                </div>

                <div class="form-group">
                    <label for="bmr-height">Height (<span id="bmrHeightUnit">cm</span>):</label>
                    <input type="number" id="bmr-height" name="bmr-height" step="0.1" min="1" max="300">
                </div>

                <div class="form-group">
                    <label for="age">Age (years):</label>
                    <input type="number" id="age" name="age" min="1" max="120">
                </div>

                <div class="form-group">
                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="activity">Activity Level:</label>
                    <select id="activity" name="activity">
                        <option value="sedentary">Sedentary (little or no exercise)</option>
                        <option value="light">Light activity (light exercise 1-3 days/week)</option>
                        <option value="moderate">Moderate activity (moderate exercise 3-5 days/week)</option>
                        <option value="active">Very active (hard exercise 6-7 days/week)</option>
                        <option value="extra">Extra active (very hard exercise, physical job)</option>
                    </select>
                </div>
            </div>

            <!-- Daily Intake Calculator Fields -->
            <div class="calculator-section hidden" id="intake-section">
                <div class="form-group">
                    <label for="intake-weight">Weight (<span id="intakeWeightUnit">kg</span>):</label>
                    <input type="number" id="intake-weight" name="intake-weight" step="0.1" min="1" max="1000">
                </div>

                <div class="form-group">
                    <label for="intake-height">Height (<span id="intakeHeightUnit">cm</span>):</label>
                    <input type="number" id="intake-height" name="intake-height" step="0.1" min="1" max="300">
                </div>

                <div class="form-group">
                    <label for="intake-age">Age (years):</label>
                    <input type="number" id="intake-age" name="intake-age" min="1" max="120">
                </div>

                <div class="form-group">
                    <label for="intake-gender">Gender:</label>
                    <select id="intake-gender" name="intake-gender">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="intake-activity">Activity Level:</label>
                    <select id="intake-activity" name="intake-activity">
                        <option value="sedentary">Sedentary (little or no exercise)</option>
                        <option value="light">Light activity (light exercise 1-3 days/week)</option>
                        <option value="moderate">Moderate activity (moderate exercise 3-5 days/week)</option>
                        <option value="active">Very active (hard exercise 6-7 days/week)</option>
                        <option value="extra">Extra active (very hard exercise, physical job)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="goal">Goal:</label>
                    <select id="goal" name="goal">
                        <option value="maintain">Maintain weight</option>
                        <option value="lose">Lose weight (0.5 kg/week)</option>
                        <option value="lose-fast">Lose weight fast (1 kg/week)</option>
                        <option value="gain">Gain weight (0.5 kg/week)</option>
                        <option value="gain-fast">Gain weight fast (1 kg/week)</option>
                    </select>
                </div>
            </div>

            <!-- Daily Water Intake Calculator Fields -->
            <div class="calculator-section hidden" id="water-section">
                <div class="form-group">
                    <label for="water-weight">Weight (<span id="waterWeightUnit">kg</span>):</label>
                    <input type="number" id="water-weight" name="water-weight" step="0.1" min="1" max="1000">
                </div>

                <div class="form-group">
                    <label for="water-age">Age (years):</label>
                    <input type="number" id="water-age" name="water-age" min="1" max="120">
                </div>

                <div class="form-group">
                    <label for="water-gender">Gender:</label>
                    <select id="water-gender" name="water-gender">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="water-activity">Activity Level:</label>
                    <select id="water-activity" name="water-activity">
                        <option value="sedentary">Sedentary (little or no exercise)</option>
                        <option value="light">Light activity (light exercise 1-3 days/week)</option>
                        <option value="moderate">Moderate activity (moderate exercise 3-5 days/week)</option>
                        <option value="active">Very active (hard exercise 6-7 days/week)</option>
                        <option value="extra">Extra active (very hard exercise, physical job)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="climate">Climate:</label>
                    <select id="climate" name="climate">
                        <option value="temperate">Temperate (15-25°C)</option>
                        <option value="hot">Hot (25-35°C)</option>
                        <option value="very-hot">Very Hot (>35°C)</option>
                        <option value="cold">Cold (<15°C)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="health-condition">Health Condition:</label>
                    <select id="health-condition" name="health-condition">
                        <option value="normal">Normal</option>
                        <option value="fever">Fever</option>
                        <option value="diarrhea">Diarrhea/Vomiting</option>
                        <option value="kidney">Kidney Issues</option>
                        <option value="heart">Heart Condition</option>
                        <option value="pregnancy">Pregnancy</option>
                        <option value="breastfeeding">Breastfeeding</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="calculate-btn" id="calculateBtn">
                <span id="btnText">Calculate BMI</span>
            </button>
        </form>

        <div class="loading" id="loading">
            <p>Calculating...</p>
        </div>

        <div class="error" id="error">
            <p id="errorMessage"></p>
        </div>

        <div class="result" id="result">
            <!-- BMI Results -->
            <div class="result-section" id="bmi-result">
                <div class="bmi-value" id="bmiValue"></div>
                <div class="bmi-category" id="bmiCategory"></div>
                <div class="bmi-advice" id="bmiAdvice"></div>
            </div>

            <!-- BMR Results -->
            <div class="result-section hidden" id="bmr-result">
                <div class="bmr-title">Your Basal Metabolic Rate</div>
                <div class="bmr-value" id="bmrValue"></div>
                <div class="bmr-detail" id="bmrDetail"></div>
                <div class="bmr-advice" id="bmrAdvice"></div>
            </div>

            <!-- Daily Intake Results -->
            <div class="result-section hidden" id="intake-result">
                <div class="intake-title">Your Daily Caloric Needs</div>
                <div class="intake-calories" id="intakeCalories"></div>
                <div class="intake-breakdown" id="intakeBreakdown"></div>
                <div class="intake-advice" id="intakeAdvice"></div>
            </div>

            <!-- Water Intake Results -->
            <div class="result-section hidden" id="water-result">
                <div class="water-title">Your Daily Water Intake</div>
                <div class="water-amount" id="waterAmount"></div>
                <div class="water-breakdown" id="waterBreakdown"></div>
                <div class="water-advice" id="waterAdvice"></div>
            </div>
        </div>
    </div>

    <script>
        let currentUnit = 'metric';
        let currentCalculator = 'bmi';

        // Calculator type selector functionality
        document.querySelectorAll('.calc-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove active class from all options
                document.querySelectorAll('.calc-option').forEach(opt => opt.classList.remove('active'));
                
                // Add active class to clicked option
                this.classList.add('active');
                
                // Update current calculator
                currentCalculator = this.dataset.calc;
                
                // Show/hide appropriate sections
                updateCalculatorSections();
                
                // Update button text
                updateButtonText();
                
                // Clear previous results
                hideResults();
            });
        });

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

        function updateCalculatorSections() {
            // Hide all sections
            document.querySelectorAll('.calculator-section').forEach(section => {
                section.classList.add('hidden');
            });
            
            document.querySelectorAll('.result-section').forEach(section => {
                section.classList.add('hidden');
            });

            // Show current section
            document.getElementById(currentCalculator + '-section').classList.remove('hidden');
        }

        function updateButtonText() {
            const btnText = document.getElementById('btnText');
            switch(currentCalculator) {
                case 'bmi':
                    btnText.textContent = 'Calculate BMI';
                    break;
                case 'bmr':
                    btnText.textContent = 'Calculate BMR';
                    break;
                case 'intake':
                    btnText.textContent = 'Calculate Daily Intake';
                    break;
                case 'water':
                    btnText.textContent = 'Calculate Water Intake';
                    break;
            }
        }

        function updateLabels() {
            const units = {
                weight: currentUnit === 'metric' ? 'kg' : 'lbs',
                height: currentUnit === 'metric' ? 'cm' : 'inches'
            };

            // Update all weight and height unit labels
            document.querySelectorAll('[id$="WeightUnit"], #weightUnit').forEach(el => {
                el.textContent = units.weight;
            });
            
            document.querySelectorAll('[id$="HeightUnit"], #heightUnit').forEach(el => {
                el.textContent = units.height;
            });

            // Update input placeholders and max values
            const weightInputs = document.querySelectorAll('input[id*="weight"]');
            const heightInputs = document.querySelectorAll('input[id*="height"]');

            weightInputs.forEach(input => {
                input.placeholder = currentUnit === 'metric' ? 'e.g. 70' : 'e.g. 154';
            });

            heightInputs.forEach(input => {
                input.placeholder = currentUnit === 'metric' ? 'e.g. 175' : 'e.g. 69';
                input.max = currentUnit === 'metric' ? '300' : '120';
            });
        }

        // Form submission
        document.getElementById('healthForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = getFormData();
            
            if (!validateFormData(formData)) {
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
                        calculator: currentCalculator,
                        unit: currentUnit,
                        ...formData
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showResult(data.data);
                } else {
                    showError(data.message || 'An error occurred while calculating.');
                }
            } catch (error) {
                showError('Failed to connect to the calculator API. Please try again.');
                console.error('Error:', error);
            } finally {
                hideLoading();
            }
        });

        function getFormData() {
            const data = {};
            
            switch(currentCalculator) {
                case 'bmi':
                    const weightEl = document.getElementById('weight');
                    const heightEl = document.getElementById('height');
                    data.weight = weightEl ? parseFloat(weightEl.value) : null;
                    data.height = heightEl ? parseFloat(heightEl.value) : null;
                    break;
                case 'bmr':
                    const bmrWeightEl = document.getElementById('bmr-weight');
                    const bmrHeightEl = document.getElementById('bmr-height');
                    const ageEl = document.getElementById('age');
                    const genderEl = document.getElementById('gender');
                    const activityEl = document.getElementById('activity');
                    data.weight = bmrWeightEl ? parseFloat(bmrWeightEl.value) : null;
                    data.height = bmrHeightEl ? parseFloat(bmrHeightEl.value) : null;
                    data.age = ageEl ? parseInt(ageEl.value) : null;
                    data.gender = genderEl ? genderEl.value : null;
                    data.activity = activityEl ? activityEl.value : null;
                    break;
                case 'intake':
                    const intakeWeightEl = document.getElementById('intake-weight');
                    const intakeHeightEl = document.getElementById('intake-height');
                    const intakeAgeEl = document.getElementById('intake-age');
                    const intakeGenderEl = document.getElementById('intake-gender');
                    const intakeActivityEl = document.getElementById('intake-activity');
                    const goalEl = document.getElementById('goal');
                    data.weight = intakeWeightEl ? parseFloat(intakeWeightEl.value) : null;
                    data.height = intakeHeightEl ? parseFloat(intakeHeightEl.value) : null;
                    data.age = intakeAgeEl ? parseInt(intakeAgeEl.value) : null;
                    data.gender = intakeGenderEl ? intakeGenderEl.value : null;
                    data.activity = intakeActivityEl ? intakeActivityEl.value : null;
                    data.goal = goalEl ? goalEl.value : null;
                    break;
                case 'water':
                    const waterWeightEl = document.getElementById('water-weight');
                    const waterAgeEl = document.getElementById('water-age');
                    const waterGenderEl = document.getElementById('water-gender');
                    const waterActivityEl = document.getElementById('water-activity');
                    const climateEl = document.getElementById('climate');
                    const healthConditionEl = document.getElementById('health-condition');
                    data.weight = waterWeightEl ? parseFloat(waterWeightEl.value) : null;
                    data.age = waterAgeEl ? parseInt(waterAgeEl.value) : null;
                    data.gender = waterGenderEl ? waterGenderEl.value : null;
                    data.activity = waterActivityEl ? waterActivityEl.value : null;
                    data.climate = climateEl ? climateEl.value : null;
                    data.healthCondition = healthConditionEl ? healthConditionEl.value : null;
                    break;
            }
            
            return data;
        }

        function validateFormData(data) {
            switch(currentCalculator) {
                case 'bmi':
                    if (!data.weight || !data.height) {
                        showError('Please enter both weight and height.');
                        return false;
                    }
                    if (isNaN(data.weight) || isNaN(data.height) || data.weight <= 0 || data.height <= 0) {
                        showError('Weight and height must be positive values.');
                        return false;
                    }
                    break;
                case 'bmr':
                case 'intake':
                    if (!data.weight || !data.height || !data.age) {
                        showError('Please fill in all required fields.');
                        return false;
                    }
                    if (isNaN(data.weight) || isNaN(data.height) || isNaN(data.age) || 
                        data.weight <= 0 || data.height <= 0 || data.age <= 0) {
                        showError('All values must be positive.');
                        return false;
                    }
                    break;
                case 'water':
                    if (!data.weight || !data.age) {
                        showError('Please fill in weight and age.');
                        return false;
                    }
                    if (isNaN(data.weight) || isNaN(data.age) || data.weight <= 0 || data.age <= 0) {
                        showError('Weight and age must be positive values.');
                        return false;
                    }
                    if (!data.gender || !data.activity || !data.climate || !data.healthCondition) {
                        showError('Please select all dropdown options.');
                        return false;
                    }
                    break;
            }
            return true;
        }

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
            
            // Hide all result sections
            document.querySelectorAll('.result-section').forEach(section => {
                section.classList.add('hidden');
            });

            // Show appropriate result section
            const currentResultSection = document.getElementById(currentCalculator + '-result');
            currentResultSection.classList.remove('hidden');

            switch(currentCalculator) {
                case 'bmi':
                    showBMIResult(data);
                    break;
                case 'bmr':
                    showBMRResult(data);
                    break;
                case 'intake':
                    showIntakeResult(data);
                    break;
                case 'water':
                    showWaterResult(data);
                    break;
            }

            resultDiv.classList.add('show');
        }

        function showBMIResult(data) {
            document.getElementById('bmiValue').textContent = data.bmi;
            document.getElementById('bmiCategory').textContent = data.category;
            document.getElementById('bmiAdvice').textContent = data.advice;

            // Update result styling based on category
            const resultDiv = document.getElementById('result');
            resultDiv.classList.remove('normal', 'underweight', 'overweight', 'obese');
            
            const categoryClass = data.category.toLowerCase().replace(' ', '');
            if (categoryClass === 'normalweight') {
                resultDiv.classList.add('normal');
            } else {
                resultDiv.classList.add(categoryClass);
            }
        }

        function showBMRResult(data) {
            document.getElementById('bmrValue').textContent = data.bmr + ' cal/day';
            document.getElementById('bmrDetail').textContent = data.detail;
            document.getElementById('bmrAdvice').textContent = data.advice;
        }

        function showIntakeResult(data) {
            document.getElementById('intakeCalories').textContent = data.calories + ' cal/day';
            document.getElementById('intakeBreakdown').innerHTML = data.breakdown;
            document.getElementById('intakeAdvice').textContent = data.advice;
        }

        function showWaterResult(data) {
            document.getElementById('waterAmount').textContent = data.amount;
            document.getElementById('waterBreakdown').innerHTML = data.breakdown;
            document.getElementById('waterAdvice').textContent = data.advice;
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

        // Initialize
        updateCalculatorSections();
        updateButtonText();
        updateLabels();
    </script>
</body>
</html>
