<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $createdDate;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false)]
    private bool $isReaded = false;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateOfRead = null;

    #[ORM\ManyToOne]
    private ?ServiceRequest $serviceRequest = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getCreatedDate(): \DateTimeInterface
    {
        return $this->createdDate;
    }

    public function setCreatedDate(\DateTimeInterface $createdDate): self
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    public function isIsReaded(): bool
    {
        return $this->isReaded;
    }

    public function setIsReaded(bool $isReaded): self
    {
        $this->isReaded = $isReaded;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDateOfRead(): ?\DateTimeInterface
    {
        return $this->dateOfRead;
    }

    public function setDateOfRead(\DateTimeInterface $dateOfRead): self
    {
        $this->dateOfRead = $dateOfRead;

        return $this;
    }

    public function getServiceRequest(): ?ServiceRequest
    {
        return $this->serviceRequest;
    }

    public function setServiceRequest(ServiceRequest $serviceRequest): self
    {
        $this->serviceRequest = $serviceRequest;

        return $this;
    }
}
