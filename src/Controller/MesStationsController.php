<?php

namespace App\Controller;

use App\Entity\Station;
use App\Entity\StationUser;
use App\Entity\User;
use App\Repository\StationRepository;
use App\Repository\StationUserRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\DatabaseDoesNotExist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MesStationsController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    #[Route('/mes/stations', name: 'app_mes_stations')]
    public function index(): Response
    {
        // Récupérer l'utilisateur connecté
        /** @var User $user */ //sans cela, $user n'est pas reconnu comme un objet de la classe "User"
        $user = $this->getUser();

        // Rediriger l'utilisateur s'il doit changer son mot de passe
        if ($user && $user->isMustChangePassword()) {
            return $this->redirectToRoute('app_forced');
        }

        $userId = $user->getId();
        /** @var StationUserRepository $stationUserRepository */
        $stationUserRepository = $this->entityManager->getRepository(StationUser::class);

        $stationNames = [];
        // Récupérer les stations favorites de l'utilisateur
        for ($i = 0; $i < count($stationUserRepository->findStationsByUserId($userId)); $i++) { //on parcourt les stations favorites de l'utilisateur
            $idStation = $stationUserRepository->findStationsByUserId($userId)[$i]["idStation"];
            $stationName = $stationUserRepository->findStationNameById($idStation)[0]["name"];
            $stationNames[] = [
                'name' => $stationName,
                'id' => $idStation
            ];
        }

        // Rendre la vue avec les stations de l'utilisateur
        return $this->render('mes_stations/index.html.twig', [
            'controller_name' => 'MesStationsController',
            'station_names' => $stationNames
        ]);
    }

    #[Route('/station/delete/{id}', name: 'app_station_delete', methods: ['POST'])]
    public function delete(int $id, Request $request): Response
    {
        /** @var StationUserRepository $stationUserRepository */
        $stationUserRepository = $this->entityManager->getRepository(StationUser::class);

        /** @var User $user */
        $user = $this->getUser();
        $userId = $user->getId();

        $idStation = $request->get("id");

        if ($idStation) {
//            $this->entityManager->remove($idStation); impossible, car on n'a que des id dans l'entité stationUser pas d'object user et station
            // Supprimer la station de l'utilisateur
            $stationUserRepository->deleteStationByStationId($idStation);
            $this->entityManager->flush();

            $this->addFlash('Succès', 'Station supprimée.');
        } else {
            $this->addFlash('Erreur', 'Station non trouvée.');
        }

        // Rediriger vers la liste des stations de l'utilisateur
        return $this->redirectToRoute('app_mes_stations');
    }
}