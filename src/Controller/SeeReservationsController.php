<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Station;
use App\Repository\StationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SeeReservationsController extends AbstractController
{
    private HttpClientInterface $client;
    private EntityManagerInterface $entityManager;

    public function __construct(HttpClientInterface $client, EntityManagerInterface $entityManager)
    {
        $this->client = $client;
        $this->entityManager = $entityManager;
    }

    #[Route('/see/reservations', name: 'app_see_reservations')]
    public function index(): Response
    {
        $user = $this->getUser();
        $reservationRepository = $this->entityManager->getRepository(Reservation::class);
        /** @var StationRepository $stationRepository */
        $stationRepository = $this->entityManager->getRepository(Station::class);
        $reservation = $reservationRepository->getReservationsByUserEmail($user->getEmail());
        $reservations = array();

        foreach ($reservation as $res){
            /** @var Reservation $res */
            $reservations[] = [
                "id" => $res->getId(),
                "date" => $res->getDate(),
                "heureDebut" => $res->getHeureDebut(),
                "heureFin" => $res->getHeureFin(),
                "idStationDepart" => $stationRepository->getStationNameById($res->getIdStationDepart())[0]["name"],
                "idStationArrivee" => $stationRepository->getStationNameById($res->getIdStationArrivee())[0]["name"],
                "type" => $res->getClearType($res->getType())
            ];

        }
        return $this->render('see_reservations/index.html.twig', [
            'controller_name' => 'SeeReservationsController',
            "reservations" => $reservations
        ]);
    }
}
