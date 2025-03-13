<?php

namespace App\Entity;

use App\Repository\AdviceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdviceRepository::class)]
class Advice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(type: 'boolean')]
    private bool $isDev = false;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $advice = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $icon = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsDev(): bool
    {
        return $this->isDev;
    }

    public function setIsDev(bool $isDev): self
    {
        $this->isDev = $isDev;

        return $this;
    }

    public function getAdvice(): ?string
    {
        return $this->advice;
    }

    public function setAdvice(string $advice): static
    {
        $this->advice = $advice;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }
}
