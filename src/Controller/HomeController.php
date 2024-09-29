<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function execute(Request $request): Response
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "http://localhost:9042/api/stations",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_SSL_VERIFYPEER => false
            //Nous récupérons les données du fichier jsono
        ]);
        $response = curl_exec($curl); //elles sont donc stockées dans response
        $err = curl_error($curl);
        curl_close($curl);

        $response = json_decode($response, true); //nous décodons les données afin qu'elles soient accessibles

        //Le même processsus pour le fichier json suivant
        $curl2 = curl_init();
        curl_setopt_array($curl2, [
            CURLOPT_URL => "http://localhost:9042/api/stations/status",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        $response2 = curl_exec($curl2);
        $err2 = curl_error($curl2);
        curl_close($curl2);

        $response2 = json_decode($response2, true);


        return $this->render('home/index.html.twig', [
            'titre' => 'Carte OpenStreetMap',
            'response' => $response,
            'response2' => $response2
        ]);
    }
}