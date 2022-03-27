<?php

namespace App\Controller\Etudiant;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class HomeController extends AbstractController
{
    #[Route('/etudiant/home', name: 'etudiant_home')]
    public function index(): Response
    {
        $user = $this->getUser();
        if(!$user) {
            return $this->redirectToRoute("etudiant_login");
        }

        return $this->render('etudiant/home/index.html.twig', [
            'user' => $user
        ]);
    }
}
