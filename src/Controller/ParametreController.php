<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ParametreController extends AbstractController
{
    #[Route('/parametre', name: 'app_parametre')]
    public function index(): Response
    {
        // Exemple de liste de paramètres, cela pourrait venir de la base de données
        $parametres = [
            ['id' => 1, 'name' => 'Changer d\'attribut'],
            ['id' => 2, 'name' => 'Changer de mot de passe'],
            ['id' => 3, 'name' => 'Changer de thème'],
            ['id' => 4, 'name' => 'À propos']
        ];

        return $this->render('parametre/index.html.twig', [
            'parametres' => $parametres,
        ]);
    }
}
