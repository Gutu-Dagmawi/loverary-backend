# Loverary Backend

[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red.svg?style=flat-square)](https://laravel.com/)
[![PHP Version](https://img.shields.io/badge/PHP-8.2+-777BB4.svg?style=flat-square)](https://php.net/)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-13+-4169E1?style=flat-square&logo=postgresql&logoColor=white)](https://www.postgresql.org/)

## 🚀 Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer 2.0 or higher
- Node.js 18.x or higher
- PostgreSQL 13 or higher
- NPM or Yarn package manager

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/loverary-backend.git
   cd loverary-backend
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```
   or if you prefer Yarn:
   ```bash
   yarn
   ```

4. **Environment Setup**
   - Copy `.env.example` to `.env`
   - Generate application key:
     ```bash
     php artisan key:generate
     ```
   - Configure your database connection in `.env`:
     ```
     DB_CONNECTION=pgsql
     DB_HOST=127.0.0.1
     DB_PORT=5432
     DB_DATABASE=loverary_backend
     DB_USERNAME=your_db_username
     DB_PASSWORD=your_db_password
     ```

5. **Run database migrations**
   ```bash
   php artisan migrate --seed
   ```

6. **Build assets**
   For development:
   ```bash
   npm run dev
   ```
   
   For production:
   ```bash
   npm run build
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```
   The application will be available at `http://localhost:8000`

## 🛠 Development

### Running Tests

```bash
php artisan test
```

### Code Style

This project uses Laravel Pint for code style fixing. To fix code style issues:

```bash
composer pint
```

## 📦 Dependencies

### Backend
- Laravel 12.x
- Laravel Sanctum for API authentication

### Frontend
- Vite for asset bundling
- Tailwind CSS for styling
- Axios for HTTP requests

## 🤝 Contributing

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is open-source and available under the [MIT License](LICENSE).

---

Built with ❤️ using Laravel
