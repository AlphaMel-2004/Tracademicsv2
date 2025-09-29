x`# TracAdemics v3

**Faculty Compliance Management System for Academic Institutions**

TracAdemics is a comprehensive Laravel-based web application designed to streamline faculty compliance tracking, curriculum management, and academic reporting for educational institutions. The system provides role-based access control, document management, and detailed reporting capabilities.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Environment Configuration](#environment-configuration)
- [Database Setup](#database-setup)
- [Default Credentials](#default-credentials)
- [Configuration Options](#configuration-options)
- [Usage](#usage)
- [Development](#development)
- [Contributing](#contributing)
- [License](#license)

## Features

- **Role-Based Access Control**: MIS, VPAA, Dean, Program Head, and Faculty roles
- **Faculty Management**: Track compliance, assignments, and documentation
- **Curriculum Management**: Organize subjects, programs, and semester schedules
- **Document Management**: Upload and manage academic documents with PDF generation
- **Reporting Dashboard**: Comprehensive analytics and compliance reports
- **Email Notifications**: Automated submission notifications
- **Responsive Design**: Bootstrap 5 with modern UI/UX
- **SEO Optimized**: Meta tags, structured data, and performance optimization

## Requirements

- **PHP**: ^8.2
- **Composer**: Latest version
- **Node.js**: ^18.0 (for asset compilation)
- **NPM**: Latest version
- **MySQL**: ^8.0 or MariaDB ^10.3
- **Web Server**: Apache or Nginx

### Development Environment
- **XAMPP** (recommended for Windows)
- **Laravel Valet** (for macOS)
- **Docker** (cross-platform)

## Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/your-organization/your-repository.git
   cd your-project-directory
   ```

2. **Install PHP Dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js Dependencies**
   ```bash
   npm install
   ```

4. **Copy Environment File**
   ```bash
   copy .env.example .env
   ```

5. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

6. **Build Assets**
   ```bash
   npm run build
   ```

## Environment Configuration

Configure the following variables in your `.env` file:

### Application Settings
```env
APP_NAME=YourAppName
APP_ENV=production
APP_KEY=[Generate using: php artisan key:generate]
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
```

### Database Configuration
```env
DB_CONNECTION=mysql
DB_HOST=your_database_host
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_secure_password
```

### Session Configuration
```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
```

### Mail Configuration (Optional)
```env
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email_username
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Cache & Queue Configuration
```env
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local
```

## Database Setup

1. **Create Database**
   ```sql
   CREATE DATABASE your_database_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. **Run Migrations**
   ```bash
   php artisan migrate
   ```

3. **Seed Database**
   ```bash
   php artisan db:seed
   ```

4. **Link Storage** (for file uploads)
   ```bash
   php artisan storage:link
   ```

### Migration Commands
- **Fresh Migration**: `php artisan migrate:fresh --seed`
- **Rollback**: `php artisan migrate:rollback`
- **Reset**: `php artisan migrate:reset`
- **Status**: `php artisan migrate:status`

## Initial Setup

After running the database seeders, the system will create default user accounts for each role:

### User Roles Available
- **MIS Administrator**: Full system access and configuration
- **VPAA**: Academic oversight and institutional reporting
- **Dean**: Department-level management and oversight
- **Program Head**: Program-specific faculty and curriculum management
- **Faculty**: Individual compliance and document management

### Security Configuration
1. **Change all default passwords** immediately after first deployment
2. **Configure strong password policies** in your institution's security settings
3. **Set up proper email verification** for new user registrations
4. **Enable two-factor authentication** if required by your institution
5. **Regular security audits** of user accounts and permissions

> **🔒 Security Notice**: Never use default credentials in production. Always implement proper authentication and authorization policies according to your institution's security requirements.

## Configuration Options

### Key Configuration Files

#### `config/app.php`
- **timezone**: Set to your local timezone (default: 'UTC')
- **locale**: Application language (default: 'en')
- **fallback_locale**: Fallback language (default: 'en')

#### `config/database.php`
- **connections**: Database connection settings
- **migrations**: Migration table configuration

#### `config/filesystems.php`
- **default**: Default storage disk
- **disks**: Configure local, public, and cloud storage

#### `config/mail.php`
- **default**: Default mail driver
- **mailers**: SMTP, log, and other mail configurations

#### `config/session.php`
- **driver**: Session storage method (database recommended)
- **lifetime**: Session timeout in minutes
- **encrypt**: Enable session encryption

### Custom Configuration

#### Academic Year Settings
Modify seeded data in `database/seeders/SemesterSeeder.php` to match your academic calendar.

#### Department Structure
Update `database/seeders/DepartmentSeeder.php` and `database/seeders/ProgramSeeder.php` to reflect your institution's structure.

#### Document Types
Configure allowed document types in `database/seeders/DocumentTypeSeeder.php`.

## Usage

### Starting the Development Server
```bash
php artisan serve
```
Access the application at: `http://localhost:8000`

### Asset Development
```bash
# Watch for changes during development
npm run dev

# Build for production
npm run build
```

### Maintenance Commands
```bash
# Clear all caches
php artisan optimize:clear

# Cache configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Generate sitemap
php artisan route:list
```

### Queue Workers (if using queues)
```bash
php artisan queue:work
```

## Development

### Code Style
The project follows Laravel coding standards with PHP CS Fixer configuration.

```bash
# Run code formatting
composer run-script post-autoload-dump
```

### Testing
```bash
# Run feature tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
```

### Debug Tools
- **Laravel Debugbar**: Available in development mode
- **Log Viewer**: Check `storage/logs/laravel.log`
- **Tinker**: `php artisan tinker` for interactive debugging

### Database Backup
```bash
# Create database backup
mysqldump -u your_db_user -p your_database_name > backup_$(date +%Y%m%d_%H%M%S).sql
```

## Architecture

### MVC Structure
- **Models**: Located in `app/Models/` - Handle data logic
- **Views**: Located in `resources/views/` - Blade templates
- **Controllers**: Located in `app/Http/Controllers/` - Handle requests

### Key Models
- **User**: Authentication and user management
- **Role**: Role-based permissions
- **Department**: Academic departments
- **Program**: Academic programs within departments
- **Subject**: Individual courses
- **Curriculum**: Program curriculum structure
- **FacultyAssignment**: Faculty-subject assignments
- **Semester**: Academic periods

### Security Features
- **CSRF Protection**: All forms protected
- **SQL Injection Prevention**: Eloquent ORM protection
- **XSS Protection**: Blade template escaping
- **Authentication**: Laravel Sanctum
- **Input Validation**: Form request validation

## Troubleshooting

### Common Issues

1. **Permission Errors**
   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

2. **Composer Memory Issues**
   ```bash
   php -d memory_limit=-1 composer install
   ```

3. **Node.js Build Errors**
   ```bash
   npm cache clean --force
   rm -rf node_modules package-lock.json
   npm install
   ```

4. **Database Connection Issues**
   - Verify MySQL service is running
   - Check database credentials in `.env`
   - Ensure database exists

### Performance Optimization

1. **Enable Caching**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

2. **Database Optimization**
   ```bash
   php artisan db:show
   php artisan migrate:status
   ```

3. **Asset Optimization**
   ```bash
   npm run build
   ```

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support and questions:
- **Documentation**: Refer to Laravel official documentation for framework-specific issues
- **Issues**: Report bugs through the project's issue tracking system
- **Community**: Consult Laravel community resources and forums
- **Professional Support**: Contact your system administrator or IT department

---

**TracAdemics v3** - Empowering Academic Excellence Through Technology

## Technical Framework

This application is built with **Laravel**, a robust PHP framework that provides:
- Clean, expressive syntax and rapid development capabilities
- Built-in security features and authentication systems
- Comprehensive database management with Eloquent ORM
- Modern front-end integration with Vite and Bootstrap
- Scalable architecture suitable for educational institutions

## Additional Resources

- **Laravel Documentation**: [https://laravel.com/docs](https://laravel.com/docs)
- **Bootstrap Documentation**: [https://getbootstrap.com/docs](https://getbootstrap.com/docs)
- **PHP Best Practices**: Follow PSR standards and modern PHP conventions

## System Requirements

Ensure your hosting environment meets all technical requirements listed in the Requirements section above for optimal performance and security.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
#   T r a c a d e m i c s v 3 
 
 #   T r a c a d e m i c s v 2 
 
 