<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\StationUser;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Reservation;

class ReservationController extends AbstractController
{
    private HttpClientInterface $client;
    private EntityManagerInterface $entityManager;

    public function __construct(HttpClientInterface $client, EntityManagerInterface $entityManager)
    {
        $this->client = $client;
        $this->entityManager = $entityManager;
    }

    #[Route('/reservation', name: 'app_reservation', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $reservation = new Reservation();
            $reservation->setEmailUser($this->getUser()->getEmail());
            $reservation->setDate(new \DateTime($request->request->get('date')));
            $reservation->setHeureDebut(new \DateTime($request->request->get('time')));
            $reservation->setHeureFin(new \DateTime($request->request->get('timeEnd')));
            $reservation->setIdStationDepart($request->request->get('station'));
            $reservation->setIdStationArrivee($request->request->get('stationEnd'));

            $this->entityManager->persist($reservation);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_reservation_success');
        }

        $response = $this->client->request('GET', $_ENV['API_VELIKO_URL'] . "/stations");
        $stations = $response->toArray();

        return $this->render('reservation/index.html.twig', [
            'controller_name' => 'ReservationController',
            'stations' => $stations,
        ]);
    }

    #[Route('/reservation/success', name: 'app_reservation_success')]
    public function success(): Response
    {
        return $this->render('reservation/success.html.twig');
    }
}