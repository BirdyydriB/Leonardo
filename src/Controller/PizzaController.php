<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Ingredient;
use App\Entity\Pizza;

class PizzaController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private SerializerInterface $serializer;

    public function __construct(ManagerRegistry $doctrine, SerializerInterface $serializer)
    {
        $this->doctrine = $doctrine;
        $this->serializer = $serializer;
    }


    #[Route('/pizzas')]
    public function list(): Response
    {
        $pizzas = $this->doctrine->getRepository(Pizza::class)
            ->findAll();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = $this->serializer->serialize($pizzas, 'json', ['groups' => 'list_pizza']);
        $response->setContent($jsonContent);
        return $response;
    }

    #[Route('/pizzas/{idPizza}')]
    public function show(int $idPizza): Response
    {
        $pizza = $this->doctrine->getRepository(Pizza::class)
            ->find($idPizza);
        if(!$pizza)
            throw $this->createNotFoundException('This pizza does not exist');

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = $this->serializer->serialize($pizza, 'json', ['groups' => 'show_pizza']);
        $response->setContent($jsonContent);
        return $response;
    }

    #[Route('/pizzas', methods:'POST')]
    public function create(ValidatorInterface $validator, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $newPizza = new Pizza();
        $newPizza->setName($data['name']);

        $errors = $validator->validate($newPizza);
        if (count($errors) > 0) {
            return new Response($errors);
        }

        $em = $this->doctrine->getManager();
        $em->persist($newPizza);
        $em->flush();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = $this->serializer->serialize($newPizza, 'json', ['groups' => 'show_pizza']);
        $response->setContent($jsonContent);
        return $response;
    }

    #[Route('/pizzas/{idPizza}', methods:'PUT')]
    public function edit(ValidatorInterface $validator, Request $request, int $idPizza): Response
    {
        $pizza = $this->doctrine->getRepository(Pizza::class)
            ->find($idPizza);
        if(!$pizza)
            throw $this->createNotFoundException('This pizza does not exist');

        $data = json_decode($request->getContent(), true);
        $pizza->setName($data['name']);

        $errors = $validator->validate($pizza);
        if (count($errors) > 0) {
            return new Response($errors);
        }

        $this->doctrine->getManager()->flush();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = $this->serializer->serialize($pizza, 'json', ['groups' => 'show_pizza']);
        $response->setContent($jsonContent);
        return $response;
    }

    #[Route('/pizzas/{idPizza}/ingredients/{idIngredient}', methods:'POST')]
    public function addPizzaIngredient(int $idPizza, int $idIngredient): Response
    {
        $pizza = $this->doctrine->getRepository(Pizza::class)
            ->find($idPizza);
        if(!$pizza)
            throw $this->createNotFoundException('This pizza does not exist');

        $ingredient = $this->doctrine->getRepository(Ingredient::class)
            ->find($idIngredient);
        if(!$ingredient)
            throw $this->createNotFoundException('This ingredient does not exist');

        $pizza->addIngredient($ingredient);
        $this->doctrine->getManager()->flush();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = $this->serializer->serialize($pizza, 'json', ['groups' => 'show_pizza']);
        $response->setContent($jsonContent);
        return $response;
    }

    #[Route('/pizzas/{idPizza}/ingredients/{idIngredient}', methods:'DELETE')]
    public function removePizzaIngredient(int $idPizza, int $idIngredient): Response
    {
        $pizza = $this->doctrine->getRepository(Pizza::class)
            ->find($idPizza);
        if(!$pizza)
            throw $this->createNotFoundException('This pizza does not exist');

        $ingredient = $this->doctrine->getRepository(Ingredient::class)
            ->find($idIngredient);
        if(!$ingredient)
            throw $this->createNotFoundException('This ingredient does not exist');

        $pizza->removeIngredient($ingredient);
        $this->doctrine->getManager()->flush();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = $this->serializer->serialize($pizza, 'json', ['groups' => 'show_pizza']);
        $response->setContent($jsonContent);
        return $response;
    }



}
