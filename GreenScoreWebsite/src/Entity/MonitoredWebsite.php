<?php

namespace App\Entity;

use App\Repository\MonitoredWebsiteRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/*!
* Cette classe est l'entité "Site monitoré", elle permet de stocker toutes les pages sur lequel l'utilisateur est allé
* afin de pouvoir les monitorer et les afficher dans les différents dashboards ou sur le plugin.
*/
#[ORM\Entity(repositoryClass: MonitoredWebsiteRepository::class)]
class MonitoredWebsite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; ///< L'id du site monitoré

    #[ORM\ManyToOne(inversedBy: 'monitoredWebsites')]
    private ?User $user = null; ///< L'utilisateur qui monitore le site

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $urlDomain = null; ///< L'URL du domaine du site

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $urlFull = null; ///< L'URL complète du site

    #[ORM\Column(nullable: true)]
    private ?int $queriesQuantity = null; ///< Le nombre de requêtes effectuées sur la page

    #[ORM\Column(nullable: true)]
    private ?float $carbonFootprint = null; ///< L'empreinte carbone de la page

    #[ORM\Column(nullable: true)]
    private ?float $dataTransferred = null; ///< Le poids des données transférées sur la page

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $resources = null; ///< Les ressources nécessaires pour la page

    #[ORM\Column(nullable: true)]
    private ?float $loadingTime = null; ///< Temps de chargement de la page

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $country = null; ///< Ville dans laquelle se trouve l'utilisateur

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?DateTimeInterface $creationDate = null; ///< Date de création

    function __construct()
    {
        $this->setCreationDate(new DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getUrlDomain(): ?string
    {
        return $this->urlDomain;
    }

    public function setUrlDomain(?string $urlDomain): static
    {
        $this->urlDomain = $urlDomain;

        return $this;
    }

    public function getUrlFull(): ?string
    {
        return $this->urlFull;
    }

    public function setUrlFull(?string $urlFull): static
    {
        $this->urlFull = $urlFull;

        return $this;
    }

    public function getQueriesQuantity(): ?int
    {
        return $this->queriesQuantity;
    }

    public function setQueriesQuantity(?int $queriesQuantity): static
    {
        $this->queriesQuantity = $queriesQuantity;

        return $this;
    }

    public function getCarbonFootprint(): ?float
    {
        return $this->carbonFootprint;
    }

    public function setCarbonFootprint(?float $carbonFootprint): static
    {
        $this->carbonFootprint = $carbonFootprint;

        return $this;
    }

    public function getDataTransferred(): ?float
    {
        return $this->dataTransferred;
    }

    public function setDataTransferred(?float $dataTransferred): static
    {
        $this->dataTransferred = $dataTransferred;

        return $this;
    }

    public function getResources(): ?string
    {
        return $this->resources;
    }

    public function setResources(?string $resources): static
    {
        $this->resources = $resources;

        return $this;
    }

    public function getLoadingTime(): ?float
    {
        return $this->loadingTime;
    }

    public function setLoadingTime(?float $loadingTime): static
    {
        $this->loadingTime = $loadingTime;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getCreationDate(): ?DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(?DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }
}
