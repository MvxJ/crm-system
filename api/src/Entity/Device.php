<?php

namespace App\Entity;

use App\Repository\DeviceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeviceRepository::class)]
class Device
{
    public const STATUS_AVAILABLE = 0;
    public const STATUS_RESERVED = 1;
    public const STATUS_DESTROYED = 2;
    public const STATUS_SOLD = 3;
    public const STATUS_RENTED = 4;
    public const STATUS_DEFEVTICE = 5;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $serialNumber = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $macAddress = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $boughtDate = null;

    #[ORM\ManyToOne(inversedBy: 'devices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Model $model = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: false)]
    private int $status = 0;

    #[ORM\ManyToOne(inversedBy: 'devices')]
    private ?Customer $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $soldDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    public function setSerialNumber(string $serialNumber): self
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    public function getMacAddress(): ?string
    {
        return $this->macAddress;
    }

    public function setMacAddress(?string $macAddress): self
    {
        $this->macAddress = $macAddress;

        return $this;
    }

    public function getBoughtDate(): ?\DateTimeInterface
    {
        return $this->boughtDate;
    }

    public function setBoughtDate(\DateTimeInterface $boughtDate): self
    {
        $this->boughtDate = $boughtDate;

        return $this;
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function setModel(?Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?Customer
    {
        return $this->user;
    }

    public function setUser(?Customer $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getSoldDate(): ?\DateTimeInterface
    {
        return $this->soldDate;
    }

    public function setSoldDate(?\DateTimeInterface $soldDate): self
    {
        $this->soldDate = $soldDate;

        return $this;
    }
}
