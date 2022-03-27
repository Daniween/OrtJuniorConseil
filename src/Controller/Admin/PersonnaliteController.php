<?php

namespace App\Controller\Admin;

use App\Entity\Personnalite;
use App\Form\PersonnaliteType;
use App\Repository\PersonnaliteRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function dd;

/**
 * Class PersonnaliteController
 * @package App\Controller\back
 * @IsGranted("ROLE_ADMIN", statusCode=403, message="Vous n'avez pas accès à cette page.")
 *
 * @Route("/admin")
 */
class PersonnaliteController extends AbstractController
{

    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param PersonnaliteRepository $personnaliteRepository
     * @return Response
     *
     *
     * @Route("/personnalite", name="personnalite_index", methods={"GET"})
     */
    public function index(PersonnaliteRepository $personnaliteRepository): Response
    {
        return $this->render('admin/personnalite/index.html.twig', [
            'personnalites' => $personnaliteRepository->findAll(),
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     *
     *
     * @Route("/personnalite/new", name="personnalite_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $personnalite = new Personnalite();
        $form = $this->createForm(PersonnaliteType::class, $personnalite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->persist($personnalite);
            $this->entityManager->flush();

            return $this->redirectToRoute('personnalite_index');
        }

        return $this->render('admin/personnalite/new.html.twig', [
            'personnalite' => $personnalite,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Personnalite $personnalite
     * @return Response
     *
     * @Route("/personnalite/{id}", name="personnalite_show", methods={"GET"})
     */
    public function show(Personnalite $personnalite): Response
    {
        $listeEtudiants = $personnalite->getEtudiantPersonnalites()->getIterator();

        return $this->render('admin/personnalite/show.html.twig', [
            'personnalite'   => $personnalite,
            'listeEtudiants' => $listeEtudiants
        ]);
    }

    /**
     * @param Request $request
     * @param Personnalite $personnalite
     * @return Response
     * @Route("/personnalite/edit/{id}", name="personnalite_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Personnalite $personnalite): Response
    {
        $form = $this->createForm(PersonnaliteType::class, $personnalite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('personnalite_index', [
                'id' => $personnalite->getId(),
            ]);
        }

        return $this->render('admin/personnalite/edit.html.twig', [
            'personnalite' => $personnalite,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Personnalite $personnalite
     * @return Response
     *
     * @Route("/personnalite/trash/{id}", name="personnalite_move_to_trash", methods={"GET", "POST"})
     */
    public function moveToTrash(Request $request, Personnalite $personnalite): Response
    {
        $personnalite->setStatus(Personnalite::TYPE_TRASH);
        $this->entityManager->flush();

        return $this->redirectToRoute('personnalite_index');
    }

    /**
     * @param Request $request
     * @param Personnalite $personnalite
     * @return Response
     *
     * @Route("/personnalite/restore/{id}", name="personnalite_restore", methods={"GET", "POST"})
     */
    public function restore(Request $request, Personnalite $personnalite): Response
    {
        $personnalite->setStatus(Personnalite::TYPE_PUBLIC);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin_trash');
    }

    /**
     * @param Request $request
     * @param Personnalite $personnalite
     * @return Response
     *
     * @Route("/personnalite/{id}", name="personnalite_delete", methods={"POST","DELETE"})
     */
    public function delete(Request $request, Personnalite $personnalite): Response
    {
        if ($this->isCsrfTokenValid('delete' . $personnalite->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($personnalite);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('personnalite_index');
    }


}
