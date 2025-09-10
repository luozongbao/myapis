# 🔮 Fortune Teller - ดูดวงออนไลน์

A multilingual fortune telling web application ### Web Interface Features

### Language Toggle
- **Thai (ไทย)**: Default language with traditional fortune telling style
- **Chinese (中文)**: Traditional Chinese fortune interpretation  
- **English**: Modern English fortune reading
- **Smart Language Switching**: Clears previous content and shows only selected language
- **Language-specific Fortune IDs**: Displays fortune numbering in appropriate language format

### Interactive Elements
- **Get Fortune Button**: Requests a new random fortune prediction
- **Clean Language Switching**: Removes previous fortune when changing languages
- **Responsive Design**: Adapts to different screen sizes
- **Loading Animation**: Provides smooth user feedback
- **Beautiful Gradients**: Modern visual appeal with card-based layout random fortune predictions in Thai, Chinese, and English.

## ✨ Features

- **52 Unique Fortune Predictions**: Comprehensive collection covering all aspects of life
- **Multilingual Support**: Available in Thai (ไทย), Chinese (中文), and English
- **5 Main Life Categories**: Each fortune covers:
  - 💼 **General** (ทั่วไป) - Overall life guidance
  - 💰 **Money** (เงินทอง) - Financial outlook
  - 🎯 **Work** (การงาน) - Career prospects
  - 💕 **Love** (ความรัก) - Relationship insights
  - 🏥 **Health** (สุขภาพ) - Wellness predictions
- **Additional Elements**: Some fortunes include expectations, losses, and other life aspects
- **Responsive Design**: Works perfectly on desktop and mobile devices
- **Beautiful UI**: Modern, gradient-based design with smooth animations

## 🏗️ Project Structure

```
fortune-teller/
├── index.php          # Main web interface
├── api/
│   └── index.php      # REST API endpoint
├── predictions/       # Individual JSON prediction files
│   ├── 1.json         # Fortune prediction #1
│   ├── 2.json         # Fortune prediction #2
│   ├── ...
│   └── 52.json        # Fortune prediction #52
└── README.md          # This documentation
```

## 🚀 API Usage

### Endpoint
```
GET /fortune-teller/api/
```

### Response Format
```json
{
    "success": true,
    "fortune": {
        "id": 1,
        "thai": "คำพยากรณ์ภาษาไทย...",
        "chinese": "中文预测...",
        "english": "English prediction..."
    },
    "timestamp": "2025-09-08 10:30:00",
    "total_fortunes": 52
}
```

### Example API Call
```javascript
fetch('/fortune-teller/api/')
    .then(response => response.json())
    .then(data => {
        console.log('Fortune:', data.fortune);
        console.log('Thai:', data.fortune.thai);
        console.log('Chinese:', data.fortune.chinese);
        console.log('English:', data.fortune.english);
    });
```

## 🎯 Fortune Categories Coverage

Each of the 52 fortunes is carefully crafted to include guidance on:

1. **ทั่วไป (General)** - Overall life direction and general advice
2. **เงินทอง (Money)** - Financial prospects, wealth, and economic outlook
3. **การงาน (Work)** - Career advancement, job opportunities, and professional growth
4. **ความรัก (Love)** - Romantic relationships, partnerships, and emotional connections
5. **สุขภาพ (Health)** - Physical wellness, mental health, and vitality

Additional elements may include:
- **ความคาดหวัง (Expectations)** - What to anticipate
- **ความสูญเสีย (Losses)** - Potential challenges or setbacks
- **โอกาส (Opportunities)** - New possibilities and chances
- **คำแนะนำ (Advice)** - Specific guidance and recommendations

## 🎨 Web Interface Features

### Language Toggle
- **Thai (ไทย)**: Default language with traditional fortune telling style
- **Chinese (中文)**: Traditional Chinese fortune interpretation
- **English**: Modern English fortune reading

### Interactive Elements
- **Get Fortune Button**: Requests a new random fortune prediction
- **Language Switching**: Instantly changes display language while maintaining the same fortune
- **Responsive Design**: Adapts to different screen sizes
- **Loading Animation**: Provides smooth user feedback
- **Beautiful Gradients**: Modern visual appeal with card-based layout

### Visual Design
- **Color Scheme**: Purple and pink gradients with professional contrasts
- **Typography**: Clean, readable fonts with proper hierarchy
- **Animations**: Smooth transitions and loading states
- **Mobile Friendly**: Touch-optimized for mobile devices

## 🔧 Technical Implementation

### Backend (PHP)
- **RESTful API**: Clean, standard API design with file-based predictions
- **JSON File Storage**: Each fortune stored in individual JSON files (1.json - 52.json)
- **Random Selection**: Uses PHP's `rand()` function to select fortune IDs
- **File Reading**: Dynamically reads JSON files from `/predictions/` directory
- **CORS Headers**: Allows cross-origin requests
- **Error Handling**: Proper HTTP status codes and error responses
- **Unicode Support**: Full UTF-8 support for multilingual content

### Frontend (HTML/CSS/JavaScript)
- **Modern JavaScript**: ES6+ features with async/await
- **CSS Grid/Flexbox**: Responsive layout system
- **CSS Animations**: Smooth transitions and effects
- **Progressive Enhancement**: Works without JavaScript for basic functionality
- **Accessibility**: Semantic HTML and proper contrast ratios

## 📱 Usage Instructions

1. **Access the Web Interface**: Navigate to `/fortune-teller/`
2. **Select Language**: Choose between Thai, Chinese, or English using the toggle buttons
3. **Get Fortune**: Click the "Get Fortune" button to receive a random prediction
4. **Read Your Fortune**: The prediction will appear with coverage of all 5 life categories in your selected language only
5. **Get New Fortune**: Click the button again for a different prediction
6. **Switch Languages**: Toggle between languages (this will clear the current fortune and show placeholder text until you get a new fortune)

## 🌟 Fortune Examples

### Sample Fortune (ID: 1)
- **Thai**: "วันนี้เป็นวันที่ดีสำหรับการเริ่มต้นใหม่ โชคลาภจะมาหาคุณในเรื่องเงินทอง การงานมีความก้าวหน้า ความรักกำลังจะบานบาน สุขภาพแข็งแรง แต่ระวังการใช้จ่ายฟุ่มเฟือย"
- **Chinese**: "今天是新开始的好日子，财运亨通，事业有成，爱情甜蜜，身体健康，但要注意不要浪费金钱"
- **English**: "Today is a good day for new beginnings. Fortune will come to you in financial matters, career advancement, love is blooming, health is strong, but beware of wasteful spending."

## 🔗 Integration

### Embed in Website
```html
<iframe src="/fortune-teller/" width="100%" height="600px" frameborder="0"></iframe>
```

### API Integration
```php
<?php
$fortune_api = file_get_contents('https://yoursite.com/fortune-teller/api/');
$fortune_data = json_decode($fortune_api, true);
echo $fortune_data['fortune']['thai']; // Display Thai fortune
?>
```

## 📄 License

This fortune teller application is created for educational and entertainment purposes. The fortunes are original content created to provide positive guidance across multiple life aspects.

---

**Note**: This application is for entertainment purposes only. Fortunes should not be used as the sole basis for important life decisions. Always use your own judgment and seek professional advice when needed.
