<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = $security->getUser(); //récupère l'user déjà connecté

        if ($user && $user->isMustChangePassword()) {
            return $this->redirectToRoute('app_forced');
        }

        $user = new User(); //définis l'user comme un objet user pour le formulaire
        $form = $this->createForm(RegistrationFormType::class, $user); //crée le formulaire d'inscription pour la classe User spécifiquement grâce au fichier RegistrationFormType.php
        $form->handleRequest($request); //vérifie si le formulaire a été soumis

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            // Vérification des critères du mot de passe
            if (strlen($plainPassword) < 12 ||
                !preg_match('/[A-Z]/', $plainPassword) ||
                !preg_match('/[0-9]/', $plainPassword) ||
                !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $plainPassword)) {
                $this->addFlash('error', 'Votre mot de passe doit contenir minimum 12 caractères, 1 caractère spécial, 1 chiffre, et 1 majuscule.');
                return $this->redirectToRoute('app_register');
            }

            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setLastPasswordChanged(new DateTime());

            // Générer un token et l'enregistrer
            $token = bin2hex(random_bytes(16));
            $user->setToken($token);

            $entityManager->persist($user);
            $entityManager->flush();

            // Envoyer un e-mail de vérification avec le token encodé
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address($_ENV["MAILER_FROM_ADDRESS"], $_ENV["MAILER_FROM_NAME"]))
                    ->to($user->getEmail())
                    ->subject('Veuillez confirmer votre adresse e-mail')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
                    ->context(['token' => urlencode($token)]) // Encodage du token
            );

            $this->addFlash('success', 'Un e-mail de confirmation a été envoyé. Veuillez vérifier votre boîte de réception.');

            return $this->redirectToRoute('app_check_email');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        return $this->render('registration/check_email.html.twig');
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userId = $request->query->get('id');
        $user = $entityManager->getRepository(User::class)->find($userId);

        if (!$user) {
            $this->addFlash('verify_email_error', 'Utilisateur non trouvé ou token invalide.');
            return $this->redirectToRoute('app_register');
        }

        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user); //vérifie le token et l'adresse e-mail de l'utilisateur pour la confirmation
            $user->setToken(null);
            $user->setVerified(true);
            $entityManager->flush();

            $this->addFlash('success', 'Votre adresse e-mail a été vérifiée.');
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());
            return $this->redirectToRoute('app_register');
        }

        return $this->redirectToRoute('app_login');
    }
}
