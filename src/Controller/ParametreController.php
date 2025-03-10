<?php

namespace App\Controller;

use App\Form\PasswordChange;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ParametreController extends AbstractController
{
    #[Route('/parametre/{action}', name: 'app_parametre', defaults: ['action' => null])]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, ?string $action, TokenStorageInterface $tokenStorage, SessionInterface $session): Response
    {
        $user = $this->getUser();
        if ($user && $user->isMustChangePassword()) { // Rediriger l'utilisateur s'il doit changer son mot de passe
            return $this->redirectToRoute('app_forced');
        }

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        // Définir les paramètres disponibles
        $parametres = [
            ['id' => 1, 'name' => 'Changer d\'attribut'],
            ['id' => 2, 'name' => 'Changer de mot de passe'],
            ['id' => 3, 'name' => 'Déconnexion'],
            ['id' => 4, 'name' => 'Supprimer mon compte'],
            ['id' => 5, 'name' => 'À propos'],
        ];

        $form = null;
        $showLogoutConfirmation = false;
        // Gérer les différentes actions
        if ($action === 'changer-attribut') {
            // Créer un formulaire pour changer les attributs de l'utilisateur et l'afficher dans le twig sans créer un nouveau fichier twig
            $form = $this->createFormBuilder($user)
                ->add('nom', TextType::class, ['data' => $user->getNom()]) //on ajoute les champs du formulaire
                ->add('prenom', TextType::class, ['data' => $user->getPrenom()])
                ->add('adresse', TextType::class, ['data' => $user->getAdresse()])
                ->add('code_postal', TextType::class, ['data' => $user->getCodePostal()])
                ->add('ville', TextType::class, ['data' => $user->getVille()])
                ->add('mettre_a_jour', SubmitType::class, [
                    'label' => 'Mettre à jour'
                ])
                ->getForm();

            $form->handleRequest($request); //on vérifie si le formulaire a été soumis
            if ($form->isSubmitted() && $form->isValid()) { //on vérifie si le formulaire est valide
                $entityManager->flush(); //on enregistre les modifications
                $this->addFlash('success', 'Vos informations ont été mises à jour avec succès.');
                return $this->redirectToRoute('app_parametre');
            }
        } elseif ($action === 'changer-mot-de-passe') {
            // Créer un formulaire pour changer le mot de passe, même principe que pour changer les attributs
            $form = $this->createForm(PasswordChange::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) { //on vérifie si le formulaire est valide
                $oldPassword = $form->get('entrezVotreAncienMotDePasse')->getData(); //on récupère les données du formulaire
                $newPassword = $form->get('entrezVotreNouveauMotDePasse')->getData();

                // Vérification de l'ancien mot de passe
                if ($passwordHasher->isPasswordValid($user, $oldPassword)) {
                    // Vérification des conditions du nouveau mot de passe
                    $passwordConditions = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*(),.?":{}|<>]).{12,}$/';

                    if (!preg_match($passwordConditions, $newPassword)) { //on vérifie si le mot de passe respecte les conditions
                        $this->addFlash('error', 'Le nouveau mot de passe doit contenir au moins 12 caractères, 1 majuscule, 1 chiffre, et 1 caractère spécial.');
                    } elseif ($oldPassword === $newPassword) {
                        $this->addFlash('error', 'Le nouveau mot de passe doit être différent de l\'ancien mot de passe.');
                    } else {
                        // Si toutes les conditions sont remplies
                        $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
                        $entityManager->flush();
                        $this->addFlash('success', 'Votre mot de passe a été changé avec succès.');
                        return $this->redirectToRoute('app_parametre');
                    }
                } else {
                    $this->addFlash('error', 'L\'ancien mot de passe est incorrect.');
                }
            }

        } elseif ($action === 'deconnexion') {
            $showLogoutConfirmation = true; // Afficher un message de confirmation pour la déconnexion
        } elseif ($action === 'supprimer-compte') {
            // Créer un formulaire pour supprimer le compte
            $form = $this->createFormBuilder()
                ->add('password', PasswordType::class, [
                    'label' => 'Entrez votre mot de passe pour confirmer',
                    'required' => true,
                ])
                ->add('supprimer', SubmitType::class, [
                    'label' => 'Supprimer mon compte',
                    'attr' => ['class' => 'btn btn-danger']
                ])
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $password = $form->get('password')->getData();

                if ($passwordHasher->isPasswordValid($user, $password)) {
                    // Anonymisation des données de l'utilisateur
                    $user->setNom('anonyme');
                    $user->setPrenom('anonyme');
                    $user->setAdresse('anonyme');
                    $user->setVille('anonyme');
                    $user->setEmail('anonyme');
                    $entityManager->flush();
                    $this->addFlash('success', 'Votre compte a été supprimé avec succès.');

                    // Déconnexion de l'utilisateur
                    $tokenStorage->setToken(null);
                    $session->invalidate();

                    return $this->redirectToRoute('app_logout');
                } else {
                    $this->addFlash('error', 'Le mot de passe est incorrect.');
                }
            }
        }
        // Rendre la vue avec les paramètres et le formulaire (s'il existe)
        return $this->render('parametre/index.html.twig', [
            'parametres' => $parametres,
            'form' => $form ? $form->createView() : null,
            'showLogoutConfirmation' => $showLogoutConfirmation,
            'action' => $action,
        ]);

    }
}