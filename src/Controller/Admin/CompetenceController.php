<?php

namespace App\Controller\Admin;

use App\Entity\Competence;
use App\Form\CompetenceType;
use App\Repository\CompetenceRepository;
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
 * Class CompetenceController
 * @package App\Controller\back
 * @IsGranted("ROLE_ADMIN", statusCode=403, message="Vous n'avez pas accès à cette page.")
 *
 * @Route("/admin")
 */
class CompetenceController extends AbstractController
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
     * @param CompetenceRepository $competenceRepository
     * @return Response
     *
     *
     * @Route("/competence", name="competence_index", methods={"GET"})
     */
    public function index(CompetenceRepository $competenceRepository): Response
    {
        return $this->render('admin/competence/index.html.twig', [
            'competences' => $competenceRepository->findAll(),
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     *
     *
     * @Route("/competence/new", name="competence_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $competence = new Competence();
        $form = $this->createForm(CompetenceType::class, $competence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->persist($competence);
            $this->entityManager->flush();

            return $this->redirectToRoute('competence_index');
        }

        return $this->render('admin/competence/new.html.twig', [
            'competence' => $competence,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Competence $competence
     * @return Response
     *
     * @Route("/competence/{id}", name="competence_show", methods={"GET"})
     */
    public function show(Competence $competence): Response
    {
        $listeEtudiants = $competence->getEtudiantCompetences()->getIterator();

        return $this->render('admin/competence/show.html.twig', [
            'competence'     => $competence,
            'listeEtudiants' => $listeEtudiants
        ]);
    }

    /**
     * @param Request $request
     * @param Competence $competence
     * @return Response
     * @throws Exception
     *
     *
     * @Route("/competence/edit/{id}", name="competence_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Competence $competence): Response
    {
        $form = $this->createForm(CompetenceType::class, $competence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('competence_index', [
                'id' => $competence->getId(),
            ]);
        }

        return $this->render('admin/competence/edit.html.twig', [
            'competence' => $competence,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Competence $competence
     * @return Response
     *
     * @Route("/competence/trash/{id}", name="competence_move_to_trash", methods={"GET", "POST"})
     */
    public function moveToTrash(Request $request, Competence $competence): Response
    {
        $competence->setStatus(Competence::TYPE_TRASH);
        $this->entityManager->flush();

        return $this->redirectToRoute('competence_index');
    }

    /**
     * @param Request $request
     * @param Competence $competence
     * @return Response
     *
     * @Route("/competence/restore/{id}", name="competence_restore", methods={"GET", "POST"})
     */
    public function restore(Request $request, Competence $competence): Response
    {
        $competence->setStatus(Competence::TYPE_PUBLIC);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin_trash');
    }

    /**
     * @param Request $request
     * @param Competence $competence
     * @return Response
     *
     * @Route("/competence/{id}", name="competence_delete", methods={"POST","DELETE"})
     */
    public function delete(Request $request, Competence $competence): Response
    {
        if ($this->isCsrfTokenValid('delete' . $competence->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($competence);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('competence_index');
    }


}
