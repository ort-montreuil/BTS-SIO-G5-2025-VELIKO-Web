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
        $stations = $stationsRepository->findBy(['emailuser' => $user]);

        // Extraire les IDs des stations
        $station_id = array_map(function($station) {
            return $station->getId();
        }, $stations);

//        $response = $this->client->request('GET', 'http://localhost:9042/api/station/{id}');
        return $this->render('mes_stations/index.html.twig', [
            'controller_name' => 'MesStationsController',
            'station_id' => $station_id
        ]);
    }
}
