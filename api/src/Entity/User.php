<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface, TwoFactorInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\ManyToMany(targetEntity: Role::Class, inversedBy: "users")]
    private Collection $roles;

    #[ORM\OneToOne(targetEntity: UserProfile::class, mappedBy: "user", cascade: ['persist', 'remove'])]
    private $profile;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\Column(nullable: true)]
    private ?string $authCode;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
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

    /**
     * @return UserProfile|null
     */
    public function getUserProfile(): ?UserProfile
    {
        return $this->userProfile;
    }

    /**
     * @param UserProfile $userProfile
     * @return $this
     */
    public function setUserProfile(UserProfile $userProfile): self
    {
        if ($userProfile->getUser() !== $this) {
            $userProfile->setUser($this);
        }

        $this->userProfile = $userProfile;

        return $this;
    }

    /**
     * @return UserProfile|null
     */
    public function getProfile(): ?UserProfile
    {
        return $this->profile;
    }

    /**
     * @param UserProfile $userProfile
     * @return $this
     */
    public function setProfile(UserProfile $userProfile): self
    {
        if ($userProfile->getUser() !== $this) {
            $userProfile->setUser($this);
        }

        $this->profile = $userProfile;

        return $this;
    }

    /**
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    /**
     * @param bool $isVerified
     * @return $this
     */
    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function isEmailAuthEnabled(): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function getEmailAuthRecipient(): string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getEmailAuthCode(): ?string
    {
        return $this->authCode;
    }

    /**
     * @param string $authCode
     */
    public function setEmailAuthCode(string $authCode): void
    {
        $this->authCode = $authCode;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
