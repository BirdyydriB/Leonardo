<?php

namespace App\Entity;

use App\Repository\PizzaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PizzaRepository::class)]
class Pizza
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['show_pizza', 'list_pizza'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['show_pizza', 'list_pizza'])]
    private ?string $name = null;

    #[Groups(['show_pizza', 'list_pizza'])]
    private ?float $price = null;

    #[ORM\ManyToMany(targetEntity: Ingredient::class, inversedBy: 'pizzas', fetch:'EAGER')]
    #[Groups(['show_pizza', 'list_pizza'])]
    private Collection $ingredients;

    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
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
        if(!$this->price)
            $this->price = $this->calculatePrice();

        return $this->price;
    }

    private function calculatePrice(): float
    {
        $totalPrice = 0;
        foreach ($this->ingredients as $ingredient) {
            $totalPrice += $ingredient->getPrice();
        }

        return round($totalPrice * 1.5, 2);
    }

    /**
     * @return Collection<int, Ingredient>
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(Ingredient $ingredient): self
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients->add($ingredient);
        }
        $this->price = $this->calculatePrice();

        return $this;
    }

    public function removeIngredient(Ingredient $ingredient): self
    {
        $this->ingredients->removeElement($ingredient);
        $this->price = $this->calculatePrice();

        return $this;
    }
}
