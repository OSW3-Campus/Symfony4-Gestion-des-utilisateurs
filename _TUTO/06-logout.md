> # Logout
Tuto > Gestion des utilisateurs


# La configuration

Dans le fichier `config/packages/security.yaml`

```yaml
        main:
            # ...

            logout:
                path: app_logout
```


# Le controleur

Dans le fichier `src/Controller/SecurityController.php` 

```php
/**
 * @Route("/logout", name="app_logout")
 */
public function logout()
{
    return $this->redirectToRoute('homepage');
}
```