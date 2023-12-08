<?php

namespace App\Entity;

use App\Repository\CustomerSettingsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: CustomerSettingsRepository::class)]
class CustomerSettings
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $emailNotifications = true;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $smsNotifications = true;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?CustomerAddress $billingAddress = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?CustomerAddress $contactAddress = null;

    #[ORM\OneToOne(mappedBy: 'settings', cascade: ['persist', 'remove'])]
    private ?Customer $customer = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getEmailNotifications(): ?bool
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

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): static
    {
        // set the owning side of the relation if necessary
        if ($customer->getSettings() !== $this) {
            $customer->setSettings($this);
        }

        $this->customer = $customer;

        return $this;
    }
}
