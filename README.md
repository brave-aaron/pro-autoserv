# AutoService - Application MVC PHP

Une application web de gestion de vente et location de véhicules construite avec une architecture MVC (Model-View-Controller) en PHP.

## 🏗️ Architecture MVC

Cette application suit le pattern MVC pour une meilleure organisation du code :

### Structure des dossiers

```
/
├── app/
│   ├── controllers/          # Contrôleurs (logique métier)
│   │   ├── Controller.php    # Classe de base des contrôleurs
│   │   ├── AuthController.php
│   │   ├── VehicleController.php
│   │   ├── AdminController.php
│   │   └── HomeController.php
│   ├── models/              # Modèles (données)
│   │   ├── Model.php        # Classe de base des modèles
│   │   ├── User.php
│   │   └── Vehicle.php
│   └── views/               # Vues (interface utilisateur)
│       ├── layouts/         # Templates de base
│       ├── auth/           # Vues d'authentification
│       ├── vehicles/       # Vues des véhicules
│       ├── admin/          # Vues d'administration
│       └── pages/          # Pages statiques
├── config/
│   ├── Database.php         # Configuration base de données
│   └── Router.php          # Système de routage
├── assets/
│   ├── css/                # Fichiers CSS
│   ├── js/                 # Fichiers JavaScript
│   └── images/             # Images
└── index.php               # Point d'entrée principal
```

## 🚀 Fonctionnalités

### Utilisateurs
- ✅ Inscription et connexion
- ✅ Gestion de profil
- ✅ Système de rôles (user/admin)
- ✅ Gestion des statuts (actif/suspendu/banni)

### Véhicules
- ✅ Publication de véhicules (vente/location)
- ✅ Recherche et filtres
- ✅ Gestion des images
- ✅ Modification et suppression

### Administration
- ✅ Dashboard administrateur
- ✅ Gestion des utilisateurs
- ✅ Modération des véhicules
- ✅ Statistiques

## 🛠️ Classes principales

### Models
- **Model.php** : Classe de base avec CRUD générique
- **User.php** : Gestion des utilisateurs
- **Vehicle.php** : Gestion des véhicules

### Controllers
- **Controller.php** : Classe de base avec méthodes utilitaires
- **AuthController.php** : Authentification et gestion des comptes
- **VehicleController.php** : CRUD des véhicules
- **AdminController.php** : Administration
- **HomeController.php** : Page d'accueil

### Configuration
- **Database.php** : Connexion à la base de données avec MySQLi
- **Router.php** : Système de routage simple

## 📡 API et Routage

Le routeur gère automatiquement les routes suivantes :

### Pages publiques
- `GET /` - Page d'accueil
- `GET /vente.php` - Liste des véhicules en vente
- `GET /location.php` - Liste des véhicules en location

### Authentification
- `GET /login.php` - Formulaire de connexion
- `POST /login_process.php` - Traitement de la connexion
- `GET /register.php` - Formulaire d'inscription
- `POST /register_process.php` - Traitement de l'inscription
- `GET /logout.php` - Déconnexion

### Gestion des véhicules
- `GET /publication.php` - Formulaire de publication
- `POST /publication_process.php` - Traitement de la publication
- `GET /my-vehicles.php` - Mes véhicules
- `POST /supprimer.php` - Suppression

### Administration
- `GET /dashboard_admin.php` - Dashboard admin
- `POST /changer_statut.php` - Changement de statut utilisateur

## 🎨 Fonctionnalités JavaScript

### AJAX
- Formulaires asynchrones avec la classe `.ajax-form`
- Notifications en temps réel
- Gestion des erreurs

### Notifications
```javascript
showNotification('Message', 'success'); // success, error, info
```

## 💾 Base de données

### Tables principales
- `users` : Utilisateurs avec rôles et statuts
- `vehicles` : Véhicules avec type (vente/location) et statuts

## 🔧 Installation

1. Cloner le projet
2. Configurer la base de données dans `config/Database.php`
3. Créer les tables nécessaires
4. Configurer le serveur web pour pointer vers `index.php`

## 📝 Utilisation

### Créer un nouveau contrôleur
```php
<?php
require_once __DIR__ . '/Controller.php';

class MonController extends Controller {
    public function maMethode() {
        $this->view('ma-vue', ['data' => $data]);
    }
}
```

### Créer un nouveau modèle
```php
<?php
require_once __DIR__ . '/Model.php';

class MonModel extends Model {
    protected $table = 'ma_table';
    
    public function maMethodePersonnalisee() {
        // Logique spécifique
    }
}
```

### Ajouter une route
```php
// Dans Router.php -> defineRoutes()
$this->get('/ma-route', 'MonController', 'maMethode');
```

## 🎯 Avantages de cette architecture

1. **Séparation des responsabilités** : Logique métier, données et interface séparées
2. **Réutilisabilité** : Classes de base extensibles
3. **Maintenabilité** : Code organisé et structuré
4. **Sécurité** : Validation centralisée et protection contre les injections SQL
5. **Évolutivité** : Facilité d'ajout de nouvelles fonctionnalités

## 🔐 Sécurité

- Requêtes préparées (protection SQL Injection)
- Validation des données d'entrée
- Hashage des mots de passe
- Gestion des sessions sécurisée
- Contrôle d'accès par rôles