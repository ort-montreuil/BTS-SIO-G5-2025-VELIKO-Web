<?php

namespace App\Controller;

use App\Entity\Station;
use App\Entity\StationUser;
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
        $response = $this->client->request('GET', $_ENV['API_VELIKO_URL'] . "/stations");
        $stations = $response->toArray();

        $query = $request->query->get('query', '');
        if ($query) {
            $stations = array_filter($stations, function ($station) use ($query) {
                return stripos($station['name'], $query) !== false;
            });
        }

        if ($request->isMethod('POST')) {
            if ($request->isMethod('POST') && $request->get("idStations"))
            {
                /** @var User $user */
                $user = $this->getUser();
                $userId = $user->getId();
                $selectedStationsId = $request->get("idStations");
                $stationUser = new StationUser();
                $stationUser->setIdStation($selectedStationsId);
                $stationUser->setIdUser($userId);
                $this->entityManager->persist($stationUser);
                $this->entityManager->flush();
            }
            $selectedStations = $request->request->all('stations');
            if ($selectedStations) {
                $userId = $this->getUser()->getId();  // ID de l'utilisateur connecté

                foreach ($selectedStations as $stationId) {
                    // Valider que stationId est bien numérique
                    if (!is_numeric($stationId)) {
                        continue;
                    }

                    // Créer une nouvelle entrée dans StationUser
                    $stationUser = new StationUser();
                    $stationUser->setIdUser($userId);
                    $stationUser->setIdStation((int)$stationId);  // Convertir en entier si nécessaire

                    $this->entityManager->persist($stationUser);
                }

                $this->entityManager->flush();
                $this->addFlash('success', 'Stations ajoutées avec succès.');
                return $this->redirectToRoute('app_station_favoris');
            }
        }

        return $this->render('station_favoris/index.html.twig', [
            'controller_name' => 'StationFavorisController',
            'stations' => $stations,
            'mesStations' => 'MesStationsController',
        ]);
    }
}