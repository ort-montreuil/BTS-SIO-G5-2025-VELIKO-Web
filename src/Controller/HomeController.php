<?php

namespace App\Controller;

use App\Repository\StationUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class HomeController extends AbstractController
{
    private StationUserRepository $stationUserRepository;
    private EntityManagerInterface $entityManager; //utile pour les opérations de base de données

    public function __construct(StationUserRepository $stationUserRepository, EntityManagerInterface $entityManager)
    {
        $this->stationUserRepository = $stationUserRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/blocked', name: 'app_blocked')] //route pour la page bloquée
    public function blocked(): Response
    {
        return $this->render('home/blocked.html.twig');
    }

    #[Route('/forced', name: 'app_forced')]
    public function forced(): Response
    {
        return $this->render('home/forced.html.twig');
    }

    #[Route('/', name: 'app_redirect_to_loading')]
    public function redirectToLoading(): Response
    {
        return $this->redirectToRoute('app_loading');
    }

    #[Route('/loading', name: 'app_loading')]
    public function loading(): Response
    {
        return $this->render('home/loading.html.twig');
    }

    #[Route('/change-password', name: 'app_change_password', methods: ['POST'])] //route pour changer le mot de passe
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) { //on vérifie si l'utilisateur est connecté
            throw new AccessDeniedException();
        }

        $user = $this->getUser();
        $newPassword = $request->request->get('new_password');
        $confirmPassword = $request->request->get('confirm_password');

        if ($newPassword !== $confirmPassword) { //on vérifie si les deux mots de passe correspondent
            $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
            return $this->redirectToRoute('app_forced');
        }

        //vérifie si le mot de passe respecte les critères de sécurité
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{12,}$/', $newPassword)) { //les critères sont : au moins 12 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial
            $this->addFlash('error', 'Le mot de passe doit contenir au moins 12 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.');
            return $this->redirectToRoute('app_forced');
        }

        //vérifie si le nouveau mot de passe est différent de l'ancien
        if ($passwordHasher->isPasswordValid($user, $newPassword)) {
            $this->addFlash('error', 'Le nouveau mot de passe ne peut pas être identique à l\'ancien mot de passe.');
            return $this->redirectToRoute('app_forced');
        }

        //hashage du mot de passe
        $encodedPassword = $passwordHasher->hashPassword($user, $newPassword); //on hash le mot de passe
        $user->setPassword($encodedPassword);
        $user->setMustChangePassword(false);

        $this->entityManager->persist($user); //on persiste les changements
        $this->entityManager->flush(); //on enregistre les changements

        $this->addFlash('success', 'Votre mot de passe a été changé avec succès.');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/home', name: 'app_home')] //route pour la page d'accueil
    public function execute(Request $request): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) { //on vérifie si l'utilisateur est connecté
            $user = $this->getUser();
            if ($user->isBlocked()) {
                return $this->redirectToRoute('app_blocked');
            }
            if ($user->isMustChangePassword()) {
                return $this->redirectToRoute('app_forced');
            }
        }
        // Configuration des requêtes cURL pour récupérer les données
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $_ENV["API_VELIKO_URL"] . "/stations",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        $stations_informations = curl_exec($curl); //cette variable contient les informations des stations
        $err = curl_error($curl);
        curl_close($curl);

        $stations_informations = json_decode($stations_informations, true); //une array associative contient une paire clé-valeur rendant les données plus faciles à manipuler

        $curl2 = curl_init();
        curl_setopt_array($curl2, [
            CURLOPT_URL => $_ENV["API_VELIKO_URL"] . "/stations/status",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        $stations_statuts = curl_exec($curl2); //cette variable contient les statuts des stations
        $err2 = curl_error($curl2);
        curl_close($curl2);

        $stations = [];
        $stations_statuts = json_decode($stations_statuts, true);

        foreach ($stations_informations as $infostat) {
            foreach ($stations_statuts as $infovelo) { //ces boucles imbriquées permettent de comparer les données des deux API
                if ($infostat['station_id'] == $infovelo['station_id']) { //on vérifie si les identifiants des stations correspondent (pour que les données ne soient pas mélangées).
                    $stations_data = [
                        'nom' => $infostat['name'],
                        'lat' => $infostat['lat'],
                        'lon' => $infostat['lon'],
                        'velodispo' => $infovelo['num_bikes_available'],
                        'velomecha' => $infovelo['num_bikes_available_types'][0]['mechanical'],
                        'veloelec' => $infovelo['num_bikes_available_types'][1]['ebike'],
                        'id' => $infostat['station_id']
                    ];
                    $stations[] = $stations_data;
                    break;
                }
            }
        }

        $favoriteStationIds = [];
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) { //on vérifie si l'utilisateur est connecté
            $userId = $this->getUser()->getId();
            $favoriteStations = $this->stationUserRepository->findStationsByUserId($userId); //on récupère les stations favorites de l'utilisateur
            $favoriteStationIds = array_column($favoriteStations, 'idStation');
            //on récupère les identifiants des stations favorites de l'utilisateur, cela permettra de vérifier si l'user a une station en favori et donc d'afficher sur la carte "ajouter en favori" ou "retirer des favoris" selon le cas
        }

        if (isset($_SESSION["flash"])) { //on affiche le message flash s'il existe
            $message = $_SESSION["flash"];
            unset($_SESSION["flash"]);
            echo $message;
        }

        return $this->render('home/index.html.twig', [
            'titre' => 'Carte OpenStreetMap',
            'stations' => $stations,
            'favoriteStationIds' => $favoriteStationIds
        ]);
    }
}