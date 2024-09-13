<?php

namespace App\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HelloWorldController extends AbstractController
{

//    les annotations commencent par @ et attribut par un #
    #[Route('/hello')]
    public function hello(): Response
    {

        return $this->render("admin/hello.html.twig",
        [
            'titre' => "ma page"
        ]);
    }
    #[Route('/world')]
    public function world(): Response
    {

        return new Response(
            '<html><body><h1>Page world</h1></body></html>'
        );
    }

}