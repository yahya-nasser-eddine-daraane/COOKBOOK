# 🍽️ COOKBOOK

A modern recipe management system built with Laravel. This application allows users to browse, create, and manage their favorite recipes with a clean and intuitive user interface.

## ✨ Features

- **Recipe Management:** Easily create, edit, and delete your recipes.
- **Browse Recipes:** Discover new recipes with a beautiful presentation.
- **Authentication:** Secure user login and registration to manage personal recipes.
- **Modern UI:** Built with clean HTML/CSS/JavaScript.

## 🚀 Getting Started

Follow these instructions to get a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

- PHP >= 8.2
- Composer
- Node.js & npm (for modern frontend tooling)
- MySQL or compatible database

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/yahya-nasser-eddine-daraane/COOKBOOK.git
   cd COOKBOOK
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies:**
   ```bash
   npm install
   ```

4. **Environment Setup:**
   Copy the `.env.example` file to `.env` and configure your database settings.
   ```bash
   cp .env.example .env
   ```

5. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

6. **Run Migrations and Seeders:**
   Deploy the database schema and initial data.
   ```bash
   php artisan migrate --seed
   ```

7. **Compile Assets:**
   ```bash
   npm run build
   ```

8. **Serve the Application:**
   ```bash
   php artisan serve
   ```
   *The application will be accessible at `http://localhost:8000`.*

## 🛠️ Built With

- [Laravel](https://laravel.com/) - The PHP framework for web artisans
- [Vanilla CSS/JS] - Frontend styling and interaction

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
