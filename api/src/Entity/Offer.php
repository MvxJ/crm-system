<?php

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: OfferRepository::class)]
class Offer
{
    public const TYPE_INTERNET = 0;
    public const TYPE_TELEVISION = 1;
    public const TYPE_INTERNET_AND_TELEVISION = 2;
    public const DISCOUNT_TYPE_ABSOLUTE = 0;
    public const DISCOUNT_TYPE_PERCENTAGE = 1;
    public const DURATION_12_M = 0;
    public const DURATION_24_M = 1;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\Column(length: 255, nullable: false)]
    private string $title;

    #[ORM\Column(type: Types::TEXT ,nullable: false)]
    private string $description;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?int $downloadSpeed = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?int $uploadSpeed = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false)]
    private bool $newUsers = false;

    #[ORM\Column(type: Types::FLOAT, nullable: false)]
    private float $price;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $discount = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: false)]
    private int $type = 0;

    #[ORM\Column(type: Types::SMALLINT, nullable: false)]
    private int $duration = 0;

    #[ORM\ManyToMany(targetEntity: Model::class)]
    private Collection $devices;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $numberOfCanals = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false)]
    private bool $forStudents = false;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $discountType = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $validDue = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false)]
    private bool $deleted = false;

    public function __construct()
    {
        $this->devices = new ArrayCollection();
    }

    public function getId(): ?Uuid
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
    public function getDownloadSpeed(): ?float
    {
        return $this->downloadSpeed;
    }

    /**
     * @param int|null $downloadSpeed
     */
    public function setDownloadSpeed(?float $downloadSpeed): void
    {
        $this->downloadSpeed = $downloadSpeed;
    }

    /**
     * @return int|null
     */
    public function getUploadSpeed(): ?float
    {
        return $this->uploadSpeed;
    }

    /**
     * @param int|null $uploadSpeed
     */
    public function setUploadSpeed(?float $uploadSpeed): void
    {
        $this->uploadSpeed = $uploadSpeed;
    }

    public function isForNewUsers(): bool
    {
        return $this->newUsers;
    }

    public function setNewUsers(bool $newUsers): void
    {
        $this->newUsers = $newUsers;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    public function setDiscount(float $discount): void
    {
        $this->discount = $discount;
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

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return Collection<int, Model>
     */
    public function getDevices(): Collection
    {
        return $this->devices;
    }

    public function addDevice(Model $device): self
    {
        if (!$this->devices->contains($device)) {
            $this->devices->add($device);
        }

        return $this;
    }

    public function removeDevice(Model $device): self
    {
        $this->devices->removeElement($device);

        return $this;
    }

    public function getNumberOfCanals(): ?int
    {
        return $this->numberOfCanals;
    }

    public function setNumberOfCanals(int $numberOfCanals): self
    {
        $this->numberOfCanals = $numberOfCanals;

        return $this;
    }

    public function isForStudents(): bool
    {
        return $this->forStudents;
    }

    public function setForStudents(bool $forStudents): self
    {
        $this->forStudents = $forStudents;

        return $this;
    }

    public function getDiscountType(): ?int
    {
        return $this->discountType;
    }

    public function setDiscountType(int $discountType): self
    {
        $this->discountType = $discountType;

        return $this;
    }

    public function getValidDue(): ?\DateTimeInterface
    {
        return $this->validDue;
    }

    public function setValidDue(\DateTimeInterface $validDue): self
    {
        $this->validDue = $validDue;

        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }
}
