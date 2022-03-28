<?php

namespace App\Controller\Etudiant;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class EtudiantPersonnaliteController extends AbstractController
{
    #[Route('/etudiant/personnalite', name: 'etudiant_personnalite')]
    public function index(): Response
    {
        $user = $this->getUser();
        if(!$user) {
            return $this->redirectToRoute("etudiant_login");
        }

        return $this->render('etudiant/personnalite/index.html.twig', [
            'user' => $user
        ]);
    }
}
