<?php

namespace App\Entity;

use App\Repository\PanierGoatRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "im22_PanierGoat")]
#[ORM\Entity(repositoryClass: PanierGoatRepository::class)]
class PanierGoat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Panier::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $panier;

    #[ORM\ManyToOne(targetEntity: Goat::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $goat;

    #[ORM\Column(type: 'integer')]
    private $quantite;

    public function __construct($Panier,$Goat,$quantite)
    {
        $this->panier=$Panier;
        $this->goat=$Goat;
        $this->quantite=$quantite;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(?Panier $panier): self
    {
        $this->panier = $panier;

        return $this;
    }

    public function getGoat(): ?Goat
    {
        return $this->goat;
    }

    public function setGoat(?Goat $goat): self
    {
        $this->goat = $goat;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function addQuantite(int $quantity): self
{
    $this->quantite +=$quantity;

    return $this;
}
}
