# 🏛️ Gemeente Klachtensysteem

Een moderne, veilige en gebruiksvriendelijke webapplicatie voor het beheren van klachten en meldingen van burgers aan de gemeente.

## 📋 Overzicht

Het Gemeente Klachtensysteem is een Laravel-gebaseerde applicatie die het voor burgers mogelijk maakt om eenvoudig klachten in te dienen en deze te volgen, terwijl medewerkers en admins deze efficiënt kunnen beheren.

### ✨ Hoofdfunctionaliteiten

- 📝 **Klacht Indienen** - Eenvoudig formulier voor burgers
- 👥 **Multi-user Systeem** - Burgers, medewerkers, en admins
- 📊 **Admin Dashboard** - Overzicht en statistieken
- 🔒 **Beveiliging** - Laravel Breeze + Spatie Permission
- 🛡️ **AVG Compliant** - Privacy logging en data retention
- 🤖 **AI Chatbot** - Geïntegreerde gebruikersondersteuning
- 📱 **Mobile Responsive** - Werkt perfect op alle apparaten
- 📎 **File Uploads** - Foto's en documenten bijvoegen
- ✉️ **Email Notificaties** - Automatische communicatie
- 🔍 **Zoeken & Filteren** - Geavanceerde zoekfunctionaliteit

## 🚀 Quick Start

### Prerequisites
- PHP 8.3+
- Composer
- Node.js & NPM
- SQLite (development) / PostgreSQL (production)

### Installation

```bash
# Clone repository
git clone [repository-url]
cd gemeente

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
touch database/database.sqlite
php artisan migrate --seed

# Build assets
npm run build

# Start development server
composer dev
```

### 👤 Admin Login

Voor toegang tot het admin dashboard:

**Email:** `admin@gemeente.nl`  
**Wachtwoord:** `admin123`

📖 **Volledige credentials en setup:** Zie [ADMIN_CREDENTIALS.md](ADMIN_CREDENTIALS.md)

### Development Commands

```bash
# Run all tests
composer test

# Code style formatting
./vendor/bin/pint

# Static analysis
composer phpstan

# Complete code quality check
composer code-quality

# Start development environment
composer dev
```

## 🏗️ Architecture

### Tech Stack
- **Backend**: Laravel 12.29.0, PHP 8.3
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js  
- **Database**: SQLite (dev), PostgreSQL (prod)
- **Authentication**: Laravel Breeze
- **Authorization**: Spatie Permission
- **Testing**: Pest Framework
- **Static Analysis**: PHPStan/Larastan
- **Code Style**: Laravel Pint

### Key Components
- **Models**: User, Complaint, Attachment, Note, StatusHistory
- **Controllers**: Admin, API, and Web controllers
- **Services**: ChatbotService, PrivacyLogger
- **Policies**: AdminPolicy, ComplaintPolicy
- **Middleware**: Admin, NoIndex, LogAdminAccess

## 📊 Code Quality

### Static Analysis
- **PHPStan Level**: 6/9 (good balance of strictness)
- **Current Issues**: 121 baselined (no new errors allowed)
- **Coverage**: 93 files analyzed
- **Integration**: Automated in CI/CD pipeline

### Testing Coverage
- **Test Suite**: 25 passing tests
- **Coverage**: Unit, Feature, and Browser tests
- **Framework**: Pest (modern PHP testing)
- **CI/CD**: GitHub Actions workflow

### Code Standards
- **Style**: PSR-12 via Laravel Pint
- **Analysis**: PHPStan/Larastan for type safety
- **Quality Gates**: No new errors in PRs
- **Documentation**: Comprehensive inline docs

## 📚 Documentation

### Project Documentation
- 📊 [ERD Document](ERD_DOCUMENT.md) - Database structure
- 🏛️ [Class Diagram](KLASSENDIAGRAM_DOCUMENT.md) - Application architecture  
- 👥 [Use Case Diagram](USE_CASE_DIAGRAM_DOCUMENT.md) - User interactions
- 🎉 [Definition of Fun](DEFINITION_OF_FUN.md) - Team collaboration
- ✅ [Definition of Done](DEFINITION_OF_DONE.md) - Quality criteria
- 📅 [Project Planning](PROJECT_PLANNING_DOCUMENT.md) - Timeline & milestones

### Technical Documentation
- 🔍 [Static Analysis](STATIC_ANALYSIS.md) - PHPStan setup and guidelines
- 🔒 [Security](SECURITY.md) - Security implementation details
- 🤖 [Chatbot Implementation](CHATBOT_IMPLEMENTATION.md) - AI integration
- 📊 [Dashboard Documentation](DASHBOARD_DOCUMENTATION.md) - Admin dashboard with interactive map

## 🛡️ Security & Privacy

### Security Features
- ✅ **Authentication**: Secure login via Laravel Breeze
- ✅ **Authorization**: Role-based access control (RBAC)
- ✅ **Input Validation**: XSS, CSRF, SQL injection prevention
- ✅ **Privacy Logging**: AVG-compliant audit trails
- ✅ **Data Retention**: Automatic cleanup of old data
- ✅ **Security Headers**: CSP, HSTS, X-Frame-Options

### Compliance
- 🔒 **AVG/GDPR**: Complete data protection compliance
- 📝 **Audit Trails**: All actions logged without PII
- 🗑️ **Right to Deletion**: Automated data purging
- 🔐 **Encryption**: Data encrypted at rest and in transit

## 🔄 Development Workflow

### Branching Strategy
- `main` - Production-ready code
- `develop` - Integration branch for features
- `feature/*` - Individual feature development

### Code Quality Process
1. **Development**: Write code following PSR-12 standards
2. **Testing**: Ensure all tests pass (`composer test`)
3. **Style**: Format code with Pint (`./vendor/bin/pint`)
4. **Analysis**: Run PHPStan (`composer phpstan`)
5. **Review**: Create PR with comprehensive description
6. **CI/CD**: Automated quality checks must pass
7. **Merge**: Squash and merge after approval

## 🤝 Contributing

### Getting Started
1. Fork the repository
2. Create a feature branch
3. Make your changes following our standards
4. Ensure all quality checks pass
5. Submit a pull request with clear description

### Code Standards
- Follow PSR-12 coding standards
- Write comprehensive tests for new features
- Add type hints and PHPDoc blocks
- No new PHPStan errors allowed
- Update documentation as needed

### Commit Guidelines
```bash
# Good commit messages
feat: add complaint status filtering
fix: resolve XSS vulnerability in uploads
docs: update API documentation
refactor: improve chatbot response logic
```

## 🐛 Issue Reporting

### Bug Reports
Please include:
- Steps to reproduce
- Expected vs actual behavior
- Environment details (PHP version, etc.)
- Screenshots if applicable

### Feature Requests
Please describe:
- Use case and business value
- Proposed implementation approach
- Impact on existing functionality
- Acceptance criteria

## 📈 Performance & Monitoring

### Current Metrics
- **Response Time**: <150ms average
- **Uptime**: 99.9% target
- **Test Coverage**: 85%+ for new code
- **Code Quality**: PHPStan level 6 compliance

### Monitoring
- **Application Performance**: Built-in Laravel monitoring
- **Error Tracking**: Comprehensive logging system
- **Security Monitoring**: Automated vulnerability scanning
- **Usage Analytics**: Privacy-compliant usage metrics

## 🔧 Troubleshooting

### Common Issues

**Tests failing?**
```bash
php artisan config:clear
php artisan migrate:fresh --seed --env=testing
composer test
```

**PHPStan errors?**
```bash
# Check for new errors
composer phpstan

# Update baseline if needed (careful!)
composer phpstan-baseline
```

**Styling issues?**
```bash
# Fix all style issues
./vendor/bin/pint

# Check style without fixing
./vendor/bin/pint --test
```

## 📞 Support & Contact

- **Technical Issues**: Create GitHub issue
- **Security Concerns**: Contact security team privately
- **General Questions**: Check documentation first
- **Feature Requests**: Use GitHub discussions

## 📄 License

This project is proprietary software developed for gemeente use. All rights reserved.

---

**Built with ❤️ using Laravel, with focus on security, performance, and user experience.**
