<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
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

    /**
     * @Route("/forgotten_password", name="app_forgotten_password")
     */
    public function forgottenPassword(Request $request, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator): Response
    {
        if ($request->isMethod('POST')) 
        {
            $entityManager = $this->getDoctrine()->getManager();

            // Récupération de l'email saisi par l'utilisateur
            $email = $request->request->get('email');

            // Recherche l'utilisateur dans la base de données
            $user = $entityManager->getRepository(User::class)->findOneByEmail($email);

            // Erreur si l'utilisateur n'est pas trouvé
            if ($user === null) 
            {
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

            $this->addFlash('notice', 'Mail envoyé : '. $url);
            return $this->redirectToRoute('homepage');
        }

        return $this->render('security/forgotten_password.html.twig');
    }

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
            if ($user === null) 
            {
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
    
    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        return $this->redirectToRoute('homepage');
    }
}
