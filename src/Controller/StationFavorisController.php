<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class StationFavorisController extends AbstractController
{
    private HttpClientInterface $client;

    // Injection du client HTTP via le constructeur
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route('/station/favoris', name: 'app_station_favoris')]
    public function index(Request $request): Response
    {
        // Faire une requête GET à l'API pour récupérer toutes les stations
        $response = $this->client->request('GET', 'http://localhost:9042/api/stations');
        $stations = $response->toArray();

        // Récupérer la requête de recherche si elle existe
        $query = $request->query->get('query');
        if ($query) {
            // Filtrer les stations en fonction de la requête
            $stations = array_filter($stations, function ($station) use ($query) {
                return stripos($station['name'], $query) !== false; // Ignorer la casse
            });
        }

        return $this->render('station_favoris/index.html.twig', [
            'controller_name' => 'StationFavorisController',
            'stations' => $stations,
        ]);
    }
}