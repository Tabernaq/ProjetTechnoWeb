<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\ManagerRegistry;

#[ORM\Table(name: "im22_Panier")]
#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'float')]
    private $totalPrice;

    #[ORM\OneToOne(inversedBy: 'panier', targetEntity: UserV2::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $client;

    public function __construct()
    {
        $this->totalPrice=0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $total_price): self
    {
        $this->totalPrice = $total_price;

        return $this;
    }

    public function updateTotalPrice(ManagerRegistry $doctrine):self
    {
        $em=$doctrine->getManager();
        $collecPaniers = $em->getRepository("App:PanierGoat")->findBy(array('panier' => $this->getId()));
        $res = 0;
        foreach ($collecPaniers as $pg){
            $res+=$pg->getQuantite()*$pg->getGoat()->getPrice();
        }
        $this->setTotalPrice($res);
        $em->flush();
        return $this;
    }

    public function getClient(): ?UserV2
    {
        return $this->client;
    }

    public function setClient(UserV2 $client): self
    {
        $this->client = $client;

        return $this;
    }
}
