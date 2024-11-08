<?php

namespace App\Controller;

use App\Entity\StationUser;
use App\Entity\User;
use App\Repository\StationUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
        /** @var StationUserRepository $stationUserRepository */
        $stationUserRepository = $this->entityManager->getRepository(StationUser::class);
        $response = $this->client->request('GET', $_ENV['API_VELIKO_URL'] . "/stations");
        $stations = $response->toArray();

        // Get the favorite stations of the logged-in user
        $favoriteStationIds = [];
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $userId = $this->getUser()->getId();
            $favoriteStations = $stationUserRepository->findStationsByUserId($userId);
            $favoriteStationIds = array_column($favoriteStations, 'idStation');
        }

        // Filter out favorite stations
        $stations = array_filter($stations, function ($station) use ($favoriteStationIds) {
            return !in_array($station['station_id'], $favoriteStationIds);
        });

        // Filter stations based on the search query
        $query = $request->query->get('query', '');
        if ($query) {
            $stations = array_filter($stations, function ($station) use ($query) {
                return stripos($station['name'], $query) !== false;
            });
        }

        return $this->render('station_favoris/index.html.twig', [
            'controller_name' => 'StationFavorisController',
            'stations' => $stations,
            'mesStations' => 'MesStationsController',
        ]);
    }
}