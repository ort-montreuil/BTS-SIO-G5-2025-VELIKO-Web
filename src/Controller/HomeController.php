<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
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

    #[Route('/home', name: 'app_home')]
    public function execute(Request $request): Response
    {
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
        $stations_informations = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $stations_informations = json_decode($stations_informations, true);

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
        $stations_statuts = curl_exec($curl2);
        $err2 = curl_error($curl2);
        curl_close($curl2);

        $stations_statuts = json_decode($stations_statuts, true);

        return $this->render('home/index.html.twig', [
            'titre' => 'Carte OpenStreetMap',
            'stations_informations' => $stations_informations,
            'stations_statuts' => $stations_statuts
        ]);
    }
}
