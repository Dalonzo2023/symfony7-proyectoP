<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

//Defino clase persona
class Persona{
    public string $nombre;
    public int $edad;
    public bool $sexo;

    public function __construct(string $nombre, int $edad, bool $sexo)
    {
        $this->nombre = $nombre;
        $this->edad = $edad;
        $this->sexo = $sexo;
    }
}

class TwingController extends AbstractController
{
    #[Route('/twing/{nombre}/{edad}/{sexo}', name: 'app_twing')]
    public function index(string $nombre, int $edad, bool $sexo): Response
    {
        //Ej: localhost:8000/twing/Daniel Alonzo/41/0
        //Creamos objeto
        $persona = new Persona($nombre,$edad, $sexo);

        return $this->render('twing/index.html.twig', [
            'controller_name' => 'TwingController',
            "TwigPersona" => $persona,
        ]);
    }
}
