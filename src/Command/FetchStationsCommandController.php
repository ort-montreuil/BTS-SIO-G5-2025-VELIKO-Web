<?php

namespace App\Command;

use App\Entity\Station;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;

class FetchStationsCommandController extends Command
{
    protected static $defaultName = 'app:fetch-stations';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Création d'un client HTTP
        $client = HttpClient::create();

        // Récupération des données depuis l'API
        $response = $client->request('GET', $_ENV['API_VELIKO_URL'] . "/api/stations");
        $data = $response->toArray();

        // Affichage des données récupérées (débogage)
        $output->writeln(print_r($data, true)); // Pour voir la structure des données

        foreach ($data as $item) {
            $station = new Station();

            // Utilisez les noms des clés comme ils sont définis dans l'API
            if (isset($item['station_id'])) {
                $station->setStationId($item['station_id']);
            }

            // Utilisez stationCode de l'API et mappez-le sur station_code de la base de données
            if (isset($item['stationCode']) && $item['stationCode'] !== null) {
                $station->setStationCode($item['stationCode']); // stationCode de l'API
            } else {
                $output->writeln('Warning: station_code is missing for station_id: ' . ($item['station_id'] ?? 'unknown'));
                continue; // Ignorez cet item et passez au suivant
            }

            if (isset($item['name'])) {
                $station->setName($item['name']);
            }

            // Mettez à jour les clés pour lat et lon en utilisant les noms corrects
            if (isset($item['lat'])) {
                $station->setLat($item['lat']);
            }

            if (isset($item['lon'])) {
                $station->setLon($item['lon']);
            }

            if (isset($item['capacity'])) {
                $station->setCapacity($item['capacity']);
            }

            // Persistance de l'entité
            $this->entityManager->persist($station);
        }

        // Enregistrement dans la base de données
        $this->entityManager->flush();

        // Message de confirmation
        $output->writeln('Stations fetched and saved to the database.');

        return Command::SUCCESS;
    }
}
