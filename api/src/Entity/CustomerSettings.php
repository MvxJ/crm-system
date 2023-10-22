<?php

namespace App\Entity;

use App\Repository\CustomerSettingsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerSettingsRepository::class)]
class CustomerSettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $emailNotifications = true;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $smsNotifications = true;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?CustomerAddress $billingAddress = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?CustomerAddress $contactAddress = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmailNotifications(): ?int
    {
        return $this->emailNotifications;
    }

    public function setEmailNotifications(int $emailNotifications): self
    {
        $this->emailNotifications = $emailNotifications;

        return $this;
    }

    public function isSmsNotifications(): ?bool
    {
        return $this->smsNotifications;
    }

    public function setSmsNotifications(bool $smsNotifications): self
    {
        $this->smsNotifications = $smsNotifications;

        return $this;
    }

    public function getBillingAddress(): ?CustomerAddress
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(?CustomerAddress $billingAddress): self
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    public function getContactAddress(): ?CustomerAddress
    {
        return $this->contactAddress;
    }

    public function setContactAddress(CustomerAddress $contactAddress): self
    {
        $this->contactAddress = $contactAddress;

        return $this;
    }
}
