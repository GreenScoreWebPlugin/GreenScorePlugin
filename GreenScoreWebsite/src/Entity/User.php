<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'Un compte avec cette adresse e-mail existe déjà.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\Email(message: 'Veuillez entrer une adresse e-mail valide.')]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(nullable: true)]
    private ?float $totalCarbonFootprint = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Organisation $organisation = null;

    #[ORM\OneToOne(inversedBy: 'organisationAdmin', cascade: ['persist', 'remove'])]
    private ?Organisation $isAdminOf = null;

    /**
     * @var Collection<int, MonitoredWebsite>
     */
    #[ORM\OneToMany(targetEntity: MonitoredWebsite::class, mappedBy: 'user')]
    private Collection $monitoredWebsites;

    public function __construct()
    {
        $this->monitoredWebsites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
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

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    // TODO: vérifier si mobile est utilisé car pas d'attribut mobile trouvé

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): static
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getTotalCarbonFootprint(): ?float
    {
        return $this->totalCarbonFootprint;
    }

    public function setTotalCarbonFootprint(?float $totalCarbonFootprint): static
    {
        $this->totalCarbonFootprint = $totalCarbonFootprint;

        return $this;
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(?Organisation $organisation): self
    {
        $this->organisation = $organisation;

        return $this;
    }

    public function getIsAdminOf(): ?Organisation
    {
        return $this->isAdminOf;
    }

    public function setIsAdminOf(?Organisation $isAdminOf): static
    {
        $this->isAdminOf = $isAdminOf;

        return $this;
    }

    /**
     * @return Collection<int, MonitoredWebsite>
     */
    public function getMonitoredWebsites(): Collection
    {
        return $this->monitoredWebsites;
    }

    public function addMonitoredWebsite(MonitoredWebsite $monitoredWebsite): static
    {
        if (!$this->monitoredWebsites->contains($monitoredWebsite)) {
            $this->monitoredWebsites->add($monitoredWebsite);
            $monitoredWebsite->setUser($this);
        }

        return $this;
    }

    public function removeMonitoredWebsite(MonitoredWebsite $monitoredWebsite): static
    {
        if ($this->monitoredWebsites->removeElement($monitoredWebsite)) {
            // set the owning side to null (unless already changed)
            if ($monitoredWebsite->getUser() === $this) {
                $monitoredWebsite->setUser(null);
            }
        }

        return $this;
    }
}
