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

        $jsonContent = $this->serializer->serialize($pizzas, 'json', ['groups' => 'list_pizza']);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
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


        $jsonContent = $this->serializer->serialize($pizza, 'json', ['groups' => 'show_pizza']);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($jsonContent);
        return $response;
    }


}
