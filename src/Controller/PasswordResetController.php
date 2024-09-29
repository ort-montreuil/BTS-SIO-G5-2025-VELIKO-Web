<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class PasswordResetController extends AbstractController
{
    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if ($user) {
                // Générer un token de réinitialisation
                $resetToken = bin2hex(random_bytes(16));
                $user->setResetToken($resetToken);
                $entityManager->flush();

                // Générer le lien de réinitialisation
                $resetUrl = $this->generateUrl('app_reset_password', ['token' => $resetToken], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL);

                // Envoyer l'email
                $email = (new Email())
                    ->from('no-reply@votre-site.com')
                    ->to($user->getEmail())
                    ->subject('Réinitialisation du mot de passe')
                    ->html('<p>Pour réinitialiser votre mot de passe, cliquez sur ce lien : <a href="' . $resetUrl . '">Réinitialiser</a></p>');

                $mailer->send($email);
            }

            return $this->render('security/forgot_password_confirm.html.twig');
        }

        return $this->render('security/forgot_password.html.twig');
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function resetPassword(string $token, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si le token est valide
        $user = $entityManager->getRepository(User::class)->findOneBy(['resetToken' => $token]);

        if (!$user) {
            return new Response('Lien invalide ou expiré.');
        }

        if ($request->isMethod('POST')) {
            // Récupérer et hacher le nouveau mot de passe
            $newPassword = $request->request->get('password');
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT); // Hachage du mot de passe

            // Mettre à jour l'utilisateur
            $user->setPassword($hashedPassword);
            $user->setResetToken(null); // Supprime le token
            $entityManager->flush(); // Sauvegarde en base de données


            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig', ['token' => $token]);
    }
}
