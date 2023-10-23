<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer implements UserInterface, PasswordAuthenticatedUserInterface, TwoFactorInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING)]
    private string $password;

    #[ORM\Column(type: Types::STRING)]
    private string $email;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $emailAuthCode;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false)]
    private bool $authenticated = false;

    #[ORM\Column(type: Types::BOOLEAN,nullable: false)]
    private bool $emailAuthEnabled = false;

    #[ORM\ManyToMany(targetEntity: Role::Class, inversedBy: "customers")]
    private Collection $roles;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Device::class)]
    private Collection $devices;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: ServiceRequest::class)]
    private Collection $serviceRequests;

    #[ORM\OneToMany(mappedBy: 'Customer', targetEntity: Message::class, orphanRemoval: true)]
    private Collection $messages;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Contract::class, orphanRemoval: true)]
    private Collection $contracts;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: ServiceVisit::class)]
    private Collection $serviceVisits;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Bill::class)]
    private Collection $bills;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Payment::class)]
    private Collection $payments;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: CustomerAddress::class)]
    private Collection $customerAddresses;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $secondName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\Column(length: 13)]
    private ?string $socialSecurityNumber = null;

    #[ORM\OneToOne(inversedBy: 'customer', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?CustomerSettings $settings = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $phoneNumber = null;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->devices = new ArrayCollection();
        $this->serviceRequests = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->contracts = new ArrayCollection();
        $this->serviceVisits = new ArrayCollection();
        $this->bills = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->customerAddresses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roleNames = [];
        $userRoles = $this->roles->toArray();
        foreach ($userRoles as $userRole) {
            $roleNames[] = $userRole->getRole();
        }

        return array_unique($roleNames);
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function getObjRoles() {
        return $this->roles->toArray();
    }

    public function hasObjRole(Role $role): ?bool {
        return( $this->roles->contains($role) );
    }

    public function getRolesNames(): array
    {
        $roleNames = [];
        $userRoles = $this->roles;
        foreach ($userRoles as $userRole) {
            $roleNames[] = $userRole->getName();
        }
        return array_unique($roleNames);
    }

    public function removeRole(Role $role): self
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
        }

        return $this;
    }

    public function setAuthenticated(bool $authenticated): self
    {
        $this->authenticated = $authenticated;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->authenticated;
    }

    public function eraseCredentials()
    {
    }

    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    public function isEmailAuthEnabled(): bool
    {
        return $this->emailAuthEnabled;
    }

    public function setEmailAuthEnabled(bool $emailAuthEnabled): self
    {
        $this->emailAuthEnabled = $emailAuthEnabled;

        return $this;
    }

    public function getEmailAuthRecipient(): string
    {
        return $this->email;
    }

    public function getEmailAuthCode(): ?string
    {
        return $this->emailAuthCode;
    }

    public function setEmailAuthCode(string $authCode): void
    {
        $this->emailAuthCode = $authCode;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, Device>
     */
    public function getDevices(): Collection
    {
        return $this->devices;
    }

    public function addDevice(Device $device): self
    {
        if (!$this->devices->contains($device)) {
            $this->devices->add($device);
            $device->setUser($this);
        }

        return $this;
    }

    public function removeDevice(Device $device): self
    {
        if ($this->devices->removeElement($device)) {
            if ($device->getUser() === $this) {
                $device->setUser(null);
            }
        }

        return $this;
    }

    public function getServiceRequests(): Collection
    {
        return $this->serviceRequests;
    }

    public function addServiceRequest(ServiceRequest $serviceRequest): self
    {
        if (!$this->serviceRequests->contains($serviceRequest)) {
            $this->devices->add($serviceRequest);
            $serviceRequest->setCustomer($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setCustomer($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getCustomer() === $this) {
                $message->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Contract>
     */
    public function getContracts(): Collection
    {
        return $this->contracts;
    }

    public function addContract(Contract $contract): self
    {
        if (!$this->contracts->contains($contract)) {
            $this->contracts->add($contract);
            $contract->setUser($this);
        }

        return $this;
    }

    public function removeContract(Contract $contract): self
    {
        if ($this->contracts->removeElement($contract)) {
            // set the owning side to null (unless already changed)
            if ($contract->getUser() === $this) {
                $contract->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ServiceVisit>
     */
    public function getServiceVisits(): Collection
    {
        return $this->serviceVisits;
    }

    public function addServiceVisit(ServiceVisit $serviceVisit): self
    {
        if (!$this->serviceVisits->contains($serviceVisit)) {
            $this->serviceVisits->add($serviceVisit);
            $serviceVisit->setCustomer($this);
        }

        return $this;
    }

    public function removeServiceVisit(ServiceVisit $serviceVisit): self
    {
        if ($this->serviceVisits->removeElement($serviceVisit)) {
            // set the owning side to null (unless already changed)
            if ($serviceVisit->getCustomer() === $this) {
                $serviceVisit->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Bill>
     */
    public function getBills(): Collection
    {
        return $this->bills;
    }

    public function addBill(Bill $bill): self
    {
        if (!$this->bills->contains($bill)) {
            $this->bills->add($bill);
            $bill->setCustomer($this);
        }

        return $this;
    }

    public function removeBill(Bill $bill): self
    {
        if ($this->bills->removeElement($bill)) {
            // set the owning side to null (unless already changed)
            if ($bill->getCustomer() === $this) {
                $bill->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setCustomer($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getCustomer() === $this) {
                $payment->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CustomerAddress>
     */
    public function getCustomerAddresses(): Collection
    {
        return $this->customerAddresses;
    }

    public function addCustomerAddress(CustomerAddress $customerAddress): self
    {
        if (!$this->customerAddresses->contains($customerAddress)) {
            $this->customerAddresses->add($customerAddress);
            $customerAddress->setCustomer($this);
        }

        return $this;
    }

    public function removeCustomerAddress(CustomerAddress $customerAddress): self
    {
        if ($this->customerAddresses->removeElement($customerAddress)) {
            // set the owning side to null (unless already changed)
            if ($customerAddress->getCustomer() === $this) {
                $customerAddress->setCustomer(null);
            }
        }

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getSecondName(): ?string
    {
        return $this->secondName;
    }

    public function setSecondName(?string $secondName): self
    {
        $this->secondName = $secondName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getSocialSecurityNumber(): ?string
    {
        return $this->socialSecurityNumber;
    }

    public function setSocialSecurityNumber(string $socialSecurityNumber): self
    {
        $this->socialSecurityNumber = $socialSecurityNumber;

        return $this;
    }

    public function getSettings(): ?CustomerSettings
    {
        return $this->settings;
    }

    public function setSettings(CustomerSettings $settings): static
    {
        $this->settings = $settings;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }
}
