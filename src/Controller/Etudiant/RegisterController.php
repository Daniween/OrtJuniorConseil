<?php

namespace App\Controller\Etudiant;

use App\Entity\Etudiant;
use App\Form\EtudiantRegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class RegisterController extends AbstractController
{

    private EntityManagerInterface $entityManager;
    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $passwordHasher;
    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    /**
     * @param UserPasswordHasherInterface $passwordHasher
     * @param MailerInterface $mailer
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->mailer         = $mailer;
    }

    /**
     * @param Request $request
     *
     * @param TokenGeneratorInterface $tokenGenerator
     * @return Response
     * @Route("/etudiant/register", name="etudiant_register", methods={"GET","POST"})
     */
    public function index(Request $request, TokenGeneratorInterface $tokenGenerator): Response
    {
        $newEtudiant = new Etudiant();
        $form = $this->createForm(EtudiantRegisterType::class, $newEtudiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $newEtudiant->setCreateAt(new \DateTime());
            //Quand le user est créé on lui attribue un token pour qu'il puisse créer son mot de passe à travers un lien envoyé par mail
            $token = $tokenGenerator->generateToken();
            $password = $tokenGenerator->generateToken();
            $newEtudiant->setResetToken($token);

            $newEtudiant->setPassword($this->passwordHasher->hashPassword($newEtudiant, $password));
            $this->entityManager->persist($newEtudiant);
            $this->entityManager->flush();

            $url = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
            //Envoi du mail avec le lien de réinitialisation de mot de passe au user
            $email = (new TemplatedEmail())
                ->from('MyForsides <contact@forsides-group.com>')
                ->to(new Address($newEtudiant->getEmail()))
                ->subject('Création de compte étudiant OrtSup')

                // path of the Twig template to render
                ->htmlTemplate('etudiant/mails/register.html.twig')

                // pass variables (name => value) to the template
                ->context([
                    'url' => $url
                ]);

            $this->mailer->send($email);

            $this->entityManager->persist($newEtudiant);
            $this->entityManager->flush();

            $this->addFlash('success', "Un email contenant un lien pour créer votre mot de passe et activer votre compte vient d'être envoyé");

            return $this->redirectToRoute('etudiant_login');
        }

        return $this->render('etudiant/security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
