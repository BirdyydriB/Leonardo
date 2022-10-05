<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
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


    #[Route('/ingredients', methods:'GET')]
    public function list(): Response
    {
        $ingredients = $this->doctrine->getRepository(Ingredient::class)
            ->findAll();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = $this->serializer->serialize($ingredients, 'json', ['groups' => 'list_ingredient']);
        $response->setContent($jsonContent);
        return $response;
    }

    #[Route('/ingredients/{idIngredient}', methods:'GET')]
    public function show(int $idIngredient): Response
    {
        $ingredient = $this->doctrine->getRepository(Ingredient::class)
            ->find($idIngredient);
        if(!$ingredient)
            throw $this->createNotFoundException('This ingredient does not exist');

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = $this->serializer->serialize($ingredient, 'json', ['groups' => 'show_ingredient']);
        $response->setContent($jsonContent);
        return $response;
    }

    #[Route('/ingredients', methods:'POST')]
    public function create(ValidatorInterface $validator, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $newIngredient = new Ingredient();
        $newIngredient->setName($data['name'])
            ->setPrice($data['price']);

        $errors = $validator->validate($newIngredient);
        if (count($errors) > 0) {
            return new Response($errors);
        }

        $em = $this->doctrine->getManager();
        $em->persist($newIngredient);
        $em->flush();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = $this->serializer->serialize($newIngredient, 'json', ['groups' => 'show_ingredient']);
        $response->setContent($jsonContent);
        return $response;
    }

    #[Route('/ingredients/{idIngredient}', methods:'PUT')]
    public function edit(ValidatorInterface $validator, Request $request, int $idIngredient): Response
    {
        $ingredient = $this->doctrine->getRepository(Ingredient::class)
            ->find($idIngredient);
        if(!$ingredient)
            throw $this->createNotFoundException('This ingredient does not exist');

        $data = json_decode($request->getContent(), true);
        $ingredient->setPrice($data['price']);

        $errors = $validator->validate($ingredient);
        if (count($errors) > 0) {
            return new Response($errors);
        }

        $this->doctrine->getManager()->flush();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $jsonContent = $this->serializer->serialize($ingredient, 'json', ['groups' => 'show_ingredient']);
        $response->setContent($jsonContent);
        return $response;
    }


}
