> # Forgotten & reset Password
Tuto > Gestion des utilisateurs


# Modifier l'entité

Ajouter la propriété `resetToken` au fichier `src/Entity/User.php` 

```php
/**
 * @ORM\Column(type="string", length=255, nullable=true)
 */
private $resetToken;
```

**Générer les Getter/Setter**

```bash
php bin/console make:entity --regenerate
App
```

**Mise à jour de la base de données**

```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```


# Etape 1 : Oublie du mot de passe

## Le controleur

Dans le fichier `src/Controller/SecurityController.php` 

```php
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;


/**
 * @Route("/forgottenPassword", name="app_forgotten_password")
 */
public function forgottenPassword( Request $request, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator): Response
{
    if ($request->isMethod('POST')) 
    {
        $entityManager = $this->getDoctrine()->getManager();

        // Récupération de l'email saisi par l'utilisateur
        $email = $request->request->get('email');

        // Recherche l'utilisateur dans la base de données
        $user = $entityManager->getRepository(User::class)->findOneByEmail($email);

        // Erreur si l'utilisateur n'est pas trouvé
        if ($user === null) {
            $this->addFlash('danger', 'Email Inconnu');
            return $this->redirectToRoute('homepage');
        }

        // Création du token de sécurité
        $token = $tokenGenerator->generateToken();
        
        // Ajout du token à la base de données
        try{
            $user->setResetToken($token);
            $entityManager->flush();
        } catch (\Exception $e) {
            $this->addFlash('warning', $e->getMessage());
            return $this->redirectToRoute('homepage');
        }

        // Préparation de l'email de notification

        // Préparation du lien de renouvellement du mot de passe
        $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

        // Préparation du message
        $message = (new \Swift_Message('Forgot Password'))
            ->setFrom('netlab@osw3.net')
            ->setTo($user->getEmail())
            ->setBody(
                "blablabla voici le token pour reseter votre mot de passe : " . $url,
                'text/html'
            );

        // Envois du message
        $mailer->send($message);

        $this->addFlash('notice', 'Mail envoyé');
        return $this->redirectToRoute('homepage');
    }
    
    return $this->render('security/forgotten_password.html.twig');
}
```


## La vue

Créer le fichier `templates/security/forgotten_password.html.twig`

```twig
{% extends 'base.html.twig' %}

{% block title %}Forgotten Password!{% endblock %}

{% block body %}
    <form method="post">
        <h1 class="h3 mb-3 font-weight-normal">Forgotten Password</h1>

        <label for="inputEmail" class="sr-only">Email</label>
        <input type="email" name="email" id="inputEmail" class="form-control" placeholder="Email" required autofocus>

        <button class="btn btn-lg btn-primary" type="submit">Send mail !</button>
    </form>
{% endblock %}
```


# Etape 2 : Reset du mot de passe

## Le controleur

Dans le fichier `src/Controller/SecurityController.php` 

```php
/**
 * @Route("/reset_password/{token}", name="app_reset_password")
 */
public function resetPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder)
{
    if ($request->isMethod('POST')) 
    {
        $entityManager = $this->getDoctrine()->getManager();

        // Recherche l'utilisateur dans la base de données
        $user = $entityManager->getRepository(User::class)->findOneByResetToken($token);

        // Erreur si l'utilisateur n'est pas trouvé
        if ($user === null) {
            $this->addFlash('danger', 'Token Inconnu');
            return $this->redirectToRoute('homepage');
        }

        // Modification du mot de passe
        $user->setResetToken(null);
        $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
        $entityManager->flush();
        
        $this->addFlash('notice', 'Mot de passe mis à jour');
        return $this->redirectToRoute('homepage');
    }
    
    return $this->render('security/reset_password.html.twig', ['token' => $token]);
}
```

## La vue

Créer le fichier `templates/security/reset_password.html.twig`

```twig
{% extends 'base.html.twig' %}

{% block title %}Reset Password !{% endblock %}

{% block body %}
    <form method="post" action="{{ path('app_reset_password', {'token':token}) }}">

        <h1 class="h3 mb-3 font-weight-normal">Reset Password</h1>

        <div>
            <label for="inputPassword" class="sr-only">Password</label>
            <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
        </div>

        <button class="btn btn-lg btn-primary" type="submit">Register</button>
    </form>
{% endblock %}
```