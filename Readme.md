# 🎨 FadelArt - Blog d'Œuvres d'Art

Plateforme web de partage et de gestion d'œuvres d'art permettant aux utilisateurs de publier, consulter et gérer des articles artistiques. Le système inclut une authentification sécurisée avec gestion des rôles (utilisateurs et administrateurs), un upload d'images, un backoffice d'administration complet .

---

## 📋 Technologies

- **Symfony 7.4**
- **Docker** (FrankenPHP, MySQL 8.0, phpMyAdmin, Mailpit)
- **API Platform**
- **VichUploader** (gestion images)
- **EasyAdmin** (backoffice)
- **Bootstrap 5** + Font Awesome

---

## 🚀 Installation

### 1. Cloner et installer
```bash
# Créer le projet
mkdir fadelart && cd fadelart
mkdir app

# Installer Symfony
cd app/
composer create-project symfony/webapp:"7.4.*" .
cd ..
```

### 2. Configuration Docker

**Créer les fichiers :**
- `docker-compose.yml`
- `Dockerfile`
- `.dockerignore`
- `.env.docker`



### 4. Lancer Docker
```bash
docker-compose up -d
```

### 5. Base de données
```bash
docker exec app php bin/console doctrine:database:create
docker exec app php bin/console doctrine:migrations:migrate
```

---

## 📦 Dépendances
```bash
docker exec app composer require symfony/maker-bundle --dev
docker exec app composer require vich/uploader-bundle
docker exec app composer require easycorp/easyadmin-bundle
docker exec app composer require api
docker exec app composer require --dev symfony/profiler-pack
```

---

## 🗂️ Structure
```
fadelart/
├── app/
│   ├── src/
│   │   ├── Controller/
│   │   │   ├── HomeController.php
│   │   │   ├── BlogController.php
│   │   │   ├── SecurityController.php
│   │   │   ├── RegistrationController.php
│   │   │   ├── User/
│   │   │   │   └── ArticleController.php
│   │   │   └── Admin/
│   │   │       ├── DashboardController.php
│   │   │       ├── ArticleController.php
│   │   │       ├── UserController.php
│   │   │       └── EasyAdminDashboardController.php
│   │   ├── Entity/
│   │   │   ├── User.php
│   │   │   └── Article.php
│   │   ├── Form/
│   │   ├── Repository/
│   │   └── Security/
│   └── templates/
├── docker-compose.yml
├── Dockerfile
├── .dockerignore
└── .env.docker
```

---

## 👤 Entités

### User
- email (login)
- password (hashé)
- firstName, lastName
- roles (ROLE_USER, ROLE_ADMIN)
- createdAt
- articles (OneToMany)

### Article
- title, artist, description, category
- imageName (VichUploader)
- author (ManyToOne → User)
- createdAt, updatedAt

---

## 🌐 Routes Principales

### Frontend Public
- `/` - Accueil
- `/blog` - Galerie publique
- `/blog/{id}` - Détail article
- `/login` - Connexion
- `/register` - Inscription

### Espace User (ROLE_USER)
- `/mes-articles` - Mes articles
- `/article/new` - Créer article
- `/article/{id}/edit` - Modifier article
- `/article/{id}/delete` - Supprimer article

### Admin Manuel (ROLE_ADMIN)
- `/admin` - Dashboard
- `/admin/articles` - Gérer tous les articles
- `/admin/users` - Gérer utilisateurs

### Backoffice EasyAdmin
- `/easyadmin` - Interface admin automatique

### API REST
- `GET /api/articles` - Liste articles
- `GET /api/articles/{id}` - Détail article
- `POST /api/articles` - Créer article
- `PUT /api/articles/{id}` - Modifier article
- `DELETE /api/articles/{id}` - Supprimer article
- `GET /api/users` - Liste users
- `/api/docs` - Documentation Swagger

---

## 🔐 Authentification
```bash
# Créer User
docker exec app php bin/console make:user

# Créer système login
docker exec app php bin/console make:auth

# Créer inscription
docker exec app php bin/console make:registration-form
```

### Compte Admin

**Email:** `admin@blog.com`  
**Password:** `admin123`

---

## 📤 Upload d'Images

**Configuration : `config/packages/vich_uploader.yaml`**
```yaml
vich_uploader:
    db_driver: orm
    mappings:
        article_images:
            uri_prefix: /images/articles
            upload_destination: '%kernel.project_dir%/public/images/articles'
            namer: Vich\UploaderBundle\Naming\SmartUniqueNamer
```

**Créer le dossier :**
```bash
docker exec app mkdir -p public/images/articles
docker exec app chmod 777 public/images/articles
```

**Affichage Twig :**
```twig
{{ vich_uploader_asset(article, 'imageFile') }}
```

---

## 🛠️ Commandes Utiles
```bash
# Migrations
docker exec app php bin/console make:migration
docker exec app php bin/console doctrine:migrations:migrate

# Créer entité
docker exec app php bin/console make:entity NomEntite

# Créer contrôleur
docker exec app php bin/console make:controller NomController

# Créer CRUD
docker exec app php bin/console make:crud NomEntite

# Vider cache
docker exec app php bin/console cache:clear

# Voir routes
docker exec app php bin/console debug:router

# Hasher password
docker exec app php bin/console security:hash-password
```

---

## 🎨 Design

- **Bootstrap 5.3**
- **Font Awesome 6.4**
- **Google Fonts (Poppins)**
- **Dégradés violet** (#667eea → #764ba2)
- **Animations CSS** (hover, transitions)

---

## 🔒 Sécurité

- Mots de passe hashés
- CSRF tokens sur formulaires
- `#[IsGranted('ROLE_USER')]` sur routes protégées
- Vérification auteur pour édition/suppression
- API : Groupes de sérialisation (password jamais exposé)

---

## 📱 API REST

**Activer sur entité :**
```php
use ApiPlatform\Metadata\ApiResource;

#[ApiResource]
class Article
{
    // ...
}
```

**Tester :**
```bash
curl http://localhost:8008/api/articles
```

**Documentation :**
```
http://localhost:8008/api/docs
```

---

## 🐳 Docker

### Services

| Service | Container | Port | Rôle |
|---------|-----------|------|------|
| **app** | `blog_app` | 8008 | Application Symfony (FrankenPHP) |
| **db** | `blog_db` | 3308 | MySQL 8.0 |
| **phpmyadmin** | - | 8083 | Interface base de données |
| **mailpit** | `blog_mailpit` | 8028 (web), 1028 (SMTP) | Test emails |

### Commandes Docker
```bash
# Démarrer tous les services
docker-compose up -d

# Arrêter tous les services
docker-compose down

# Voir les logs
docker-compose logs -f app

# Entrer dans le conteneur app
docker exec app bash

# Entrer dans MySQL
docker exec  blog_db mysql -u root blog_db

# Redémarrer un service
docker-compose restart app
```


---

## ✅ Fonctionnalités

- ✅ Inscription / Connexion
- ✅ Upload d'images
- ✅ CRUD articles (User)
- ✅ Dashboard admin
- ✅ Gestion utilisateurs (Admin)
- ✅ Backoffice EasyAdmin
- ✅ API REST complète
- ✅ Documentation Swagger
- ✅ Profiler Symfony
- ✅ Test emails (Mailpit)
- ✅ Design moderne responsive




## 📝 Auteur

**Fadel d'Almeida**  
ECE Campus Paris  
Projet Symfony 7.4

---

## 📄 Licence

MIT