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
        $response = $this->client->request('GET', $_ENV['API_VELIKO_URL'] . "/stations");
        $stations = $response->toArray();

        // Filtrer les stations selon la requête
        $query = $request->query->get('query', '');
        if ($query) {
            $stations = array_filter($stations, function ($station) use ($query) {
                return stripos($station['name'], $query) !== false;
            });
        }

        if ($request->isMethod('POST')) {
            /** @var StationUserRepository $stationUserRepository */
            $stationUserRepository = $this->entityManager->getRepository(StationUser::class);
            /** @var User $user */
            $user = $this->getUser();
            $userId = $user->getId();

            // Vérifier si une station doit être ajoutée ou retirée
            $stationId = $request->get("idStations");
            $favoriAction = $request->get("favori"); // "add" ou "remove"

            if ($favoriAction === 'add') {
                // Ajouter une station aux favoris
                $stationUser = new StationUser();
                $stationUser->setIdStation($stationId);
                $stationUser->setIdUser($userId);
                $this->entityManager->persist($stationUser);
                $this->entityManager->flush();
                $this->addFlash('success', 'Station ajoutée aux favoris.');
            } elseif ($favoriAction === 'remove') {
                // Retirer une station des favoris
                $stationUser = $stationUserRepository->findOneBy(['idStation' => $stationId, 'idUser' => $userId]);
                if ($stationUser) {
                    $this->entityManager->remove($stationUser);
                    $this->entityManager->flush();
                    $this->addFlash('success', 'Station retirée des favoris.');
                }
            }

            return $this->redirectToRoute('app_station_favoris');
        }

        return $this->render('station_favoris/index.html.twig', [
            'controller_name' => 'StationFavorisController',
            'stations' => $stations,
            'mesStations' => 'MesStationsController',
        ]);
    }
}