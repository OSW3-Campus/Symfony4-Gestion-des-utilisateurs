> # L'Entitié `User`
Tuto > Gestion des utilisateurs


# Création de l'entité

```
php bin/console make:user
```

Valider tous les choix par défaut.

Le Maker à créer la classe `src/Entity/User.php`.


# Modifier la classe

## En ligne de commande

```bash
php bin/console make:entity User
```

## En modifiant le fichier

**Ajouter les propriétés**

On ajoute les propriétés :
- firstname
- lastname

```php
/**
 * @ORM\Column(type="string", length=40)
 */
private $firstname;

/**
 * @ORM\Column(type="string", length=40)
 */
private $lastname;
```

**Générer les Getter/Setter**

```bash
php bin/console make:entity --regenerate
App
```


# Création de la table

```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```
