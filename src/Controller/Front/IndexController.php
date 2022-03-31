<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index_ort_junior_conseil')]
    public function index(): Response
    {
        return $this->render('front/home/index.html.twig');
    }

    #[Route('/qui-sommes-nous', name: 'qui_sommes_nous_ort_junior_conseil')]
    public function quiSommesNous(): Response
    {
        return $this->render('front/home/nous.html.twig');
    }

}
