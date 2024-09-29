<?php

namespace App\Controller;

use App\Entity\User; // Assurez-vous que vous avez importé votre entité User
use App\Form\UserType; // Assurez-vous d'avoir créé un formulaire UserType
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParametreController extends AbstractController
{
    #[Route('/parametre', name: 'app_parametre')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $parametres = [
            ['id' => 1, 'name' => 'Changer d\'attribut'],
            ['id' => 2, 'name' => 'Changer de mot de passe'],
            ['id' => 3, 'name' => 'Déconnexion'],
            ['id' => 4, 'name' => 'À propos'],
        ];

        $form = null;
        if ($request->query->get('action') === 'changer-attribut') {
            $user = $this->getUser();
            $form = $this->createFormBuilder($user)
                ->add('nom', TextType::class, ['data' => $user->getNom()])
                ->add('prenom', TextType::class, ['data' => $user->getPrenom()])
                ->add('adresse', TextType::class, ['data' => $user->getAdresse()])
                ->add('code_postal', TextType::class, ['data' => $user->getCodePostal()])
                ->add('ville', TextType::class, ['data' => $user->getVille()])
                ->add('mettre_a_jour', SubmitType::class, [
                    'label' => 'Mettre à jour'
                ])
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();
                $this->addFlash('success', 'Vos informations ont été mises à jour avec succès.');
                return $this->redirectToRoute('app_parametre');
            }
        }

        return $this->render('parametre/index.html.twig', [
            'parametres' => $parametres,
            'form' => $form ? $form->createView() : null,
        ]);
    }
}
