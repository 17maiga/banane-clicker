<?php

namespace App\Entity;

use App\Repository\UpgradeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UpgradeRepository::class)]
class Upgrade
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'integer')]
    private $bananasPerSecond;

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

    public function getBananasPerSecond(): ?int
    {
        return $this->bananasPerSecond;
    }

    public function setBananasPerSecond(int $bananasPerSecond): self
    {
        $this->bananasPerSecond = $bananasPerSecond;

        return $this;
    }
}
