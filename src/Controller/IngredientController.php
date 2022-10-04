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

class IngredientController extends AbstractController
{
    private ManagerRegistry $doctrine;
    private SerializerInterface $serializer;

    public function __construct(ManagerRegistry $doctrine, SerializerInterface $serializer)
    {
        $this->doctrine = $doctrine;
        $this->serializer = $serializer;
    }


    #[Route('/ingredients')]
    public function list(): Response
    {
        $ingredients = $this->doctrine->getRepository(Ingredient::class)
            ->findAll();

        $jsonContent = $this->serializer->serialize($ingredients, 'json', ['groups' => 'list_ingredient']);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($jsonContent);
        return $response;
    }

    #[Route('/ingredients/{idIngredient}')]
    public function show(int $idIngredient): Response
    {
        $ingredient = $this->doctrine->getRepository(Ingredient::class)
            ->find($idIngredient);

        if(!$ingredient)
            throw $this->createNotFoundException('This ingredient does not exist');


        $jsonContent = $this->serializer->serialize($ingredient, 'json', ['groups' => 'show_ingredient']);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($jsonContent);
        return $response;
    }


}
