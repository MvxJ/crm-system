<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Security\Core\Role\Role as CoreRole;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role extends CoreRole
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_CUSTOMER = 'ROLE_CUSTOMER';
    const ROLE_MARKETING = 'ROLE_MARKETING';
    const ROLE_SERVICE = 'ROLE_SERVICE';
    const ROLE_ACCOUNTMENT = 'ROLE_ACCOUNTMENT';
    const ROLE_ACCESS_ADMIN_PANEL = 'ROLE_ACCESS_ADMIN_PANEL';

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 50)]
    private string $role;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: "roles")]
    private Collection $users;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: "roles")]
    private Collection $customers;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->customers = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $role
     * @return $this
     */
    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addRole($this);
        }

        return $this;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeRole($this);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function addCustomer(Customer $customer): self
    {
        if (!$this->customers->contains($customer)) {
            $this->customers->add($customer);
            $customer->addRole($this);
        }

        return $this;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function removeCustomer(Customer $customer): self
    {
        if ($this->customers->contains($customer)) {
            $this->customers->removeElement($customer);
            $customer->removeRole($this);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }
}
