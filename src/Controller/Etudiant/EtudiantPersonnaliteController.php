<?php

namespace App\Controller\Etudiant;

use App\Entity\EtudiantPersonnalite;
use App\Repository\PersonnaliteRepository;
use App\Repository\EtudiantPersonnaliteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class EtudiantPersonnaliteController extends AbstractController
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
    #[Route('/etudiant/personnalite', name: 'etudiant_personnalite')]
    public function index(PersonnaliteRepository $personnaliteRepository): Response
    {
        $user = $this->getUser();
        if(!$user) {
            return $this->redirectToRoute("etudiant_login");
        }

        return $this->render('etudiant/personnalite/index.html.twig', [
            'user'                  => $user,
            'personnalites_owned'     => $personnaliteRepository->findByEtudiantOwned($user),
            'personnalites_not_owned' => $personnaliteRepository->findByEtudiantNotOwned($user)
        ]);
    }

    /**
     * @param Request $request
     * @param EtudiantPersonnaliteRepository $etudiantPersonnaliteRepository
     * @param PersonnaliteRepository $personnaliteRepository
     * @return Response
     *
     * @Route("/etudiant/addOrRemovePersonnalite/", name="add_or_remove_personnalite", methods={"POST"})
     */
    public function addOrRemovePersonnalite(Request $request, EtudiantPersonnaliteRepository $etudiantPersonnaliteRepository, PersonnaliteRepository $personnaliteRepository) {
        $user = $this->getUser();
        $data = json_decode($request->getContent());

        if (!empty($etudiantPersonnalite = $etudiantPersonnaliteRepository->findBy(["etudiant"=>$user, "personnalite"=>$data->personnaliteId]))) {
            $etudiantPersonnalite = $etudiantPersonnaliteRepository->findBy(["etudiant"=>$user, "personnalite"=>$data->personnaliteId]);
            $this->entityManager->remove($etudiantPersonnalite[0]);
        } else {
            $newEtudiantPersonnalite = new EtudiantPersonnalite();
            $newEtudiantPersonnalite->setPersonnalite($personnaliteRepository->findOneBy(["id"=>$data->personnaliteId]));
            $newEtudiantPersonnalite->setEtudiant($user);
            $this->entityManager->persist($newEtudiantPersonnalite);
        }

        $this->entityManager->flush();

        return new JsonResponse("ok");
    }
}
