<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailVerifier
{
    //Ce fichier gère l'envoi et la vérification des emails de confirmation pour les utilisateurs.
    private VerifyEmailHelperInterface $verifyEmailHelper;
    private MailerInterface $mailer;
    private EntityManagerInterface $entityManager;

    public function __construct( // Injection de dépendances
        VerifyEmailHelperInterface $verifyEmailHelper,
        MailerInterface $mailer,
        EntityManagerInterface $entityManager
    ) {
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
    }

    public function sendEmailConfirmation(string $verifyEmailRouteName, User $user, TemplatedEmail $email): void // Envoi d'email de confirmation
    {
        // Génération de la signature de l'URL
        $signatureComponents = $this->verifyEmailHelper->generateSignature( //permet de sécuriser l'URL
            $verifyEmailRouteName,
            (string) $user->getId(),
            $user->getEmail(),
            ['id' => $user->getId()]
        );

        // Ajout des paramètres à l'email
        $context = $email->getContext();
        $context['signedUrl'] = $signatureComponents->getSignedUrl();
        $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
        $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

        $email->context($context);

        // Envoi de l'email
        $this->mailer->send($email);
    }

    public function handleEmailConfirmation(Request $request, User $user): void // Vérification de l'email
    {
        try {
            $this->verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                (string) $user->getId(),
                $user->getEmail()
            );

            $user->setVerified(true);
            $user->setToken(null); // Optionnel : effacer le token après vérification
            $this->entityManager->flush();
        } catch (VerifyEmailExceptionInterface $e) {
            throw new \RuntimeException($e->getReason());
        }
    }


}