<?php

namespace App\Entity;

use App\Repository\EtudiantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: EtudiantRepository::class)]
class Etudiant implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $firstName;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $num;

    #[ORM\Column(type: 'string', length: 255)]
    private $email;

    #[ORM\Column(type: 'string', length: 255)]
    private $password;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $birthDate;

    #[ORM\Column(type: 'datetime')]
    private $createAt;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $avatar;

    #[ORM\Column(type: 'boolean')]
    private $activate = 0;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $address;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $document;

    #[ORM\ManyToOne(targetEntity: Etude::class, inversedBy: 'etudiants')]
    #[ORM\JoinColumn(nullable: true)]
    private $etude;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $resetToken;

    #[ORM\OneToMany(mappedBy: 'etudiant', targetEntity: EtudiantCompetence::class)]
    private $etudiantCompetences;

    #[ORM\OneToMany(mappedBy: 'etudiant', targetEntity: EtudiantPersonnalite::class)]
    private $etudiantPersonnalites;

    public function __construct()
    {
        $this->etudiantCompetences = new ArrayCollection();
        $this->etudiantPersonnalites = new ArrayCollection();
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getNum(): ?string
    {
        return $this->num;
    }

    public function setNum(string $num): self
    {
        $this->num = $num;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return Etudiant
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getBirthDate(): ?\DateTime
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTime $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getCreateAt(): ?\DateTime
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTime $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getActivate(): ?bool
    {
        return $this->activate;
    }

    public function setActivate(bool $activate): self
    {
        $this->activate = $activate;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getDocument(): ?string
    {
        return $this->document;
    }

    public function setDocument(?string $document): self
    {
        $this->document = $document;

        return $this;
    }

    public function getEtude(): ?Etude
    {
        return $this->etude;
    }

    public function setEtude(?Etude $etude): self
    {
        $this->etude = $etude;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResetToken(): string
    {
        return $this->resetToken;
    }

    /**
     * @param mixed $resetToken
     * @return Etudiant
     */
    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

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
            $etudiantCompetence->setEtudiant($this);
        }

        return $this;
    }

    public function removeEtudiantCompetence(EtudiantCompetence $etudiantCompetence): self
    {
        if ($this->etudiantCompetences->removeElement($etudiantCompetence)) {
            // set the owning side to null (unless already changed)
            if ($etudiantCompetence->getEtudiant() === $this) {
                $etudiantCompetence->setEtudiant(null);
            }
        }

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
            $etudiantPersonnalite->setEtudiant($this);
        }

        return $this;
    }

    public function removeEtudiantPersonnalite(EtudiantPersonnalite $etudiantPersonnalite): self
    {
        if ($this->etudiantPersonnalites->removeElement($etudiantPersonnalite)) {
            // set the owning side to null (unless already changed)
            if ($etudiantPersonnalite->getEtudiant() === $this) {
                $etudiantPersonnalite->setEtudiant(null);
            }
        }

        return $this;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }
}
