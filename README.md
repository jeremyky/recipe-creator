# Pantry Pilot 🍳

**Live Demo:** [https://cs4640.cs.virginia.edu/juh7hc/index.php?action=home](https://cs4640.cs.virginia.edu/juh7hc/index.php?action=home)

A full-stack web application that helps reduce food waste by intelligently matching recipes with pantry ingredients. Built with PHP, PostgreSQL, and JavaScript, featuring AI-powered recipe recommendations and an interactive cooking experience.

## 📋 Table of Contents

- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Current Status](#-current-status)
- [Quick Start](#-quick-start)
- [Usage Guide](#-usage-guide)
- [Project Structure](#-project-structure)
- [Deployment](#-deployment)
- [Authors](#-authors)

## ✨ Features

### 🤖 AI-Powered Recipe Assistant
- **OpenAI Integration**: Get personalized recipe suggestions using GPT-3.5-turbo
- **Context-Aware**: AI has access to your pantry inventory and saved recipes
- **One-Click Save**: Instantly save AI-generated recipes to your collection
- **Rate Limited**: 5 messages per 2 minutes to prevent API abuse

### 🥘 Smart Recipe Matching
- **Ingredient-Based Matching**: Find recipes based on what you have in your pantry
- **Flexible Filtering**: Adjust "max missing ingredients" from 0-5
- **Phonetic Matching**: Uses SOUNDEX algorithm for intelligent ingredient matching
- **Match Score Display**: Visual percentage showing how well recipes match your pantry

### 📦 Pantry Management
- **Track Ingredients**: Add ingredients with quantities and units (lbs, oz, cups, etc.)
- **Search & Filter**: Real-time search and sorting (Name, Recently Added, Quantity)
- **Low Stock Alerts**: Visual indicators for items running low
- **Quick Add**: Autocomplete with common ingredient suggestions
- **Bulk Actions**: Update or remove multiple items at once

### 📝 Recipe Management
- **Upload Recipes**: Import from URLs or add manually
- **Recipe Browser**: Browse all saved recipes with search and filters
- **Recipe Details**: View full recipe with ingredients, steps, and images
- **Favorites**: Mark and filter favorite recipes
- **Edit & Delete**: Full CRUD operations on recipes

### 👨‍🍳 Interactive Cooking Mode
- **Step-by-Step Guide**: Navigate through cooking instructions
- **Ingredient Checklist**: Check off ingredients as you use them
- **Progress Tracking**: Visual progress indicators
- **Session Management**: Resume or restart cooking sessions

### 🎨 User Experience
- **Light/Dark Mode**: Toggle between themes with localStorage persistence
- **Responsive Design**: Mobile-first design works on all devices
- **PWA Support**: Progressive Web App with offline capabilities
- **Accessibility**: ARIA labels, keyboard navigation, screen reader support

### 🔐 Authentication & Security
- **Email/Password**: Secure account creation and login
- **Google OAuth 2.0**: One-click sign in with Google
- **CSRF Protection**: All forms protected with tokens
- **Session Management**: Secure server-side sessions
- **SQL Injection Prevention**: Prepared statements throughout
- **XSS Prevention**: Input sanitization and output escaping

## 🛠️ Tech Stack

### Frontend
- **HTML5** - Semantic markup
- **CSS3** - Custom properties, Grid, Flexbox, animations
- **JavaScript (ES6+)** - Vanilla JS with modern features
- **jQuery** - AJAX requests and DOM manipulation
- **Fetch API** - AI chat integration

### Backend
- **PHP 8.4+** - Server-side logic and routing
- **PostgreSQL** - Relational database
- **PDO** - Database abstraction layer
- **Sessions** - User authentication

### AI & APIs
- **OpenAI GPT-3.5-turbo** - Recipe generation and suggestions
- **Google OAuth 2.0** - Social authentication

### Infrastructure
- **Apache** - Web server
- **SFTP** - File deployment
- **Environment Variables** - Secure configuration

## 📊 Current Status

### ✅ Completed Features
- [x] User authentication (email/password + Google OAuth)
- [x] Pantry management with full CRUD operations
- [x] Recipe upload and management
- [x] AI-powered recipe chat assistant
- [x] Smart recipe matching algorithm
- [x] Interactive cooking mode
- [x] Light/dark theme toggle
- [x] Responsive mobile design
- [x] PWA manifest and mobile optimization
- [x] Search and filtering across all pages
- [x] Real-time form validation
- [x] Database schema and migrations
- [x] Production deployment

### 🚀 Production Ready
- Fully functional on production server
- Database connection and migrations
- Environment variable configuration
- Error handling and logging
- Security best practices implemented

## 🚀 Quick Start

### Prerequisites
- PHP 8.4 or higher
- PostgreSQL 12+ (for production)
- Web server (Apache/Nginx) or PHP built-in server

### Local Development Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd webapp
   ```

2. **Set up environment variables**
   Create a `.env` file in the root directory:
   ```env
   DB_HOST=localhost
   DB_PORT=5432
   DB_NAME=your_database
   DB_USER=your_username
   DB_PASSWORD=your_password
   OPENAI_API_KEY=sk-proj-your-key-here
   GOOGLE_CLIENT_ID=your-client-id
   GOOGLE_CLIENT_SECRET=your-client-secret
   GOOGLE_REDIRECT_URI=http://localhost:8000/index.php?action=google_callback
   ```

3. **Initialize database**
   ```bash
   php init_db.php
   php populate_sample_data.php
   ```

4. **Start local development server**
   ```bash
   php -S localhost:8000
   ```

5. **Open in browser**
   ```
   http://localhost:8000
   ```

### Production Deployment

1. **Upload files via SFTP**
   ```bash
   sftp juh7hc@cs4640.cs.virginia.edu
   cd public_html
   put -r api assets lib views
   put .env .htaccess index.php init_db.php manifest.json
   ```

2. **Configure database**
   - Update `.env` with production database credentials
   - Run `init_db.php` to create tables

3. **Set file permissions**
   ```bash
   chmod 644 .env
   chmod 755 public_html
   ```

## 📖 Usage Guide

### Getting Started

1. **Sign Up / Sign In**
   - Create an account with email/password
   - Or use Google OAuth for quick access
   - Demo mode available for testing

2. **Add Items to Pantry**
   - Navigate to "Pantry" page
   - Use the "Add Ingredient" form
   - Enter ingredient name, quantity, and unit
   - Click suggested ingredients for quick add

3. **Upload or Generate Recipes**
   - **Upload**: Go to "Upload" page and add recipe manually
   - **AI Generate**: Use "Chat" page to ask AI for recipes
   - AI recipes can be saved with one click

4. **Match Recipes to Pantry**
   - Go to "Match" page
   - Adjust "Max Missing Ingredients" slider (0-5)
   - Browse matched recipes sorted by match percentage
   - View full recipe details

5. **Start Cooking**
   - Go to "Cook" page and select a recipe
   - Follow step-by-step instructions
   - Check off ingredients and steps as you complete them
   - Track your progress visually

### Key Features Explained

#### AI Chat Assistant
- Ask questions like "Give me a recipe using chicken and rice"
- AI has access to your pantry, so suggestions are personalized
- Rate limit: 5 messages per 2 minutes
- Save recipes directly from chat responses

#### Recipe Matching Algorithm
- Compares recipe ingredients with pantry items
- Uses phonetic matching (SOUNDEX) for variations
- Calculates match percentage based on available ingredients
- Filters by maximum missing ingredients

#### Pantry Management
- Search ingredients in real-time
- Sort by Name, Recently Added, or Quantity
- Filter by low stock items
- Update quantities inline
- Bulk remove operations

## 📁 Project Structure

```
webapp/
├── api/                          # REST API endpoints
│   ├── chat.php                 # AI chat with OpenAI
│   ├── recipes.php              # Recipe data endpoints
│   ├── save_ai_recipe.php      # Save AI-generated recipes
│   └── toggle_favorite.php     # Favorite toggle
├── assets/
│   ├── js/                      # JavaScript modules
│   │   ├── auth.js             # Authentication logic
│   │   ├── chat.js             # AI chat interface
│   │   ├── cook.js             # Cooking session
│   │   ├── filters.js          # Filter system
│   │   ├── home.js             # Homepage state
│   │   ├── match.js            # Recipe matching
│   │   ├── mobile-menu.js      # Mobile navigation
│   │   ├── pantry.js           # Pantry management
│   │   ├── recipes.js          # Recipe search & filters
│   │   ├── theme.js            # Theme toggle
│   │   └── upload.js           # Recipe upload
│   ├── styles.css              # Main application styles
│   ├── filters.css             # Filter component styles
│   ├── landing.css             # Landing page styles
│   ├── favicon.svg             # Site favicon
│   └── logo-icon.svg           # Brand logo
├── lib/                         # Backend utilities
│   ├── auth.php                # Authentication functions
│   ├── db.php                  # Database connection
│   ├── repo.php                # Data access layer
│   ├── session.php             # Session management
│   ├── util.php                # Helper functions
│   └── validate.php            # Input validation
├── views/                       # PHP templates
│   ├── layout.header.php       # Site header
│   ├── layout.footer.php       # Site footer
│   ├── home.php                # Dashboard
│   ├── recipes.php             # Recipe browser
│   ├── recipe_detail.php       # Recipe details
│   ├── recipe_edit.php         # Recipe editor
│   ├── chat.php                # AI assistant
│   ├── pantry.php              # Pantry management
│   ├── match.php               # Recipe matching
│   ├── cook.php                # Cooking selection
│   ├── cook_session.php        # Active cooking
│   ├── upload.php              # Recipe upload
│   ├── login.php               # Sign in
│   ├── signup.php              # Sign up
│   └── landing.php             # Public landing
├── index.php                    # Front controller & routing
├── init_db.php                  # Database initialization
├── populate_sample_data.php     # Sample data seeder
├── fix_user_sequence.php        # Database utility
├── manifest.json                # PWA manifest
├── .htaccess                    # Apache configuration
└── .env                         # Environment variables (gitignored)
```

## 🌐 Deployment

### Live Site
**URL:** [https://cs4640.cs.virginia.edu/juh7hc/index.php?action=home](https://cs4640.cs.virginia.edu/juh7hc/index.php?action=home)

### Deployment Process

1. **Prepare files**
   - Ensure `.env` has production credentials
   - Verify all dependencies are included

2. **Upload via SFTP**
   ```bash
   sftp juh7hc@cs4640.cs.virginia.edu
   cd public_html
   # Upload directories and files
   ```

3. **Verify deployment**
   - Check database connection
   - Test authentication
   - Verify AI chat functionality

## 🔒 Security Features

- **CSRF Protection**: All forms protected with tokens
- **SQL Injection Prevention**: Prepared statements throughout
- **XSS Prevention**: Input sanitization and output escaping
- **Session Security**: Secure cookie settings
- **Rate Limiting**: AI chat rate limits prevent abuse
- **Environment Variables**: Sensitive data stored securely
- **Input Validation**: Server-side validation on all inputs

## 📱 Mobile Optimization

- **Responsive Design**: Mobile-first CSS approach
- **Touch-Friendly**: 44px minimum touch targets
- **PWA Support**: Installable as mobile app
- **Mobile Navigation**: Hamburger menu with slide-out drawer
- **Optimized Images**: Efficient image loading
- **Viewport Meta Tags**: Proper mobile rendering

## 🎨 Design System

- **Color Palette**: Indigo (#6366f1) primary, Cyan (#06b6d4) accent
- **Typography**: System font stack, 16px base size
- **Spacing**: Consistent 4px grid system
- **Components**: Unified button, card, and form styles
- **Animations**: Smooth 200-300ms transitions
- **Dark Mode**: Full theme support

## 🤝 Authors

- **Jeremy Ky**
- **Ashley Wu**
- **Shaunak Sinha**

**Course:** CS 4640 - Web Application Development  
**Institution:** University of Virginia  
**Semester:** Fall 2024

## 📄 License

This project is part of a course assignment at the University of Virginia.

## 🌟 Highlights

- **Full-Stack Application**: Complete PHP backend with PostgreSQL database
- **AI Integration**: OpenAI GPT-3.5-turbo for intelligent recipe suggestions
- **Real-World Problem**: Addresses food waste through smart matching
- **Production Ready**: Deployed and functional on live server
- **Modern UX**: Responsive design with dark mode and PWA support
- **Security Best Practices**: CSRF, XSS, SQL injection prevention

## 🔗 Links

- **Live Demo**: [https://cs4640.cs.virginia.edu/juh7hc/index.php?action=home](https://cs4640.cs.virginia.edu/juh7hc/index.php?action=home)
- **Repository**: [GitHub Repository URL]

---

**Built with ❤️ by Jeremy Ky, Ashley Wu, and Shaunak Sinha**
