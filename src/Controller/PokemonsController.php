<?php

namespace App\Controller;

use App\Entity\Categorias;
use App\Entity\Pokemons;
use App\Repository\PokemonsRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/pokemons', name: 'app_pokemons_')]
class PokemonsController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(): Response
    {
        return $this->render('pokemons/index.html.twig', [
            'controller_name' => 'PokemonsController',
        ]);
    }
      //04 insercion 1 registro con parametros y ForingKey
    #[Route('/insertar/{cat}/{nombre}/{altura}/{peso}/{sexo}', name: 'insertarParams')]
    public function insertarParams(int $cat, string $nombre, int $altura, float $peso, bool $sexo, ManagerRegistry $doctrine): Response
    {
        $gestorEntidades = $doctrine->getManager();
        $pokemon = new Pokemons();
        $pokemon->setNombre($nombre);
        $pokemon->setAltura($altura);
        $pokemon->setPeso($peso);
        $pokemon->setSexo($sexo);
        //introducir la clave foranea, se introduce el objeto entero, me creo un objeto vacio
        $categoria = new Categorias();
        
        //obtengo el reposytorio de Categorias
        $repoCategorias = $gestorEntidades->getRepository(Categorias::class);
        //saco el objeto completo
        $categoria = $repoCategorias->find($cat);
        //insertar la categoria
        $pokemon->setIdCategoria($categoria);
        
        $gestorEntidades->persist($pokemon);
        $gestorEntidades->flush();

        return new Response("Pokemon insertado con ID: " 
            . $pokemon->getId());
    }
    
    //5 Consulta completa (findAll)
    #[Route('/verPokemons', name: 'verpokemons')]
    public function verPokemons(EntityManagerInterface $gestorEntidades): Response
    {
        $repoPokemons= $gestorEntidades->getRepository(Pokemons::class);
        $pokemons = $repoPokemons->findAll();

        return $this->render('pokemons/index.html.twig', [
            'controller_name' => 'PokemonsController',
            'Bichos'=> $pokemons,
        ]);
    }

     //6.0 consulta completa, salida array JSON
    #[Route('/verPokemonsJSON', name: 'verpokemonsjson')]
    public function verPokemonsJSON(PokemonsRepository $repoPokemons): Response
    {
        $pokemons = $repoPokemons->findAll();
        $datos = [];
        foreach ($pokemons as $pokemon) {
            $datos[] = [
                "idPokemon" => $pokemon->getId(),
                "Nombre" => $pokemon->getNombre(),
                "Altura" => $pokemon->getAltura(),
                "Peso" => $pokemon->getPeso(),
                "Sexo" => $pokemon->isSexo(),
                "Categoria" => $pokemon->getIdCategoria()->getCategoria(),
            ];
        }


        return new JsonResponse($datos);
    }
    
    //07 consulta por parametro, salida ARRAY JSON
    #[Route('/verPokemonsJSON/{sexo}', name: 'verpokemonsjsonparam')]
    public function verPokemonsJSONParam(PokemonsRepository $repoPokemons, bool $sexo): Response
    {
       
        $pokemons = $repoPokemons->findBy(["sexo" =>$sexo]);
        $datos = [];
        foreach ($pokemons as $pokemon) {
            $datos[] = [
                "idPokemon" => $pokemon->getId(),
                "Nombre" => $pokemon->getNombre(),
                "Altura" => $pokemon->getAltura(),
                "Peso" => $pokemon->getPeso(),
                "Sexo" => $pokemon->isSexo(),
                "Categoria" => $pokemon->getIdCategoria()->getCategoria(),
            ];
        }

        return new JsonResponse($datos);
        //return $this->json($datos);
    }

    //07 consulta por parametro y ordenacion, salida ARRAY JSON
    #[Route('/verPokemonsOrdenadosJSON/{sexo}', name: 'verpokemonsordenadosjsonparam')]
    public function verPokemonsOrdenadosJSONParam(PokemonsRepository $repoPokemons, bool $sexo): Response
    {
        //consutar pokemons por sexo
        //y ordenar por altura de mayor a menor
        $pokemons = $repoPokemons->findBy(["sexo" =>$sexo],["altura"=> "DESC"]);
        $datos = [];
        foreach ($pokemons as $pokemon) {
            $datos[] = [
                "idPokemon" => $pokemon->getId(),
                "Nombre" => $pokemon->getNombre(),
                "Altura" => $pokemon->getAltura(),
                "Peso" => $pokemon->getPeso(),
                "Sexo" => $pokemon->isSexo(),
                "Categoria" => $pokemon->getIdCategoria()->getCategoria(),
            ];
        }

        return new JsonResponse($datos);
        //return $this->json($datos);
    }

    //09. Actualizar con parámetros
    #[Route('/actualizar/{id}/{altura}/{peso}', name: 'actualizarparams')]
    public function actualizarParams(ManagerRegistry $doctrine, int $id, int $altura, float $peso): Response
    {
        $gestorEntidades= $doctrine->getManager();
        //Sacamos el pokemon que vamos a actualizar
        $repoPokemons= $gestorEntidades->getRepository(Pokemons::class);
        $pokemon= $repoPokemons->find($id);
        
        if(!$pokemon){
          throw $this->createNotFoundException("Pokemon NO encontrado");
          
        }
        $pokemon->setAltura($altura);
        $pokemon->setPeso($peso);
         //actualizar
        $gestorEntidades->flush();

       //vamos a hacer una redireccion
       return $this->redirectToRoute('app_pokemons_verpokemons');
    }

     //10. eliminación con parámetro
    #[Route('/eliminar/{id}', name: 'eliminar')]
    public function eliminar(EntityManagerInterface $gestorEntidades, int $id): Response
    {
        $repoPokemons= $gestorEntidades->getRepository(Pokemons::class);
        $pokemon= $repoPokemons->find($id);

        //Borro y actualizo
        $gestorEntidades->remove($pokemon);
        $gestorEntidades->flush();

        return new Response("Pokemon eliminado con ID: " . $id);
    }

     //11. Formularios
     //Dos inyecciones la solicitud(request) y el doctrine
    #[Route('/formulario', name: 'formulario')]
    public function formulario(Request $request, ManagerRegistry $doctrine): Response
    {
        
        //a) Creamos el objeto a guardar vacio
        $pokemon= new Pokemons();
        //b) Creamos el objeto formulario
        $formulario = $this->createFormBuilder($pokemon)
             ->add("nombre", TextType::class, 
             ["attr"=> ["class"=> "form-control"]])
             ->add("altura", IntegerType::class, 
             ["attr"=> ["class"=> "form-control"]])
             ->add("peso", NumberType::class, 
             ["attr"=> ["class"=> "form-control", 
             'step' => '0.01'], 
             'html5' => true])
             ->add("sexo", ChoiceType::class, 
             [  "choices" => [
                "Macho" => false,
                "Hembra" => true,], 
                "attr"=> ["class"=> "form-control"]])
                //Añadimos el campo del FK
                ->add("idCategoria", EntityType::class, 
                [
                    "class" => Categorias::class,  //entidad
                    //choice label -> nombre de la tabla fk en minuscula
                    "choice_label" => "categoria", 
                    "placeholder" => "Selecciona Categoria",
                    "attr"=> ["class"=> "form-select"]
                ])

             ->add("guardar", SubmitType::class, 
             ["attr" => ["class" => "btn btn-danger"],
                "label" => "Insertar Pokemon"])
             ->getForm();

        
        //c)Tratar el formulario
        //Recoger datos del formulario e insertar en la BBDD
        $formulario->handleRequest($request);
        
        if ($formulario->isSubmitted() && $formulario->isValid()) {
            
            $gestorEntidades= $doctrine->getManager();
            $gestorEntidades->persist($pokemon);
            $gestorEntidades->flush();

            //redireccionamos
            return $this->redirectToRoute("app_pokemons_verpokemons");
        };
        
        //d) Pintar el formulario
        return $this->render('pokemons/formulario.html.twig', [
            'controller_name' => 'PokemonsController',
            "formulario"=> $formulario,

        ]);
    }

}
