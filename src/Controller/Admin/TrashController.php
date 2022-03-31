<?php

namespace App\Controller\Admin;

use App\Repository\CompetenceRepository;
use App\Repository\PersonnaliteRepository;
use App\Repository\EtudeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TrashController
 * @package App\Controller\Admin
 * @IsGranted("ROLE_ADMIN", statusCode=403, message="Vous n'avez pas accès à cette page.")
 *
 * @Route("/admin")
 */
class TrashController extends AbstractController
{
    private CompetenceRepository $competenceRepository;
    private PersonnaliteRepository $personnaliteRepository;
    private EtudeRepository $etudeRepository;

    /**
     * TrashController constructor.
     * @param CompetenceRepository $competenceRepository
     * @param PersonnaliteRepository $personnaliteRepository
     * @param EtudeRepository $etudeRepository
     */
    public function __construct(CompetenceRepository $competenceRepository, PersonnaliteRepository $personnaliteRepository, EtudeRepository $etudeRepository)
    {
        $this->competenceRepository = $competenceRepository;
        $this->personnaliteRepository = $personnaliteRepository;
        $this->etudeRepository = $etudeRepository;
    }

    /**
     * @return Response
     *
     *
     * @Route("/trash", name="admin_trash")
     */
    public function index(): Response
    {
        return $this->render('admin/trash/index.html.twig', [
            'competences'      => $this->competenceRepository->findAll(true),
            'personnalites'    => $this->personnaliteRepository->findAll(true),
            'etudes'           => $this->etudeRepository->findAll(true),
        ]);
    }
}