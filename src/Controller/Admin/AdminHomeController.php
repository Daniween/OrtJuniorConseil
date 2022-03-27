<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AdminHomeController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_home')]
    public function index(): Response
    {
        return $this->render('admin/home/index.html.twig');
    }
}
