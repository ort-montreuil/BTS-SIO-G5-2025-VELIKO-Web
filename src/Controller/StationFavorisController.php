<?php

namespace App\Controller;

use App\Entity\StationUser;
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
        $stationUserRepository = $this->entityManager->getRepository(StationUser::class);
        $response = $this->client->request('GET', $_ENV['API_VELIKO_URL'] . "/stations");
        $stations = $response->toArray();
        $favoriteStationIds = [];

        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $userId = $this->getUser()->getId();
            $favoriteStations = $stationUserRepository->findStationsByUserId($userId);
            $favoriteStationIds = array_column($favoriteStations, 'idStation');
        }

        $stations = array_filter($stations, function ($station) use ($favoriteStationIds) {
            return !in_array($station['station_id'], $favoriteStationIds);
        });

        $query = $request->query->get('query', '');
        if ($query) {
            $stations = array_filter($stations, function ($station) use ($query) {
                return stripos($station['name'], $query) !== false;
            });
        }

        if ($request->isMethod('POST')) {
            $user = $this->getUser();
            $userId = $user->getId();
            $stationId = $request->get("idStations");
            $favoriAction = $request->get("favori");

            if ($favoriAction === 'add' && $stationId) {
                $stationUser = new StationUser();
                $stationUser->setIdStation($stationId);
                $stationUser->setIdUser($userId);
                $this->entityManager->persist($stationUser);
                $this->entityManager->flush();
                $this->addFlash('success', 'Station ajoutée aux favoris.');
            }

            if ($favoriAction === 'remove' && $stationId) {
                $stationUserRepository->deleteStationByStationId($stationId);
                $this->addFlash('success', 'Station retirée des favoris.');
            }

            return $this->redirectToRoute('app_station_favoris');
        }

        return $this->render('station_favoris/index.html.twig', [
            'controller_name' => 'StationFavorisController',
            'stations' => $stations,
        ]);
    }
}
