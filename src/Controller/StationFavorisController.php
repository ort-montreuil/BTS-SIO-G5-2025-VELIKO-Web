<?php

namespace App\Controller;

use App\Entity\Station;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class StationFavorisController extends AbstractController
{
    private HttpClientInterface $client;
    private EntityManagerInterface $entityManager;

    public function __construct(HttpClientInterface $client, EntityManagerInterface $entityManager)
    {
        $this->client = $client;
        $this->entityManager = $entityManager;
    }

    #[Route('/station/favoris', name: 'app_station_favoris')]
    public function index(Request $request): Response
    {
        $response = $this->client->request('GET', 'http://localhost:9042/api/stations');
        $stations = $response->toArray();

        $query = $request->query->get('query', '');
        if ($query) {
            $stations = array_filter($stations, function ($station) use ($query) {
                return stripos($station['name'], $query) !== false;
            });
        }

        if ($request->isMethod('POST')) {
            $selectedStations = $request->request->all('stations');

            if ($selectedStations) {
                $userEmail = $this->getUser()->getEmail();
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userEmail]);

                foreach ($selectedStations as $stationId) {
                    // Validate that the stationId is an integer
                    if (!is_numeric($stationId)) {
                        continue;
                    }

                    // Check if the station already exists in the EntityManager
                    $existingStation = $this->entityManager->getRepository(Station::class)->find($stationId);
                    if ($existingStation) {
                        $stationFavoris = $existingStation;
                    } else {
                        $stationFavoris = new Station();
                        $stationFavoris->setId((int)$stationId); // Ensure the ID is an integer
                    }
                    $stationFavoris->setEmailuser($user);

                    $this->entityManager->persist($stationFavoris);
                }
                $this->entityManager->flush();

                $this->addFlash('success', 'Stations ajoutées avec succès.');

                return $this->redirectToRoute('app_station_favoris');
            }
        }

        return $this->render('station_favoris/index.html.twig', [
            'controller_name' => 'StationFavorisController',
            'stations' => $stations,
        ]);
    }
}