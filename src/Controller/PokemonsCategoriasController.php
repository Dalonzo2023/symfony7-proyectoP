<?php
namespace App\Controller;

use App\Entity\Categorias;
use App\Entity\Pokemons;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PokemonsCategoriasController extends AbstractController
{
    #[Route('/pokemons/insertar-con-categorias/{categoria}/{nombrePokemon}/{altura}/{peso}/{sexo}', 
    name: 'app_pokemons_categorias_')]
    public function insertar(
        string $categoria,
        string $nombrePokemon,
        int $altura,
        float $peso,
        bool $sexo,
        EntityManagerInterface $entityManager
    ): Response {
        // Crear una nueva categoría y persistirla
        $categorias = new Categorias();
        $categorias->setCategoria($categoria);
        $entityManager->persist($categorias);

        // Crear un nuevo Pokémon asociado a la categoría y persistirlo
        $nuevoPokemon = new Pokemons();
        $nuevoPokemon->setNombre($nombrePokemon);
        $nuevoPokemon->setAltura($altura);
        $nuevoPokemon->setPeso($peso);
        $nuevoPokemon->setSexo($sexo);
        $nuevoPokemon->setIdCategoria($categorias);
        $entityManager->persist($nuevoPokemon);

        // Guardar los cambios en la base de datos
        $entityManager->flush();

        
        //return $this->redirectToRoute("app_pokemons_verpokemons") ;
        // Renderizar
        return $this->render('pokemons_categorias/index.html.twig', [
            'pokemons' => [$nuevoPokemon],
            'categorias' => [$categorias],
        ]);
    }
}
