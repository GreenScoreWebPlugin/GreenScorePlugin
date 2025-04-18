<?php

namespace App\Entity;

use App\Repository\OrganisationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use RuntimeException;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Unique;

/*!
 * Cette classe est l'entité "Organisatioj", elle permet de stocker toutes les organisations.
 */
#[ORM\Entity(repositoryClass: OrganisationRepository::class)]
#[UniqueEntity(fields: ['siret'], message: 'Ce numéro SIRET est déjà utilisé.')]
class Organisation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; ///< Id de l'organisation

    #[ORM\Column(length: 255)]
    private ?string $organisationName = null; ///< Nom de l'organisation

    #[ORM\Column(length: 20)]
    private ?string $organisationCode = null; ///< Code de l'organisation

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null; ///< Ville de l'organisation

    #[ORM\Column(length: 14,nullable: true)]
    private ?string $siret = null; ///< Numéro SIRET de l'organisation

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'organisation')]
    private Collection $users; ///< Les utilisateurs de l'organisation

    #[ORM\OneToOne(mappedBy: 'isAdminOf', cascade: ['persist', 'remove'])]
    private ?User $organisationAdmin = null; ///< L'administrateur de l'organisation

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrganisationName(): ?string
    {
        return $this->organisationName;
    }

    public function setOrganisationName(string $organisationName): static
    {
        $this->organisationName = $organisationName;

        return $this;
    }

    public function getOrganisationCode(): ?string
    {
        return $this->organisationCode;
    }

    public function setOrganisationCode(string $organisationCode): static
    {
        $this->organisationCode = $organisationCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(string $siret): static
    {
        $this->siret = $siret;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setOrganisation($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->contains($user)) {
            if ($this->users->removeElement($user)) {
                if ($user->getOrganisation() === $this) {
                    $user->setOrganisation(null);
                }
            } else {
                throw new RuntimeException("Impossible de supprimer l'utilisateur de l'organisation.");
            }
        } else {
            throw new RuntimeException("L'utilisateur ne fait pas partie de cette organisation.");
        }

        return $this;
    }


    public function getOrganisationAdmin(): ?User
    {
        return $this->organisationAdmin;
    }

    public function setOrganisationAdmin(?User $organisationAdmin): static
    {
        // unset the owning side of the relation if necessary
        if ($organisationAdmin === null && $this->organisationAdmin !== null) {
            $this->organisationAdmin->setIsAdminOf(null);
        }

        // set the owning side of the relation if necessary
        if ($organisationAdmin !== null && $organisationAdmin->getIsAdminOf() !== $this) {
            $organisationAdmin->setIsAdminOf($this);
        }

        $this->organisationAdmin = $organisationAdmin;

        return $this;
    }

    public function generateOrganisationCode(): String
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        $length = 8;

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $code;
    }
}
