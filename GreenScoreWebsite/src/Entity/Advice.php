<?php

namespace App\Entity;

use App\Repository\AdviceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/*!
 * Cette classe est l'entité "Conseil", elle permet de stocker tous les conseils de GreenIT qu'ils soient pour les
 * développeurs ou non.
 */
#[ORM\Entity(repositoryClass: AdviceRepository::class)]
class Advice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; ///< L'id de l'entité
    
    #[ORM\Column(type: 'boolean')]
    private bool $isDev = false; ///< Indique si le conseil est pour les développeurs ou non

    #[ORM\Column(type: Types::TEXT)]
    private ?string $advice = null; ///< Le conseil lui-même

    #[ORM\Column(type: Types::TEXT)]
    private ?string $title = null; ///< Le titre du conseil

    #[ORM\Column(type: Types::TEXT)]
    private ?string $icon = null; ///< L'icone du conseil

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
