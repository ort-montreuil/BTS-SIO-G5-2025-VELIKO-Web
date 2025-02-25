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
        // Récupérer l'utilisateur actuellement connecté
        $user = $this->getUser();
        if ($user && $user->isMustChangePassword()) {
            return $this->redirectToRoute('app_forced');
        }

        // Vérifier si la requête est de type POST
        if ($request->isMethod('POST')) {
            // Récupérer les données du formulaire
            $idStationDepart = $request->request->get('station');
            $idStationFin = $request->request->get('stationEnd');
            $velos = $this->client->request('GET', $_ENV['API_VELIKO_URL'] . "/velos");
            $velos = $velos->toArray();
            $auth_token = json_decode(file_get_contents("../var/api/configDataset.json"), true)['token']['default']; // Récupérer le token d'authentification
            $veloFound = false;

            // Parcourir les vélos pour trouver un vélo disponible
            foreach ($velos as $velo) {
                if ($velo['station_id_available'] == $idStationDepart) {
                    if ($velo['status'] == "available") {
                        if ($velo['type'] != $request->get('type')) {
                            continue;
                        } else {
                            // Réserver le vélo
                            $this->client->request("PUT", $_ENV['API_VELIKO_URL'] . "/velo/" . $velo['velo_id'] . "/location", [
                                'headers' => ["Authorization" => $auth_token]
                            ]);
                            $this->addFlash('success', "Votre réservation a été effectuée avec succès");
                            // Restaurer le vélo à la station de fin
                            $this->client->request("PUT", $_ENV['API_VELIKO_URL'] . "/velo/" . $velo['velo_id'] . "/restore/" . $idStationFin, [
                                'headers' => ["Authorization" => $auth_token],
                            ]);
                            $veloFound = true;
                            break;
                        }
                    } else {
                        // Dans le cas où aucun vélo n'est disponible dans la station
                        /** @var StationRepository $stationRepository */
                        $stationRepository = $this->entityManager->getRepository(Station::class);
                        $this->addFlash("danger", "Aucun vélo n'est disponible dans la station " . $stationRepository->getStationNameById($velo['station_id_available'])[0]["name"]);
                        return $this->redirectToRoute('app_reservation');
                    }
                }
            }

            // Si aucun vélo du type demandé n'a été trouvé
            if (!$veloFound) {
                $this->addFlash('danger', "Le type de vélo que vous avez choisi n'est pas disponible à cette station");
                return $this->redirectToRoute('app_reservation');
            }

            // Créer une nouvelle réservation
            $reservation = new Reservation();
            $reservation->setEmailUser($this->getUser()->getEmail());
            $reservation->setDate(new DateTime($request->request->get('date')));
            $reservation->setHeureDebut(new DateTime($request->request->get('time')));
            $reservation->setHeureFin(new DateTime($request->request->get('timeEnd')));
            $reservation->setIdStationDepart($idStationDepart);
            $reservation->setIdStationArrivee($idStationFin);
            $reservation->setType($request->get('type'));

            // Vérifier que l'heure de fin est après l'heure de début
            if ($reservation->getHeureFin() < $reservation->getHeureDebut()) {
                $request->getSession()->getFlashBag()->clear();
                $this->addFlash("danger", "L'heure de fin ne peut pas être inférieur à la date de début");
                return $this->redirectToRoute("app_reservation");
            } elseif ($reservation->getDate() < new DateTime()) {
                // Vérifier que la date de réservation n'est pas dans le passé
                $request->getSession()->getFlashBag()->clear();
                $this->addFlash("danger", "La date de réservation ne peut pas être antérieure à la date actuelle");
                return $this->redirectToRoute("app_reservation");
            } else {
                // Enregistrer la réservation en base de données
                $this->entityManager->persist($reservation);
                $this->entityManager->flush();
            }

            return $this->redirectToRoute('app_reservation');
        }

        // Récupérer la liste des stations
        $response = $this->client->request('GET', $_ENV['API_VELIKO_URL'] . "/stations");
        $stations = $response->toArray();

        // Rendre la vue de la page de réservation
        return $this->render('reservation/index.html.twig', [
            'controller_name' => 'ReservationController',
            'stations' => $stations,
        ]);
    }
}