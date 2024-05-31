<?php

namespace App\Controller;

use App\Entity\Categoria;
use App\Entity\Categorias;
use App\Entity\Pokemon;
use App\Entity\Pokemons;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PokemonsCategoriasController extends AbstractController
{
    #[Route('/pokemons/categorias', name: 'app_pokemons_categorias')]
    public function index(): Response
    {
        return $this->render('pokemons_categorias/index.html.twig', [
            'controller_name' => 'PokemonsCategoriasController',
        ]);
    }


    #[Route('/pokemons/insertar-con-categorias/categoria/nombreP/altura/peso/sexo', name: 'insertar_con_categorias')]
    public function insertarConCategorias(
       $categoria,
       $nombreP,
       $altura,
       $peso,
       $sexo,
       EntityManagerInterface $em 
    ): Response
    {
        // Crear y persistir la categoría
        $categoriaEntity = new Categorias;
        $categoriaEntity->setCategoria($categoria);
        $em->persist($categoriaEntity);

        // Crear y persistir el Pokémon
        $pokemon = new Pokemons();
        $pokemon->setNombre($nombreP);
        $pokemon->setAltura($altura);
        $pokemon->setPeso($peso);
        $pokemon->isSexo($sexo);
        $pokemon->setIdCategoria($categoriaEntity);
        $em->persist($pokemon);

        // Guardar cambios en la base de datos
        $em->flush();

        // Obtener listas actualizadas
        $pokemons = $em->getRepository(Pokemons::class)->findAll();
        $categorias = $em->getRepository(Categorias::class)->findAll();
        // Renderizar la vista con Twig
        return $this->render('pokemons_categorias/list.html.twig', [
            'pokemons' => $pokemons,
            'categorias' => $categorias,
        ]);
        
    }
}
