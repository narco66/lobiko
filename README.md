# 🏥 LOBIKO - Plateforme de Santé Numérique pour l'Afrique

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)

## 📋 Table des matières

- [À propos](#-à-propos)
- [Fonctionnalités](#-fonctionnalités)
- [Architecture](#-architecture)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Utilisation](#-utilisation)
- [API](#-api)
- [Tests](#-tests)
- [Déploiement](#-déploiement)
- [Contribution](#-contribution)
- [Support](#-support)
- [Licence](#-licence)

## 🎯 À propos

**LOBIKO** est une plateforme de santé numérique complète conçue spécifiquement pour répondre aux défis du système de santé en Afrique. Elle facilite la mise en relation entre patients, professionnels de santé, pharmacies et assurances, tout en offrant une gestion intégrée des consultations, prescriptions, paiements et prises en charge médicales.

### Vision
Démocratiser l'accès aux soins de santé en Afrique grâce à la technologie numérique.

### Mission
- Simplifier le parcours de soins pour les patients
- Optimiser la gestion médicale et financière pour les professionnels
- Garantir la transparence et la traçabilité des prestations
- Faciliter l'intégration avec les systèmes d'assurance santé

## ✨ Fonctionnalités

### Pour les Patients
- 📅 **Prise de rendez-vous** en ligne avec géolocalisation
- 💬 **Téléconsultation** intégrée (audio/vidéo/chat)
- 📋 **Dossier médical électronique** sécurisé
- 💊 **Ordonnances électroniques** avec QR code
- 🏪 **Commande en pharmacie** avec livraison
- 💳 **Paiements multi-canaux** (Mobile Money, cartes, espèces)
- 📊 **Suivi des remboursements** assurance

### Pour les Professionnels de Santé
- 📆 **Agenda médical** intelligent
- 👥 **Gestion des patients** avec historique complet
- 📝 **Consultations** et prescriptions numériques
- 💰 **Facturation automatisée** avec gestion PEC
- 📈 **Tableaux de bord** et statistiques
- 🔄 **Reversements automatiques** des honoraires

### Pour les Pharmacies
- 💊 **Dispensation d'ordonnances** électroniques
- 📦 **Gestion des stocks** en temps réel
- 🚚 **Gestion des livraisons**
- 💳 **Encaissement intégré**
- 📊 **Alertes** rupture et péremption

### Pour les Structures Médicales
- 🏥 **Gestion multi-praticiens**
- 📊 **Pilotage d'activité**
- 💼 **Gestion financière** complète
- 👥 **Gestion du personnel**
- 📈 **Rapports détaillés**

### Pour les Assurances
- ✅ **Validation PEC** en temps réel
- 💰 **Gestion des remboursements**
- 🔍 **Détection de fraudes**
- 📊 **Reporting** et statistiques
- 🤝 **Tiers payant** automatisé

## 🏗️ Architecture

### Stack Technique
- **Backend:** Laravel 11 (PHP 8.3)
- **Base de données:** MySQL 8.0 / MariaDB
- **Frontend:** Blade, Bootstrap 5, Alpine.js
- **Cache:** Redis
- **Queue:** Redis + Horizon
- **Stockage:** Local / S3
- **Paiements:** Mobile Money APIs, Stripe
- **Temps réel:** Pusher / Laravel Echo
- **API:** RESTful avec Laravel Sanctum

### Structure du Projet
```
lobiko/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Requests/
│   │   ├── Resources/
│   │   └── Middleware/
│   ├── Models/
│   ├── Services/
│   ├── Repositories/
│   ├── Jobs/
│   ├── Events/
│   └── Listeners/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── resources/
│   ├── views/
│   ├── js/
│   └── css/
├── routes/
├── tests/
└── public/
```

## 🚀 Installation

### Prérequis
- PHP >= 8.3
- Composer >= 2.0
- Node.js >= 18.0
- MySQL >= 8.0
- Redis (optionnel mais recommandé)

### Installation Rapide

```bash
# 1. Cloner le repository
git clone https://github.com/votre-org/lobiko.git
cd lobiko

# 2. Installer les dépendances PHP
composer install

# 3. Installer les dépendances JavaScript
npm install && npm run build

# 4. Copier le fichier d'environnement
cp .env.example .env

# 5. Générer la clé d'application
php artisan key:generate

# 6. Configurer la base de données dans .env
# DB_DATABASE=lobiko_db
# DB_USERNAME=root
# DB_PASSWORD=

# 7. Créer la base de données
mysql -u root -p -e "CREATE DATABASE lobiko_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 8. Exécuter les migrations et seeders
php artisan migrate --seed

# 9. Créer le lien symbolique pour le stockage
php artisan storage:link

# 10. Démarrer le serveur
php artisan serve
```

### Installation avec Script

```bash
chmod +x deploy.sh
./deploy.sh
```

## ⚙️ Configuration

### Variables d'Environnement Importantes

```env
# Application
APP_NAME=LOBIKO
APP_ENV=local
APP_URL=http://localhost:8000

# Base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lobiko_db
DB_USERNAME=root
DB_PASSWORD=

# Cache et Queue
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525

# Mobile Money APIs
AIRTEL_MONEY_API_URL=
AIRTEL_MONEY_API_KEY=
MTN_MOMO_API_URL=
MTN_MOMO_API_KEY=
ORANGE_MONEY_API_URL=
ORANGE_MONEY_API_KEY=

# Google Maps
GOOGLE_MAPS_API_KEY=

# SMS Gateway
SMS_GATEWAY_URL=
SMS_GATEWAY_API_KEY=
```

### Configuration des Workers

```bash
# Démarrer les workers de queue
php artisan queue:work --queue=high,default,low --sleep=3 --tries=3

# Ou avec Supervisor (recommandé en production)
sudo supervisorctl start lobiko-worker:*
```

### Tâches Planifiées

Ajouter au crontab :
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

## 📱 Utilisation

### Comptes de Démonstration

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| **Admin** | admin@lobiko.com | Admin@2025 |
| **Médecin** | dr.martin@lobiko.com | Medecin@2025 |
| **Patient** | patient.test@lobiko.com | Patient@2025 |
| **Pharmacien** | pharmacie.centrale@lobiko.com | Pharmacie@2025 |
| **Assureur** | assurance@lobiko.com | Assurance@2025 |
| **Comptable** | comptable@lobiko.com | Comptable@2025 |

### Parcours Patient Type

1. **Inscription** → Création du compte patient
2. **Recherche** → Trouver un médecin par spécialité/localisation
3. **Rendez-vous** → Réserver un créneau
4. **Consultation** → En présentiel ou téléconsultation
5. **Ordonnance** → Réception de la prescription électronique
6. **Pharmacie** → Commander les médicaments
7. **Paiement** → Régler via Mobile Money
8. **Livraison** → Réception à domicile

## 🔌 API

### Authentification

```bash
# Login
POST /api/login
{
    "email": "user@example.com",
    "password": "password"
}

# Response
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "user": {...}
}
```

### Endpoints Principaux

```bash
# Consultations
GET    /api/consultations
POST   /api/consultations
GET    /api/consultations/{id}
PUT    /api/consultations/{id}
DELETE /api/consultations/{id}

# Rendez-vous
GET    /api/appointments
POST   /api/appointments
PATCH  /api/appointments/{id}/confirm
PATCH  /api/appointments/{id}/cancel

# Ordonnances
GET    /api/prescriptions
GET    /api/prescriptions/{id}
POST   /api/prescriptions/{id}/dispense

# Paiements
POST   /api/payments
GET    /api/payments/{id}
POST   /api/payments/{id}/confirm
```

Documentation complète : [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)

## 🧪 Tests

### Exécuter les Tests

```bash
# Tous les tests
php artisan test

# Tests en parallèle
php artisan test --parallel

# Tests avec couverture
php artisan test --coverage

# Tests spécifiques
php artisan test --filter ConsultationTest
```

### Structure des Tests

```
tests/
├── Feature/
│   ├── ConsultationTest.php
│   ├── PaymentTest.php
│   └── ...
├── Unit/
│   ├── UserTest.php
│   ├── FactureTest.php
│   └── ...
└── TestCase.php
```

## 🚢 Déploiement

### Production avec Docker

```bash
# Build
docker-compose build

# Démarrer
docker-compose up -d

# Migrations
docker-compose exec app php artisan migrate --force
```

### Déploiement sur VPS

```bash
# 1. Cloner le projet
git clone https://github.com/votre-org/lobiko.git /var/www/lobiko

# 2. Installer les dépendances
cd /var/www/lobiko
composer install --optimize-autoloader --no-dev
npm install && npm run build

# 3. Configuration
cp .env.production .env
php artisan key:generate

# 4. Permissions
chown -R www-data:www-data /var/www/lobiko
chmod -R 755 /var/www/lobiko/storage

# 5. Optimisation
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 6. Nginx configuration
ln -s /var/www/lobiko/nginx.conf /etc/nginx/sites-enabled/lobiko
systemctl reload nginx
```

## 🤝 Contribution

Nous accueillons avec plaisir les contributions ! Veuillez consulter notre [Guide de Contribution](CONTRIBUTING.md).

### Processus de Contribution

1. Fork le projet
2. Créer une branche (`git checkout -b feature/AmazingFeature`)
3. Commit les changements (`git commit -m 'Add AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

### Standards de Code

- PSR-12 pour PHP
- ESLint pour JavaScript
- Commits conventionnels
- Tests obligatoires pour nouvelles fonctionnalités

## 📞 Support

### Documentation
- [Documentation Utilisateur](docs/user-guide.md)
- [Documentation Technique](docs/technical-guide.md)
- [FAQ](docs/faq.md)

### Contact
- **Email:** support@lobiko.com
- **Téléphone:** +241 77 79 06 54
- **Discord:** [Rejoindre notre serveur](https://discord.gg/lobiko)

### Rapporter un Bug
[Créer une issue](https://github.com/votre-org/lobiko/issues/new)

## 📄 Licence

Ce projet est sous licence propriétaire. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

---

## 🎉 Remerciements

- Laravel Team pour le framework excellent
- Spatie pour les packages de qualité
- La communauté open source
- Tous nos contributeurs

---

**Développé avec ❤️ pour l'Afrique**

© 2025 LOBIKO - Tous droits réservés
