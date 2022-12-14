<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Mapping\ClassMetadata;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['show_ingredient', 'list_ingredient', 'show_pizza', 'list_pizza'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['show_ingredient', 'list_ingredient', 'show_pizza', 'list_pizza'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['show_ingredient', 'list_ingredient', 'show_pizza', 'list_pizza'])]
    private ?float $price = null;

    #[ORM\ManyToMany(targetEntity: Pizza::class, mappedBy: 'ingredients')]
    #[Groups(['show_ingredient'])]
    private Collection $pizzas;

    public function __construct()
    {
        $this->pizzas = new ArrayCollection();
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('name', new Assert\NotBlank());
        $metadata->addPropertyConstraint('name', new Assert\Length(['min' => 3, 'max' => 255]));
        $metadata->addConstraint(new UniqueEntity(['fields' => 'name']));

        $metadata->addPropertyConstraint('price', new Assert\NotBlank());
        $metadata->addPropertyConstraint('price', new Assert\Positive());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, Pizza>
     */
    public function getPizzas(): Collection
    {
        return $this->pizzas;
    }

    public function addPizza(Pizza $pizza): self
    {
        if (!$this->pizzas->contains($pizza)) {
            $this->pizzas->add($pizza);
            $pizza->addIngredient($this);
        }

        return $this;
    }

    public function removePizza(Pizza $pizza): self
    {
        if ($this->pizzas->removeElement($pizza)) {
            $pizza->removeIngredient($this);
        }

        return $this;
    }
}
