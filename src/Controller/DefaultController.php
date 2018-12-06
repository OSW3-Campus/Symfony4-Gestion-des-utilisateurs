<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
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
}
