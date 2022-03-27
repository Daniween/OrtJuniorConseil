<?php

namespace App\Controller\Etudiant;

use App\Entity\Etudiant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\Exception\JsonException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class EtudiantSecurityController extends AbstractController
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
     * @Route("/etudiant/login", name="etudiant_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {

            if ($this->getUser()->getActivate() === false) {
                $this->addFlash('warning', "Votre compte est désactivé.");

                return $this->redirectToRoute('etudiant_logout');
            }
            return $this->redirectToRoute('etudiant_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('etudiant/security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @param Request $request
     * @param MailerInterface $mailer
     * @param TokenGeneratorInterface $tokenGenerator
     * @return Response
     *
     * @Route("/forgotten_password", name="app_forgotten_password")
     * @throws TransportExceptionInterface
     * @throws JsonException
     */
    public function forgottenPassword(Request $request, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator): Response
    {

        if ($request->isMethod('POST')) {

            $email         = $request->request->get('email');
            $user          = $this->entityManager->getRepository(Etudiant::class)->findOneBy(['email' => $email]);

            if ($user === null) {
                $this->addFlash('danger', 'Vous n\'êtes pas encore inscrit');
                return $this->redirectToRoute('app_forgotten_password');
            }

            $token = $tokenGenerator->generateToken();

            try {
                $user->setResetToken($token);
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('etudiant_home');
            }

            $url = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

            $email = (new Email())
                ->from('OrtSup <contact@forsides-group.com>')
                ->to(new Address($user->getEmail()))
                ->subject('Réinitialisation de mot de passe')
                ->text('Voici le lien pour réinitialiser votre mot de passe : '. $url)
            ;

            $mailer->send($email);

            $this->addFlash('success', "Un email contenant un lien pour réinitialiser votre mot de passe vient d'etre envoyé");

            return $this->render('etudiant/security/forgotten_password.html.twig');

        } else {
            return $this->render('etudiant/security/forgotten_password.html.twig');
        }
    }

    /**
     * @param Request $request
     * @param string $token
     * @param UserPasswordHasherInterface $passwordHasher
     * @return RedirectResponse|Response
     *
     * @Route("/reset_password/{token}", name="app_reset_password")
     */
    public function resetPassword(Request $request, string $token, UserPasswordHasherInterface $passwordHasher)
    {
        if ($request->isMethod('POST')) {

            $user = $this->entityManager->getRepository(Etudiant::class)->findOneByResetToken($token);

            if ($user === null) {
                $this->addFlash('error', 'Le lien que vous utilisez semble expiré, renouvelez la demande de réinitialisation de mot de passe en saisissant votre email ce-dessous');
                return $this->redirectToRoute('app_forgotten_password');
            }

            $user->setResetToken(null);
            $user->setPassword($passwordHasher->hashPassword($user, $request->request->get('password')));
            if ($user->getActivate() === false) {
                $user->setActivate(true);
            }
            $this->entityManager->flush();

            $this->addFlash('success', 'Mot de passe mis à jour avec succès! Redirection dans 3 secondes...');

            return $this->render('etudiant/security/reset_password.html.twig', ['token' => null]);
        } else {
            return $this->render('etudiant/security/reset_password.html.twig', ['token' => $token]);
        }

    }

    /**
     * @Route("/etudiant/logout", name="etudiant_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}