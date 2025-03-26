<?php

namespace App\Entity;

use App\Repository\EquivalentRepository;
use Doctrine\ORM\Mapping as ORM;

/*!
 * Cette classe est l'entité "Équivalent", elle permet de stocker tous les équivalents CO2 afin de pouvoir
 * les calculer et les afficher dans les différents dashboards ou sur le plugin.
 */
#[ORM\Entity(repositoryClass: EquivalentRepository::class)]
class Equivalent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; ///< L'id de l'équivalent

    #[ORM\Column(length: 255)]
    private ?string $name = null; ///< Le nom de l'équivalent

    #[ORM\Column]
    private ?float $equivalent = null; ///< La valeur de l'équivalent sur une base de 100kg de CO2

    #[ORM\Column(length: 255)]
    private ?string $iconThumbnail = null; ///< Le nom du fichier de l'icone de l'équivalent

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
