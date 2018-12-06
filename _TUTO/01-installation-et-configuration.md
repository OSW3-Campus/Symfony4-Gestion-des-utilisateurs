> # Installation et configuration
Tuto > Gestion des utilisateurs


# Installation du projet

```bash
composer create-project symfony/website-skeleton my-project
```


# Installation des dépendances

#### Les dépendances de l'application

- **Security**, 
- **Swift Mailer**, 

```bash
composer require security
composer require swiftmailer/swiftmailer
```

### Les dépendances pour le développement

- **WebServer Bundle**
- **Maker Bundle**

```bash
composer require symfony/web-server-bundle --dev
composer require symfony/maker-bundle --dev
```


# Création de la base de données

### Paramètres de connexion

Définir les paramètres de connexion à la base de données dans le fichier `.env`.

```env
DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
```

### Création de la base de données

```bash
php bin/console doctrine:database:create
```


# Configuration de Swift Mailer

Définir les paramètres de connexion à la base de données dans le fichier `.env`.

```env
MAILER_URL=null://localhost
```


# Démarrer le serveur

Démarrer le serveur web interne.

```bash
php bin/console server:run
```

`[OK] Server listening on http://127.0.0.1:8000`