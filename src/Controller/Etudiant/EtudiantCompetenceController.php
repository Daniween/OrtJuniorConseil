<?php

namespace App\Controller\Etudiant;

use App\Entity\EtudiantCompetence;
use App\Repository\CompetenceRepository;
use App\Repository\EtudiantCompetenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class EtudiantCompetenceController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    /**
     * UserController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/etudiant/competence', name: 'etudiant_competence')]
    public function index(CompetenceRepository $competenceRepository): Response
    {
        $user = $this->getUser();
        if(!$user) {
            return $this->redirectToRoute("etudiant_login");
        }

        return $this->render('etudiant/competence/index.html.twig', [
            'user'                  => $user,
            'competences_owned'     => $competenceRepository->findByEtudiantOwned($user),
            'competences_not_owned' => $competenceRepository->findByEtudiantNotOwned($user)
        ]);
    }

    /**
     * @param Request $request
     * @param EtudiantCompetenceRepository $etudiantCompetenceRepository
     * @param CompetenceRepository $competenceRepository
     * @return Response
     *
     * @Route("/etudiant/addOrRemoveCompetence/", name="add_or_remove_competence", methods={"POST"})
     */
    public function addOrRemoveCompetence(Request $request, EtudiantCompetenceRepository $etudiantCompetenceRepository, CompetenceRepository $competenceRepository) {
        $user = $this->getUser();
        $data = json_decode($request->getContent());

        if (!empty($etudiantCompetence = $etudiantCompetenceRepository->findBy(["etudiant"=>$user, "competence"=>$data->competenceId]))) {
            $etudiantCompetence = $etudiantCompetenceRepository->findBy(["etudiant"=>$user, "competence"=>$data->competenceId]);
            $this->entityManager->remove($etudiantCompetence[0]);
        } else {
            $newEtudiantCompetence = new EtudiantCompetence();
            $newEtudiantCompetence->setCompetence($competenceRepository->findOneBy(["id"=>$data->competenceId]));
            $newEtudiantCompetence->setEtudiant($user);
            $this->entityManager->persist($newEtudiantCompetence);
        }

        $this->entityManager->flush();

        return new JsonResponse("ok");
    }
}
