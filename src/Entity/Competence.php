<?php

namespace App\Entity;

use App\Repository\CompetenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompetenceRepository::class)]
class Competence
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

    #[ORM\OneToMany(mappedBy: 'competence', targetEntity: EtudiantCompetence::class)]
    private $etudiantCompetences;

    public function __construct()
    {
        $this->etudiantCompetences = new ArrayCollection();
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
     * @return Collection<int, EtudiantCompetence>
     */
    public function getEtudiantCompetences(): Collection
    {
        return $this->etudiantCompetences;
    }

    public function addEtudiantCompetence(EtudiantCompetence $etudiantCompetence): self
    {
        if (!$this->etudiantCompetences->contains($etudiantCompetence)) {
            $this->etudiantCompetences[] = $etudiantCompetence;
            $etudiantCompetence->setCompetence($this);
        }

        return $this;
    }

    public function removeEtudiantCompetence(EtudiantCompetence $etudiantCompetence): self
    {
        if ($this->etudiantCompetences->removeElement($etudiantCompetence)) {
            // set the owning side to null (unless already changed)
            if ($etudiantCompetence->getCompetence() === $this) {
                $etudiantCompetence->setCompetence(null);
            }
        }

        return $this;
    }
}
