# TABDIL - Currency Converter Platform

<p align="center">
  <img src="public/images/logo.png" alt="TABDIL Logo" width="200">
</p>

<p align="center">
  <strong>Real-time currency conversion for regional and international currencies</strong>
</p>

<p align="center">
  <a href="#features">Features</a> •
  <a href="#tech-stack">Tech Stack</a> •
  <a href="#installation">Installation</a> •
  <a href="#configuration">Configuration</a> •
  <a href="#usage">Usage</a> •
  <a href="#license">License</a>
</p>

---

## About TABDIL

**TABDIL** (تبديل - Arabic for "exchange") is a modern, responsive currency converter platform that enables users to convert between six key regional and international currencies with real-time exchange rates.

### Supported Currencies

- 🇸🇦 **SAR** - Saudi Riyal
- 🇾🇪 **YER** - Yemeni Rial
- 🇴🇲 **OMR** - Omani Rial
- 🇺🇸 **USD** - US Dollar
- 🇦🇪 **AED** - UAE Dirham
- 🇰🇼 **KWD** - Kuwaiti Dinar

---

## Features

### ✨ Core Features

- **🔄 Real-Time Conversion** - Instant currency conversion with live exchange rates from exchangerate-api
- **📊 Multi-Currency Comparison** - Compare one currency against all others simultaneously
- **⭐ Favorites System** - Save frequently used conversions for quick access
- **🌐 Bilingual Support** - Full Arabic and English interface with automatic RTL/LTR switching
- **📱 Fully Responsive** - Optimized for mobile, tablet, and desktop devices
- **⚡ Turbo Navigation** - Lightning-fast page transitions with Turbo.js
- **🎨 Modern UI** - Clean, gradient-based design with glassmorphism effects

### 🔐 Authentication

- **Email & Password** - Standard user authentication
- **Guest Mode** - Use converter without registration
- **Admin Panel** - Secure admin access for rate management

### 🛠️ Admin Features

- **📈 Dashboard** - User statistics and recent activity logs
- **💰 Backup Rates** - Manual rate configuration as API fallback
- **🔧 API Settings** - Configure API provider, keys, and cache duration
- **👥 User Management** - View and manage registered users
- **📝 Audit Logs** - Track all admin actions and changes

---

## Tech Stack

### Backend
- **PHP 8.2** - Server-side programming
- **Laravel 11** - Web application framework
- **MySQL 8.0+** - Database management

### Frontend
- **Laravel Blade** - Templating engine
- **TailwindCSS** - Utility-first CSS framework
- **Turbo.js** - Instant page navigation
- **Vanilla JavaScript** - Core interactivity

### Build Tools
- **Vite** - Modern frontend build tool
- **npm** - Package management

### External Services
- **exchangerate-api** - Real-time exchange rate provider

---

## Installation

### Prerequisites

Ensure you have the following installed:

- PHP >= 8.2
- Composer
- Node.js & npm
- MySQL 8.0+
- XAMPP (for local development) or similar

### Step 1: Clone the Repository

```bash
git clone https://github.com/yourusername/tabdil.git
cd tabdil
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Install Node Dependencies

```bash
npm install
```

### Step 4: Environment Configuration

1. Copy the example environment file:

```bash
cp .env.example .env
```

2. Generate application key:

```bash
php artisan key:generate
```

### Step 5: Database Setup

1. Create a MySQL database named `change_currency`

2. Update `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=change_currency
DB_USERNAME=root
DB_PASSWORD=
```

3. Run migrations:

```bash
php artisan migrate
```

### Step 6: API Configuration

1. Get a free API key from [exchangerate-api.com](https://www.exchangerate-api.com/)

2. Add to your database via phpMyAdmin or MySQL:

```sql
INSERT INTO api_settings (key, value) VALUES ('api_key', 'your_api_key_here');
INSERT INTO api_settings (key, value) VALUES ('api_provider', 'exchangerate-api');
INSERT INTO api_settings (key, value) VALUES ('cache_duration', '60');
INSERT INTO api_settings (key, value) VALUES ('api_enabled', 'true');
```

Or use the admin panel after seeding an admin user.

### Step 7: Seed Admin User (Optional)

Create an admin account manually in the `users` table:

```sql
INSERT INTO users (name, email, password, email_verified_at) 
VALUES ('Admin', 'admin@tabdil.com', '$2y$12$[bcrypt-hash]', NOW());
```

**Note:** Use Laravel Tinker to generate bcrypt hash:

```bash
php artisan tinker
>>> bcrypt('your-password')
```

---

## Configuration

### API Settings

Configure these settings through the admin panel (`/admin/api-settings`):

| Key | Description | Example |
|-----|-------------|---------|
| `api_provider` | API provider name | `exchangerate-api` |
| `api_key` | Your API key | `abc123...` |
| `cache_duration` | Cache time in minutes | `60` |
| `api_enabled` | Toggle API on/off | `true` or `false` |

### Backup Rates

Backup rates are now managed via a dedicated table `backup_rates` and can be configured in the admin panel (`/admin/backup-rates`).

The system stores distinct **Buy** and **Sell** rates for each currency against YER:

| Currency | Buy Rate (Foreign → YER) | Sell Rate (YER → Foreign) |
|----------|--------------------------|---------------------------|
| SAR | 425.00 | 428.00 |
| USD | 1617.00 | 1632.00 |

---

## Usage

### Development Server

Start the Laravel development server:

```bash
php artisan serve
```

Start Vite for asset compilation:

```bash
npm run dev
```

Access the application at: [http://localhost:8000](http://localhost:8000)

### Production Build

Build assets for production:

```bash
npm run build
```

### Admin Panel Access

Navigate to: [http://localhost:8000/admin/dashboard](http://localhost:8000/admin/dashboard)

Default credentials (if you seeded):
- Email: `admin@tabdil.com`
- Password: `admin123`

---

## Project Structure

```
change_currency/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminController.php
│   │   │   ├── CurrencyController.php
│   │   │   └── FavoriteController.php
│   │   └── Middleware/
│   ├── Models/
│   │   ├── BackupRate.php
│   │   ├── ExchangeRate.php
│   │   └── User.php
│   └── Services/
│       └── CurrencyConversionService.php
├── database/
│   ├── migrations/
│   │   └── ..._create_backup_rates_table.php
│   └── seeders/
│       └── BackupRatesSeeder.php
├── public/
├── resources/
│   ├── js/
│   │   └── app.js
│   ├── views/
│   │   ├── admin/
│   │   │   ├── api-settings.blade.php
│   │   │   ├── backup-rates.blade.php
│   │   │   └── dashboard.blade.php
│   │   ├── currency/
│   │   └── layouts/
├── routes/
│   └── web.php
├── .env
└── README.md
```

---

## Key Features Explained

### Buy/Sell Rate Logic

TABDIL uses a hybrid rate calculation system:

1.  **YER Conversions:**
    *   **Foreign → YER:** Uses the **Buy Rate** from `backup_rates` table.
    *   **YER → Foreign:** Uses the **Sell Rate** from `backup_rates` table.
    *   *Note: These rates are manually set by the admin.*

2.  **Cross-Currency Conversions (e.g., USD → SAR):**
    *   Primary: Uses real-time rates from the external API.
    *   Fallback: Uses historical rates if API is unavailable.

### Mobile Responsiveness

The platform features a highly adaptive UI:
- **Responsive Tables:** Data tables automatically transform into elegant **Cards** on mobile devices for better readability.
- **Smart Text Handling:** Large numbers automatically wrap or resize to fit small screens without breaking the layout.
- **Touch-Friendly:** Buttons and inputs are sized for touch interaction.

### Turbo.js Integration

The platform uses Turbo.js for:
- Instant page navigation without full reloads
- Automatic currency converter re-initialization
- Dynamic RTL/LTR switching on language change
- Improved perceived performance

### Fallback Mechanism

If the exchangerate-api fails or is disabled:
1. System seamlessly switches to stored backup rates for YER.
2. Cross-currency conversions may use cached or historical data.
3. Admin can test API connection status directly from the dashboard.

---

## Language Support

TABDIL supports full bilingual functionality:

### Switching Languages

Users can switch between Arabic and English via the navigation menu. The interface automatically:
- Translates all text
- Switches text direction (RTL ↔ LTR)
- Updates date/time formats
- Maintains user preference

### Adding Translations

Edit language files in `resources/lang/`:

- **English:** `resources/lang/en/messages.php`
- **Arabic:** `resources/lang/ar/messages.php`

---

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## Troubleshooting

### Common Issues

**Database Connection Error:**
```
SQLSTATE[HY000] [2002] No connection could be made
```
**Solution:** Ensure MySQL is running in XAMPP Control Panel.

**API Rate Limit Exceeded:**
**Solution:** System automatically switches to backup rates. Check `api_settings` table.

**Turbo Navigation Issues:**
**Solution:** Clear browser cache and ensure `resources/js/app.js` is properly compiled.

**Missing Translations:**
**Solution:** Run `php artisan cache:clear` and check language files.

---

## Performance Optimization

### Caching

- API responses cached based on `cache_duration` setting (default: 60 minutes)
- Laravel cache used for sessions and application data

### Asset Optimization

Production build minifies and optimizes:
```bash
npm run build
```

---

## Security

- All rates validated before storage
- Admin routes protected by authentication middleware
- CSRF protection on all forms
- SQL injection prevention via Eloquent ORM
- XSS protection via Blade templating

---

## Deployment

### Recommended Hosting

**Primary:** Laravel Cloud (planned)

**Alternatives:**
- VPS (DigitalOcean, Linode, AWS)
- Shared hosting with PHP 8.2+
- Docker containers

### Pre-Deployment Checklist

- [ ] Update `.env` with production database credentials
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `npm run build`
- [ ] Configure web server (Apache/Nginx)
- [ ] Set up SSL certificate
- [ ] Configure backup strategy

---

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## Credits

### Built With

- [Laravel](https://laravel.com) - The PHP Framework
- [TailwindCSS](https://tailwindcss.com) - CSS Framework
- [Turbo](https://turbo.hotwired.dev) - Navigation Library
- [exchangerate-api](https://www.exchangerate-api.com) - Currency Data Provider

### Author

**Mohammed512** - [GitHub Profile](https://github.com/Mohammed512)

---

## Support

For support, questions, or feature requests:
- Open an issue on GitHub
- Contact: support@tabdil.com (if applicable)

---

**Made with ❤️ for currency conversion**

---

**Version:** 2.0  
**Last Updated:** December 3, 2025
