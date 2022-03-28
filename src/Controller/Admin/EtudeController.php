<?php

namespace App\Controller\Admin;

use App\Entity\Etude;
use App\Form\EtudeType;
use App\Repository\EtudeRepository;
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
 * Class EtudeController
 * @package App\Controller\back
 * @IsGranted("ROLE_ADMIN", statusCode=403, message="Vous n'avez pas accès à cette page.")
 *
 * @Route("/admin")
 */
class EtudeController extends AbstractController
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
     * @param EtudeRepository $etudeRepository
     * @return Response
     *
     *
     * @Route("/etude", name="etude_index", methods={"GET"})
     */
    public function index(EtudeRepository $etudeRepository): Response
    {
        return $this->render('admin/etude/index.html.twig', [
            'etudes' => $etudeRepository->findAll(),
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     *
     *
     * @Route("/etude/new", name="etude_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $etude = new Etude();
        $form = $this->createForm(EtudeType::class, $etude);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->persist($etude);
            $this->entityManager->flush();

            return $this->redirectToRoute('etude_index');
        }

        return $this->render('admin/etude/new.html.twig', [
            'etude' => $etude,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Etude $etude
     * @return Response
     *
     * @Route("/etude/{id}", name="etude_show", methods={"GET"})
     */
    public function show(Etude $etude): Response
    {
        $listeEtudiants = $etude->getEtudiants()->getIterator();

        return $this->render('admin/etude/show.html.twig', [
            'etude'          => $etude,
            'listeEtudiants' => $listeEtudiants
        ]);
    }

    /**
     * @param Request $request
     * @param Etude $etude
     * @return Response
     * @throws Exception
     *
     *
     * @Route("/etude/edit/{id}", name="etude_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Etude $etude): Response
    {
        $form = $this->createForm(EtudeType::class, $etude);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('etude_index', [
                'id' => $etude->getId(),
            ]);
        }

        return $this->render('admin/etude/edit.html.twig', [
            'etude' => $etude,
            'form'  => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Etude $etude
     * @return Response
     *
     * @Route("/etude/trash/{id}", name="etude_move_to_trash", methods={"GET", "POST"})
     */
    public function moveToTrash(Request $request, Etude $etude): Response
    {
        $etude->setStatus(Etude::TYPE_TRASH);
        $this->entityManager->flush();

        return $this->redirectToRoute('etude_index');
    }

    /**
     * @param Request $request
     * @param Etude $etude
     * @return Response
     *
     * @Route("/etude/restore/{id}", name="etude_restore", methods={"GET", "POST"})
     */
    public function restore(Request $request, Etude $etude): Response
    {
        $etude->setStatus(Etude::TYPE_PUBLIC);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin_trash');
    }

    /**
     * @param Request $request
     * @param Etude $etude
     * @return Response
     *
     * @Route("/etude/{id}", name="etude_delete", methods={"POST","DELETE"})
     */
    public function delete(Request $request, Etude $etude): Response
    {
        if ($this->isCsrfTokenValid('delete' . $etude->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($etude);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('etude_index');
    }


}
