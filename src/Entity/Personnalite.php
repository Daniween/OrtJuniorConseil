<?php

namespace App\Entity;

use App\Repository\PersonnaliteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonnaliteRepository::class)]
class Personnalite
{
    public const TYPE_PUBLIC   = "public";
    public const TYPE_TRASH    = "trash";

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $libelle;

    #[ORM\Column(type: 'string', length: 255)]
    private $status = self::TYPE_PUBLIC;

    #[ORM\OneToMany(mappedBy: 'personnalite', targetEntity: EtudiantPersonnalite::class)]
    private $etudiantPersonnalites;

    public function __construct()
    {
        $this->etudiantPersonnalites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, EtudiantPersonnalite>
     */
    public function getEtudiantPersonnalites(): Collection
    {
        return $this->etudiantPersonnalites;
    }

    public function addEtudiantPersonnalite(EtudiantPersonnalite $etudiantPersonnalite): self
    {
        if (!$this->etudiantPersonnalites->contains($etudiantPersonnalite)) {
            $this->etudiantPersonnalites[] = $etudiantPersonnalite;
            $etudiantPersonnalite->setPersonnalite($this);
        }

        return $this;
    }

    public function removeEtudiantPersonnalite(EtudiantPersonnalite $etudiantPersonnalite): self
    {
        if ($this->etudiantPersonnalites->removeElement($etudiantPersonnalite)) {
            // set the owning side to null (unless already changed)
            if ($etudiantPersonnalite->getPersonnalite() === $this) {
                $etudiantPersonnalite->setPersonnalite(null);
            }
        }

        return $this;
    }
}
