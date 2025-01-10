<?php

namespace App\Entity;

use App\Repository\EquivalentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquivalentRepository::class)]
class Equivalent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $equivalent = null;

    #[ORM\Column(length: 255)]
    private ?string $iconThumbnail = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEquivalent(): ?float
    {
        return $this->equivalent;
    }

    public function setEquivalent(float $equivalent): static
    {
        $this->equivalent = $equivalent;

        return $this;
    }

    public function getIconThumbnail(): ?string
    {
        return $this->iconThumbnail;
    }

    public function setIconThumbnail(string $iconThumbnail): static
    {
        $this->iconThumbnail = $iconThumbnail;

        return $this;
    }
}
