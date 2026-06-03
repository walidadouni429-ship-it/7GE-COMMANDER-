# SGE-ERP - Seven Generation Energy

## 🚀 Déploiement rapide

### 1. Prérequis
- PHP 7.4+
- MySQL 5.7+
- Composer

### 2. Installation
```bash
cd sge-erp
composer install
mysql -u root -p < install.sql
php -S localhost:8000
```

### 3. Accès
- URL: `http://localhost:8000`
- Email: `admin@sevengenenergy.tn`
- Mot de passe: `password`

### 4. API Endpoints
- `GET/POST /api/devis.php` - Devis
- `GET/POST /api/clients.php` - Clients

### ⚙️ Configuration
Modifier `config/database.php`:
- DB_USER, DB_PASS (MySQL)
- SMTP_USER, SMTP_PASS (Gmail)