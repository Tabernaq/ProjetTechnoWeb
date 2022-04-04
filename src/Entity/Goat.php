<?php

namespace App\Entity;

use App\Repository\GoatRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: "im22_Goat")]
#[ORM\Entity(repositoryClass: GoatRepository::class)]
class Goat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $Race;

    #[ORM\Column(type: 'float')]
    private $Price;

    #[ORM\Column(type: 'integer')]
    private $Stock;

    /**
     * Goat constructor
     */
    public function __construct()
    {
        $this->Stock=0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRace(): ?string
    {
        return $this->Race;
    }

    public function setRace(string $Race): self
    {
        $this->Race = $Race;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->Price;
    }

    public function setPrice(float $Price): self
    {
        $this->Price = $Price;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->Stock;
    }

    public function setStock(int $Stock): self
    {
        $this->Stock = $Stock;

        return $this;
    }
}
