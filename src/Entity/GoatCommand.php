<?php

namespace App\Entity;

use App\Repository\GoatCommandRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "im22_GoatCommand")]
#[ORM\Entity(repositoryClass: GoatCommandRepository::class)]
class GoatCommand
{
    //Cette classe ne serve que lors d'un formulaire pour commander un produit, la table liée est donc inutile puisqu'on n'y insère jamais rien mais pourrait servir si on met en place un histoire des transactions
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $quantite;

    #[ORM\Column(type: 'integer')]
    private $race;


    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getRace(): ?int
    {
        return $this->race;
    }

    public function setRace(int $race): self
    {
        $this->race = $race;

        return $this;
    }
}
