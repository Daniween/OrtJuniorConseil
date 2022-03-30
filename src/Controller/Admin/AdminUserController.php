<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Form\AdminType;
use App\Repository\AdminRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class AdminUserController
 * @package App\Controller
 * @IsGranted("ROLE_ADMIN", statusCode=403, message="Vous n'avez pas accès à cette page.")
 * @Route("/admin")
 */
class AdminUserController extends AbstractController
{

    private EntityManagerInterface $entityManager;
    protected UserPasswordHasherInterface $passwordHasher;

    /**
     * BackUserController constructor.
     * @param UserPasswordHasherInterface $passwordHasher
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    /**
     * @param AdminRepository $adminRepository
     * @return Response
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Vous n'avez pas accès à cette page.")
     *
     * @Route("/user", name="admin_user_index", methods={"GET"})
     */
    public function index(AdminRepository $adminRepository): Response
    {

        return $this->render('admin/users/index.html.twig', [
            'adminUsers' => $adminRepository->findAll(),
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Vous n'avez pas accès à cette page.")
     *
     * @Route("/user/new", name="admin_user_new", methods={"GET","POST"})
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        $admin = new Admin();
        $form = $this->createForm(AdminType::class, $admin, ['validation_groups' => 'create']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Pour encoder le mot de passe et le sauvegarder de manière sécurisée
            $admin->setPassword($this->passwordHasher->hashPassword($admin, $form->get('plainPassword')->getData()));
            $admin->setCreateAt(new DateTime());
            $this->entityManager->persist($admin);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/users/new.html.twig', [
            'admin'     => $admin,
            'form'      => $form->createView(),
            'error'     => $form->getErrors()
        ]);
    }

    /**
     * @param Admin $admin
     * @return Response
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Vous n'avez pas accès à cette page.")
     *
     * @Route("/user/{id}", name="admin_user_show", methods={"GET"})
     */
    public function show(Admin $admin): Response
    {
        return $this->render('admin/users/show.html.twig', [
            'admin' => $admin,
        ]);

    }

    /**
     * @param Request $request
     * @param Admin $admin
     * @return Response
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Vous n'avez pas accès à cette page.")
     * @Route("/user/edit/{id}", name="admin_user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Admin $admin): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(AdminType::class, $admin, ['validation_groups' => 'Default']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('plainPassword')->getData()) {
                // Pour encoder le mot de passe et le sauvegarder de manière sécuriser
                $admin->setPassword($this->passwordHasher->hashPassword($admin, $form->get('plainPassword')->getData()));
            }

            if ($form->get('plainPassword')->getData()) {
                $this->entityManager->flush();
            }

            $this->entityManager->flush();

            return $this->redirectToRoute('admin_user_show', [
                'id' => $admin->getId(),
            ]);
        }

        return $this->render('admin/users/edit.html.twig', [
            'admin' => $admin,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Admin $admin
     * @return Response
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Vous n'avez pas accès à cette page.")
     *
     * @Route("/user/trash/{id}", name="admin_user_move_to_trash", methods={"GET", "POST"})
     */
    public function moveToTrash(Request $request, Admin $admin): Response
    {
        $admin->setStatus(Admin::TYPE_TRASH);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin_user_index');
    }

    /**
     * @param Request $request
     * @param Admin $admin
     * @return Response
     *
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Vous n'avez pas accès à cette page.")
     *
     * @Route("/user/restore/{id}", name="admin_user_restore", methods={"GET", "POST"})
     */
    public function restore(Request $request, Admin $admin): Response
    {
        $admin->setStatus(1);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin_trash');
    }

    /**
     * @param Request $request
     * @param Admin $admin
     * @return Response
     * @IsGranted("ROLE_ADMIN", statusCode=403, message="Vous n'avez pas accès à cette page.")
     *
     * @Route("/user/{id}", name="admin_user_delete", methods={"POST","DELETE"})
     */
    public function delete(Request $request, Admin $admin): Response
    {
        if ($this->isCsrfTokenValid('delete' . $admin->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($admin);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('admin_trash');
    }
}
