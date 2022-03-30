<?php

namespace App\Controller\Etudiant;

use App\Data\SearchData;
use App\Entity\Etudiant;
use App\Form\SearchFormType;
use App\Repository\CompetenceRepository;
use App\Repository\EtudiantRepository;
use App\Repository\PersonnaliteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package App\Controller\Etudiant
 *
 */
class SearchProfilController extends AbstractController
{

    private EntityManagerInterface $entityManager;
    private EtudiantRepository $etudiantRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param EtudiantRepository $etudiantRepository
     */
    public function __construct(EntityManagerInterface $entityManager, EtudiantRepository $etudiantRepository)
    {
        $this->entityManager = $entityManager;
        $this->etudiantRepository = $etudiantRepository;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/search", name="search_etudiant_index")
     */
    public function index(Request $request): Response
    {
        $data = new SearchData();

        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);
        $etudiants = $this->etudiantRepository->findSearch($data);

        return $this->render('front/search/index.html.twig', [
            'etudiants' => $etudiants,
            'form'  => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param Etudiant $etudiant
     * @param CompetenceRepository $competenceRepository
     * @param PersonnaliteRepository $personnaliteRepository
     * @return Response
     *
     * @Route("/search/show/{id}", name="search_show_profil")
     */
    public function show(Request $request, Etudiant $etudiant, CompetenceRepository $competenceRepository, PersonnaliteRepository $personnaliteRepository): Response
    {
        return $this->render('front/search/show.html.twig', [
            'etudiant' => $etudiant,
            'competences' => $competenceRepository->findByEtudiantOwned($etudiant),
            'personnalites' => $personnaliteRepository->findByEtudiantOwned($etudiant)
        ]);
    }
}
