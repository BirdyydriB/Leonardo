<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Ingredient;
use App\Entity\Pizza;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $ingredientsNames = ['tomates', 'ognions', 'Cheddar', 'champignons', 'jambon', 'pommes de terre', 'lardons', 'Parmesan', 'merguez',
            'Reblochon', 'saumon', 'Chorizo', 'poivrons', 'Roquefort', 'Chèvre', 'olives', 'Pepperoni', 'anchois', 'ananas'];

        $ingredients = [];
        foreach($ingredientsNames as $ingredientsName) {
            $ingredient = new Ingredient();
            $ingredient->setName($ingredientsName)
              ->setPrice(rand(1, 20) / 10);
            $manager->persist($ingredient);

            $ingredients[$ingredientsName] = $ingredient;
        }

        $pizzasNames = ['Burger', 'Montagnarde', '3 fromages', 'Calzone', 'Marguarita', 'Reine', 'Indienne', 'Méditerranééenne', 'Paysanne', 'Norvégienne'];
        foreach($pizzasNames as $pizzaName) {
            $pizza = new Pizza();
            $pizza->setName($pizzaName);

            $randomIngredients = array_rand(array_flip($ingredientsNames), rand(2, 7));
            foreach($randomIngredients as $randomIngredient) {
                $pizza->addIngredient($ingredients[$randomIngredient]);
            }
            $manager->persist($pizza);
        };

        $manager->flush();
    }
}
