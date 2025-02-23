<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Station;
use App\Repository\StationRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

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
        $user = $this->getUser();
        if ($user && $user->isMustChangePassword()) {
            return $this->redirectToRoute('app_forced');
        }

        if ($request->isMethod('POST')) {
            $idStationDepart = $request->request->get('station');
            $idStationFin = $request->request->get('stationEnd');
            $velos = $this->client->request('GET', $_ENV['API_VELIKO_URL'] . "/velos");
            $velos = $velos->toArray();
            $auth_token = json_decode(file_get_contents("../var/api/configDataset.json"), true)['token']['default'];

            $veloFound = false;

            foreach ($velos as $velo) {
                if ($velo['station_id_available'] == $idStationDepart) {
                    if ($velo['status'] == "available") {
                        if ($velo['type'] != $request->get('type')) {
                            continue;
                        } else {
                            $this->client->request("PUT", $_ENV['API_VELIKO_URL'] . "/velo/" . $velo['velo_id'] . "/location", [
                                'headers' => ["Authorization" => $auth_token]
                            ]);
                            $this->addFlash('success', "Votre réservation a été effectuée avec succès");
                            $this->client->request("PUT", $_ENV['API_VELIKO_URL'] . "/velo/" . $velo['velo_id'] . "/restore/" . $idStationFin, [
                                'headers' => ["Authorization" => $auth_token],
                            ]);
                            $veloFound = true;
                            break;
                        }
                    } else {
                        /** @var StationRepository $stationRepository */
                        $stationRepository = $this->entityManager->getRepository(Station::class);
                        $this->addFlash("danger", "Aucun vélo n'est disponible dans la station " . $stationRepository->getStationNameById($velo['station_id_available'])[0]["name"]);
                        return $this->redirectToRoute('app_reservation');
                    }
                }
            }

            if (!$veloFound) {
                $this->addFlash('danger', "Le type de vélo que vous avez choisi n'est pas disponible à cette station");
                return $this->redirectToRoute('app_reservation');
            }

            $reservation = new Reservation();
            $reservation->setEmailUser($this->getUser()->getEmail());
            $reservation->setDate(new DateTime($request->request->get('date')));
            $reservation->setHeureDebut(new DateTime($request->request->get('time')));
            $reservation->setHeureFin(new DateTime($request->request->get('timeEnd')));
            $reservation->setIdStationDepart($idStationDepart);
            $reservation->setIdStationArrivee($idStationFin);
            $reservation->setType($request->get('type'));
            $this->entityManager->persist($reservation);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_reservation');
        }

        $response = $this->client->request('GET', $_ENV['API_VELIKO_URL'] . "/stations");
        $stations = $response->toArray();

        return $this->render('reservation/index.html.twig', [
            'controller_name' => 'ReservationController',
            'stations' => $stations,
        ]);

    }
}