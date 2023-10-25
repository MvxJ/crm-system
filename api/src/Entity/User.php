<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\Column(nullable: true)]
    private ?string $authCode;

    #[ORM\Column]
            private bool $emailAuth = false;

    #[ORM\Column(nullable: true)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?string $surname = null;

    #[ORM\Column(nullable: true)]
    private ?string $phoneNumber = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ServiceRequest::class)]
    private Collection $serviceRequests;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ServiceVisit::class, orphanRemoval: true)]
    private Collection $serviceVisits;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Document::class, orphanRemoval: true)]
    private Collection $documents;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Notification::class, orphanRemoval: true)]
    private Collection $notifications;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isDeleted = false;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->serviceRequests = new ArrayCollection();
        $this->serviceVisits = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->notifications = new ArrayCollection();
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

    /**
     * @return bool
     */
    public function isEmailAuthEnabled(): bool
    {
        return $this->emailAuth;
    }

    /**
     * @param bool $emailAuth
     * @return $this
     */
    public function setEmailAuth(bool $emailAuth): self
    {
        $this->emailAuth = $emailAuth;

        return $this;
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

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ServiceRequest>
     */
    public function getServiceRequests(): Collection
    {
        return $this->serviceRequests;
    }

    public function addServiceRequest(ServiceRequest $serviceRequest): self
    {
        if (!$this->serviceRequests->contains($serviceRequest)) {
            $this->serviceRequests->add($serviceRequest);
            $serviceRequest->setUser($this);
        }

        return $this;
    }

    public function removeServiceRequest(ServiceRequest $serviceRequest): self
    {
        if ($this->serviceRequests->removeElement($serviceRequest)) {
            // set the owning side to null (unless already changed)
            if ($serviceRequest->getUser() === $this) {
                $serviceRequest->setUser(null);
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
            $serviceVisit->setUser($this);
        }

        return $this;
    }

    public function removeServiceVisit(ServiceVisit $serviceVisit): self
    {
        if ($this->serviceVisits->removeElement($serviceVisit)) {
            // set the owning side to null (unless already changed)
            if ($serviceVisit->getUser() === $this) {
                $serviceVisit->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setAuthor($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getAuthor() === $this) {
                $document->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }

    public function isIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }
}
