<?php

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OfferRepository::class)]
class Offer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $downloadSpeed = null;

    #[ORM\Column]
    private ?int $uploadSpeed = null;

    #[ORM\Column]
    private ?bool $forNewUsers = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column]
    private ?int $percentageDiscount = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDownloadSpeed(): ?int
    {
        return $this->downloadSpeed;
    }

    /**
     * @param int|null $downloadSpeed
     */
    public function setDownloadSpeed(?int $downloadSpeed): void
    {
        $this->downloadSpeed = $downloadSpeed;
    }

    /**
     * @return int|null
     */
    public function getUploadSpeed(): ?int
    {
        return $this->uploadSpeed;
    }

    /**
     * @param int|null $uploadSpeed
     */
    public function setUploadSpeed(?int $uploadSpeed): void
    {
        $this->uploadSpeed = $uploadSpeed;
    }

    public function isForNewUsers(): ?bool
    {
        return $this->forNewUsers;
    }

    public function setForNewUsers(?bool $forNewUsers): void
    {
        $this->forNewUsers = $forNewUsers;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): void
    {
        $this->price = $price;
    }

    public function getPercentageDiscount(): ?int
    {
        return $this->percentageDiscount;
    }

    public function setPercentageDiscount(?int $percentageDiscount): void
    {
        $this->percentageDiscount = $percentageDiscount;
    }
}
