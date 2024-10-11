<?php

namespace App\Controller;

use App\Entity\Station;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MesStationsController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/mes/stations', name: 'app_mes_stations')]
    public function index(): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Récupérer les stations favorites de l'utilisateur depuis la base de données
        $stationsRepository = $this->entityManager->getRepository(Station::class);
        $userStations = $stationsRepository->findBy(['emailuser' => $user->getEmail()]);

        return $this->render('mes_stations/index.html.twig', [
            'controller_name' => 'MesStationsController',
            'stations' => $userStations,
        ]);
    }
}
