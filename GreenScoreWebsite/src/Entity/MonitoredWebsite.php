<?php

namespace App\Entity;

use App\Repository\MonitoredWebsiteRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MonitoredWebsiteRepository::class)]
class MonitoredWebsite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'monitoredWebsites')]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $urlDomain = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $urlFull = null;

    #[ORM\Column(nullable: true)]
    private ?int $queriesQuantity = null;

    #[ORM\Column(nullable: true)]
    private ?float $carbonFootprint = null;

    #[ORM\Column(nullable: true)]
    private ?float $dataTransferred = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $resources = null;

    #[ORM\Column(nullable: true)]
    private ?float $loadingTime = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $creationDate = null;

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

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(?\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }
}
