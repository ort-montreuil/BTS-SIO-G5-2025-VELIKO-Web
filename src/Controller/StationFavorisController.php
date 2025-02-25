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
        // Récupérer le repository des stations favorites
        $stationUserRepository = $this->entityManager->getRepository(StationUser::class);

        // Faire une requête pour récupérer toutes les stations
        $response = $this->client->request('GET', $_ENV['API_VELIKO_URL'] . "/stations");
        $stations = $response->toArray();
        $favoriteStationIds = [];

        // Récupérer l'utilisateur actuellement connecté
        $user = $this->getUser();
        if ($user && $user->isMustChangePassword()) {
            return $this->redirectToRoute('app_forced');
        }

        // Vérifier si l'utilisateur est authentifié
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $userId = $this->getUser()->getId();
            // Récupérer les stations favorites de l'utilisateur
            $favoriteStations = $stationUserRepository->findStationsByUserId($userId);
            $favoriteStationIds = array_column($favoriteStations, 'idStation'); // Récupérer les IDs des stations favorites
        }

        // Filtrer les stations pour exclure celles qui sont déjà en favoris
        $stations = array_filter($stations, function ($station) use ($favoriteStationIds) {
            return !in_array($station['station_id'], $favoriteStationIds); // Exclure les stations qui ont un ID présent dans le tableau des favoris
        });

        // Récupérer la requête de recherche
        $query = $request->query->get('query', '');
        if ($query) {
            // Filtrer les stations en fonction de la requête de recherche
            $stations = array_filter($stations, function ($station) use ($query) {
                return stripos($station['name'], $query) !== false; // Vérifier si le nom de la station contient la requête de recherche
            });
        }

        // Vérifier si la requête est de type POST
        if ($request->isMethod('POST')) {
            $user = $this->getUser();
            $userId = $user->getId();
            $stationId = $request->get("idStations");
            $favoriAction = $request->get("favori");

            // Ajouter une station aux favoris si l'action est 'add'
            if ($favoriAction === 'add' && $stationId) {
                $stationUser = new StationUser();
                $stationUser->setIdStation($stationId);
                $stationUser->setIdUser($userId);
                $this->entityManager->persist($stationUser);
                $this->entityManager->flush();
                $this->addFlash('success', 'Station ajoutée aux favoris.');
            }

            // Retirer une station des favoris si l'action est 'remove'
            if ($favoriAction === 'remove' && $stationId) {
                $stationUserRepository->deleteStationByStationId($stationId);
                $this->addFlash('success', 'Station retirée des favoris.');
            }

            // Rediriger vers la route des stations favorites
            return $this->redirectToRoute('app_station_favoris');
        }

        // Rendre la vue avec les stations disponibles
        return $this->render('station_favoris/index.html.twig', [
            'controller_name' => 'StationFavorisController',
            'stations' => $stations,
        ]);
    }
}