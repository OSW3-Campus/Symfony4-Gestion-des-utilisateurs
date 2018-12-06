> # Le controleur Security
Tuto > Gestion des utilisateurs


# Création du controleur

```bash
php bin/console make:controller SecurityController
```

Le maker créé le controleur `src/Controller/SecurityController` et la vue `templates/security/index.html.twig`.


# Vérification

Vérifier le rendu sur http://127.0.0.1:8000/security

![](../../images/view-of-security-route.png)


# Préparer le controleur pour la suite

Supprimer le contenu de la classe

```php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
}
```