<?php

namespace App\Entity;

use App\Repository\ServiceRequestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceRequestRepository::class)]
class ServiceRequest
{
    public const STATUS_CANCELLED = 3;
    public const STATUS_CLOSED = 2;
    public const STATUS_OPENED = 0;
    public const STATUS_REALIZATION = 1;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $createdDate;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $closeDate = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false)]
    private bool $isClosed = false;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'serviceRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private Customer $customer;

    #[ORM\OneToMany(mappedBy: 'serviceRequest', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\ManyToOne(inversedBy: 'serviceRequests')]
    private User $user;

    #[ORM\OneToMany(mappedBy: 'serviceRequest', targetEntity: ServiceVisit::class, orphanRemoval: true)]
    private Collection $serviceVisits;

    #[ORM\OneToMany(mappedBy: 'serviceRequest', targetEntity: Message::class)]
    private Collection $messages;

    #[ORM\ManyToOne(inversedBy: 'serviceRequests')]
    private ?Contract $contract = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: false)]
    private int $status = 0;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->serviceVisits = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): int
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

    public function getCloseDate(): ?\DateTimeInterface
    {
        return $this->closeDate;
    }

    public function setCloseDate(?\DateTimeInterface $closeDate): self
    {
        $this->closeDate = $closeDate;

        return $this;
    }

    public function getIsClosed(): bool
    {
        return $this->isClosed;
    }

    public function setIsClosed(bool $isClosed): self
    {
        $this->isClosed = $isClosed;

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

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
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
            $comment->setServiceRequest($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getServiceRequest() === $this) {
                $comment->setServiceRequest(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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
            $serviceVisit->setServiceRequest($this);
        }

        return $this;
    }

    public function removeServiceVisit(ServiceVisit $serviceVisit): self
    {
        if ($this->serviceVisits->removeElement($serviceVisit)) {
            // set the owning side to null (unless already changed)
            if ($serviceVisit->getServiceRequest() === $this) {
                $serviceVisit->setServiceRequest(null);
            }
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
            $message->setServiceRequest($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getServiceRequest() === $this) {
                $message->setServiceRequest(null);
            }
        }

        return $this;
    }

    public function getContract(): ?Contract
    {
        return $this->contract;
    }

    public function setContract(?Contract $contract): self
    {
        $this->contract = $contract;

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }
}
