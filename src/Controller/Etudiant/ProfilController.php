<?php

namespace App\Controller\Etudiant;

use App\Form\EtudiantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ProfilController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;

    /**
     * ProfilController constructor.
     * @param UserPasswordHasherInterface $passwordHasher
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }


    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/etudiant/profil", name="etudiant_profil", methods={"GET","POST"})
     */
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        if(!$user) {
            return $this->redirectToRoute("etudiant_login");
        }

        $user = $this->getUser();
        $oldAvatar = $user->getAvatar();
        $oldDocument  = $user->getDocument();

        $form = $this->createForm(EtudiantType::class, $user, ['validation_groups' => 'Default']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('avatar')->isEmpty()) {
                $user->setAvatar($oldAvatar);
            } else {
                /** @var UploadedFile $file */
                $file = $form->get('avatar')->getData();

                $extensionFile = $file->guessExtension();
                $fileName = $this->generateUniqueFileName() . '.' . $extensionFile;

                try {
                    $file->move(
                        $this->getParameter('avatars_directory'),
                        $fileName
                    );

                } catch (FileException $e) {
                    dd($e->getMessage());
                }

                if (!empty($oldAvatar) and $oldAvatar !== null) {
                    unlink($this->getParameter('avatars_directory')."/".$oldAvatar);
                }

                $user->setAvatar($fileName);

                $this->addFlash('success', 'Votre photo de profil a bien été enregistrée !');
            }

            if ($form->get('document')->isEmpty()) {
                $user->setDocument($oldDocument);
            } else {
                /** @var UploadedFile $file */
                $file = $form->get('document')->getData();

                $extensionFile = $file->guessExtension();
                $fileNameResume = $this->generateUniqueFileName() . '.' . $extensionFile;

                try {
                    $file->move(
                        $this->getParameter('documents_directory'),
                        $fileNameResume
                    );

                } catch (FileException $e) {
                    dd($e->getMessage());
                }

                if (!empty($oldDocument)) {
                    unlink($this->getParameter('documents_directory')."/".$oldDocument);
                }

                $user->setDocument($fileNameResume);

                $this->addFlash('success', 'Votre CV a bien été enregistré !');
            }

            if ($form->get('plainPassword')->getData()) {
                $user->setPassword($this->passwordHasher->hashPassword($user, $form->get('plainPassword')->getData()));
            }

            // si l'étudiant a rempli son nom, son prénom et son niveau d'étude : son profil est considéré comme complété
            if (!$form->get('name')->isEmpty() and !$form->get('firstName')->isEmpty() and !$form->get('etude')->isEmpty()) {
                $user->setCompleted(true);
            }

            $this->entityManager->flush();
            $this->addFlash('success', 'Votre profil à bien été mis à jour !');
            return $this->redirectToRoute('etudiant_profil', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render('etudiant/profil/index.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * @return string
     */
    private function generateUniqueFileName(): string
    {
        return md5(uniqid());
    }

}
