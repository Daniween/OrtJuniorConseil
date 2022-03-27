<?php

namespace App\Entity;

use App\Repository\EtudiantPersonnaliteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtudiantPersonnaliteRepository::class)]
class EtudiantPersonnalite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Etudiant::class, inversedBy: 'etudiantPersonnalites')]
    #[ORM\JoinColumn(nullable: false)]
    private $etudiant;

    #[ORM\ManyToOne(targetEntity: Personnalite::class, inversedBy: 'etudiantPersonnalites')]
    #[ORM\JoinColumn(nullable: false)]
    private $personnalite;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtudiant(): ?Etudiant
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiant $etudiant): self
    {
        $this->etudiant = $etudiant;

        return $this;
    }

    public function getPersonnalite(): ?Personnalite
    {
        return $this->personnalite;
    }

    public function setPersonnalite(?Personnalite $personnalite): self
    {
        $this->personnalite = $personnalite;

        return $this;
    }
}
