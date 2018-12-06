> # Homepage
Tuto > Gestion des utilisateurs


La homepage nous servira uniquement pour tester la connxeion utilisateur.

```bash
php bin/console make:controller
DefaultController
```

# Le controleur

Modifier le controleur `src/Controller/DefaultController.php` 

```php
/**
 * @Route("/", name="homepage")
 */
public function index()
{
    $user = $this->getUser();

    return $this->render('default/index.html.twig', [
        'controller_name' => 'DefaultController',
        'user' => $user,
    ]);
}
```

# La vue

Modifier la vue `templates/default/index.html.twig`

```twig
{% if not is_granted('IS_AUTHENTICATED_REMEMBERED') %}
    <div>
        <a href="{{ path('app_login') }}">Login</a>
        <a href="{{ path('app_register') }}">Register</a>
    </div>
{% else %}
    <div>
        {{ user.firstname }} {{ user.lastname }}
        <a href="{{ path('app_logout') }}">Logout</a>
    </div>
{% endif %}
```