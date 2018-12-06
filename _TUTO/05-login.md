> # Login
Tuto > Gestion des utilisateurs


Le maker **Authenticator**

```bash
php bin/console make:auth
1
LoginFormAuthenticator
SecurityController
```

- Choisir l'option `[1] Login form authenticator`
- Nommer la classe `LoginFormAuthenticator`
- Conserver le controleur `SecurityController`

Ce que le maker créé : 
- La configuration `guard` dans le fichier `config/packages/security.yaml`
- La methode `login` dans le controleur `src/Controller/SecurityController.php` 
- La vue `templates/security/login.html.twig`
- Le controleur `src/Security/LoginFormAuthenticator.php`


# La configuration

On ajoute la classe `LoginFormAuthenticator` au Firewall et on active l'option `remember_me` valide 30 jours.

```yaml
        main:
            anonymous: true
            guard:
                authenticators:
                    - App\Security\LoginFormAuthenticator
            
            remember_me:
                secret: '%kernel.secret%'
                lifetime: 2592000
```


# La méthode login

```php
/**
 * @Route("/login", name="app_login")
 */
public function login(AuthenticationUtils $authenticationUtils): Response
{
    // get the login error if there is one
    $error = $authenticationUtils->getLastAuthenticationError();
    // last username entered by the user
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
}
```


# La vue

```twig
{% extends 'base.html.twig' %}

{% block title %}Log in!{% endblock %}

{% block body %}
<form method="post">
    {% if error %}
        <div class="alert alert-danger">{{ error.messageKey|trans(error.messageData, 'security') }}</div>
    {% endif %}

    <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
    <div>
        <label for="inputEmail" class="sr-only">Email</label>
        <input type="email" value="{{ last_username }}" name="email" id="inputEmail" class="form-control" placeholder="Email" required autofocus>
    </div>
    <div>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
    </div>
    <div>
        <label>
            <input type="checkbox" name="_remember_me"> Remember me
        </label>
    </div>

    <input type="hidden" name="_csrf_token" value="{{ csrf_token('authenticate') }}">
    <button class="btn btn-lg btn-primary" type="submit">Sign in</button>

    <div>
        <a  href="{{ path('app_forgotten_password') }}">Forgotten password</a>
    </div>
</form>
{% endblock %}
```


# Le controleur

```php
namespace App\Security;
// ...
class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    // ...
    public function supports(Request $request)
    {
        return 'app_login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);

        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Email could not be found.');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('homepage'));
    }

    protected function getLoginUrl()
    {
        return $this->router->generate('app_login');
    }
}
```