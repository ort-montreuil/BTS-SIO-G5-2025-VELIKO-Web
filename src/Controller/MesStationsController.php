<?php

namespace App\Controller;

use App\Controller\StationFavorisController;
use App\Entity\Station;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MesStationsController extends AbstractController
{
    private HttpClientInterface $client;
    private EntityManagerInterface $entityManager;

    public function __construct(HttpClientInterface $client, EntityManagerInterface $entityManager)
    {
        $this->client = $client;
        $this->entityManager = $entityManager;
    }

    #[Route('/mes/stations', name: 'app_mes_stations')]
    public function index(): Response
    {
        $response = $this->client->request('GET', 'http://localhost:9042/api/stations');
        $stations = $response->toArray();

        return $this->render('mes_stations/index.html.twig', [
            'controller_name' => 'MesStationsController',
            'stations' => $stations,
        ]);
    }
}
