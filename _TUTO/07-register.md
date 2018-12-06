> # Register
Tuto > Gestion des utilisateurs


# Le controleur

Dans le fichier `src/Controller/SecurityController.php` 

```php
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

// ...

/**
 * @Route("/register", name="app_register")
 */
public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
{
    if ($request->isMethod('POST')) 
    {
        $user = new User();

        $firstname  = $request->request->get('firstname');
        $lastname   = $request->request->get('lastname');
        $email      = $request->request->get('email');
        $password   = $request->request->get('password');
        $password   = $passwordEncoder->encodePassword($user, $password);

        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setEmail($email);
        $user->setPassword($password);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('homepage');
    }
    
    return $this->render('security/register.html.twig');
}
```


# La vue

Dans le fichier `templates/security/register.html.twig`

```twig
{% extends 'base.html.twig' %}

{% block title %}Register !{% endblock %}

{% block body %}
    <form method="post">
        <h1 class="h3 mb-3 font-weight-normal">Register</h1>
        <div>
            <label for="firstname" class="sr-only">Firstname</label>
            <input type="text" name="firstname" id="firstname" class="form-control" placeholder="Firstname" required autofocus>
        </div>
        <div>
            <label for="lastname" class="sr-only">Lastname</label>
            <input type="text" name="lastname" id="lastname" class="form-control" placeholder="Lastname" required autofocus>
        </div>
        <div>
            <label for="email" class="sr-only">Email</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="Email" required autofocus>
        </div>
        <div>
            <label for="password" class="sr-only">Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
        </div>
        <button class="btn btn-lg btn-primary" type="submit">Register</button>
    </form>
{% endblock %}
```