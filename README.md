# Recipe Creator 🍳

A smart recipe management web app that helps you reduce food waste by matching recipes with your pantry ingredients, featuring AI-powered recipe recommendations and interactive cooking modes.

## ✨ Features

- **🤖 AI Chat Assistant** - Get personalized recipe suggestions with OpenAI integration, save recipes with one click
- **🥘 Smart Recipe Matcher** - Find recipes based on ingredients you already have (0-5 missing ingredients filter)
- **📦 Pantry Management** - Track your ingredients with quantities and units
- **📝 Recipe Upload** - Import recipes from URLs or add manually
- **👨‍🍳 Interactive Cook Mode** - Step-by-step cooking with progress tracking and ingredient checklist
- **🔍 Live Search** - Real-time recipe filtering with AJAX
- **🌓 Light/Dark Mode** - Theme toggle with localStorage persistence
- **🔐 User Authentication** - Email/password + Google OAuth (placeholder)
- **📱 Responsive Design** - Mobile-friendly CSS Grid layout

## 🛠️ Tech Stack

**Frontend:**
- Vanilla JavaScript (ES6+) with jQuery for AJAX
- CSS3 with CSS Variables for theming
- Responsive CSS Grid layout
- Fetch API for AI chat integration

**Backend:**
- PHP 8.4+
- PostgreSQL database
- Session-based authentication
- RESTful JSON APIs

**AI Integration:**
- OpenAI GPT-3.5-turbo
- Context-aware recipe generation
- Structured recipe output format
- Rate limiting (5 requests per 2 minutes)

## 🚀 Quick Start

### Prerequisites
- PHP 8.4 or higher
- PostgreSQL (optional for local testing)

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd webapp
   ```

2. **Set up environment variables**
   Create a `.env` file in the root directory:
   ```
   OPENAI_API_KEY=sk-proj-your-key-here
   ```

3. **Initialize database** (optional - local testing works without DB)
   ```bash
   php init_db.php
   ```

4. **Start local server**
   ```bash
   php -S localhost:8888
   ```

5. **Open in browser**
   ```
   http://localhost:8888
   ```

### Local Testing Mode
The app automatically uses browser localStorage when database is unavailable, allowing full feature testing without PostgreSQL setup.

## 📖 Usage

1. **Browse Landing Page** - Learn about features and benefits
2. **Sign In** - Use demo mode for local testing
3. **Add Pantry Items** - Track your ingredients
4. **Upload Recipes** - Import or create recipes
5. **Match Recipes** - Find what you can make with your pantry
6. **Chat with AI** - Get recipe suggestions and save them instantly
7. **Start Cooking** - Follow interactive step-by-step instructions

## 🎯 Key JavaScript Features

- **7 Page-Specific Objects** - One per interactive page (HomePageState, RecipeSearch, PantryManager, etc.)
- **AJAX with jQuery** - Live recipe search and filtering
- **AJAX with Fetch** - AI chat API integration
- **Event-Driven Architecture** - DOM manipulation, form validation, dynamic updates
- **Anonymous Functions** - Error handling, suggestion buttons, recipe parsing
- **Arrow Functions** - Real-time validation, style updates, callbacks
- **Client-Side Validation** - Real-time feedback on forms
- **JSON Consumption** - Recipe data and AI responses

## 🔒 Security Features

- CSRF token protection on all forms
- Session-based authentication
- Rate limiting on AI chat (prevents API abuse)
- Server-side input validation
- SQL injection prevention (prepared statements)
- XSS prevention (htmlspecialchars)

## 📱 Responsive Design

- Mobile-first approach
- CSS Grid with auto-responsive columns
- Collapsible navigation on small screens
- Touch-friendly button sizes (44px minimum)
- Optimized for 320px - 1920px+ screens

## 🎨 Design System

- **Color Palette**: Indigo (#6366f1) & Cyan (#06b6d4)
- **Typography**: System font stack, 16px base
- **Spacing**: 4, 8, 12, 16, 24, 32, 40px scale
- **Components**: Unified button system, card patterns, form styles
- **Animations**: 200-250ms smooth transitions

## 📊 Project Structure

```
webapp/
├── api/                    # JSON API endpoints
│   ├── chat.php           # AI chat with OpenAI
│   ├── recipes.php        # Recipe data
│   └── save_ai_recipe.php # Save AI-generated recipes
├── assets/
│   ├── js/                # Page-specific JavaScript objects
│   │   ├── home.js       # HomePageState
│   │   ├── recipes.js    # RecipeSearch (jQuery + AJAX)
│   │   ├── chat.js       # ChatInterface (Fetch API)
│   │   ├── pantry.js     # PantryManager
│   │   ├── upload.js     # UploadFormValidator
│   │   ├── match.js      # MatchManager
│   │   └── cook.js       # CookingSession
│   ├── styles.css        # Main app styles (1700+ lines)
│   ├── landing.css       # Landing page styles
│   └── auth.css          # Authentication styles
├── lib/                   # Backend utilities
│   ├── auth.php          # Authentication logic
│   ├── db.php            # Database connection
│   ├── repo.php          # Data access functions
│   └── validate.php      # Input validation
├── views/                # PHP templates
│   ├── landing.php       # Public landing page
│   ├── home.php          # Dashboard
│   ├── recipes.php       # Recipe browsing
│   ├── chat.php          # AI assistant
│   └── ...
├── index.php             # Front controller & routing
└── init_db.php           # Database initialization
```

## 🤝 Authors

- Jeremy Ky
- Ashley Wu
- Shaunak Sinha

**Course:** CS 4640 - Web Application Development  
**Institution:** University of Virginia  

## 🌟 Highlights

- **Context-Aware AI** - Knows your pantry and recipes
- **One-Click Recipe Save** - AI recipes → Collection instantly
- **Smart Ingredient Matching** - Minimizes food waste
- **Interactive Cooking** - Real-time progress tracking
- **Fallback Support** - Works offline with localStorage
- **Professional UI** - Modern design with smooth animations

