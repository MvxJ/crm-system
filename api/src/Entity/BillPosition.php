<?php

namespace App\Entity;

use App\Repository\BillPositionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BillPositionRepository::class)]
class BillPosition
{
    public const TYPE_CONTRACT = 0;
    public const TYPE_SERVICE = 1;
    public const TYPE_DEVICE = 2;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: Types::SMALLINT, nullable: false)]
    private int $type = 0;

    #[ORM\Column(type: Types::FLOAT, nullable: false)]
    private float $price;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    private int $amount = 1;

    #[ORM\ManyToOne(inversedBy: 'billPositions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Bill $bill;

    #[ORM\Column(length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getBill(): Bill
    {
        return $this->bill;
    }

    public function setBill(?Bill $bill): self
    {
        $this->bill = $bill;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
