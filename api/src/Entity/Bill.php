<?php

namespace App\Entity;

use App\Repository\BillRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BillRepository::class)]
class Bill
{
    public const STATUS_AWAITING_PAYMENT = 0;
    public const STATUS_PAID = 1;
    public const STATUS_PAID_PARTIALLY = 2;
    public const STATUS_PAIMENT_DELAYED = 3;
    public const STATUS_NOT_PAID = 4;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $number;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateOfIssue = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $paymentDate = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: false)]
    private int $status = 0;

    #[ORM\Column(type: Types::FLOAT, nullable: false)]
    private float $totalAmount;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $payDue = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updateDate = null;

    #[ORM\ManyToOne(inversedBy: 'bills')]
    #[ORM\JoinColumn(nullable: false)]
    private Customer $customer;

    #[ORM\ManyToOne(inversedBy: 'bills')]
    private ?Contract $contract = null;

    #[ORM\OneToMany(mappedBy: 'bill', targetEntity: BillPosition::class)]
    private Collection $billPositions;

    #[ORM\OneToMany(mappedBy: 'bill', targetEntity: Payment::class)]
    private Collection $payments;

    public function __construct()
    {
        $this->billPositions = new ArrayCollection();
        $this->payments = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getDateOfIssue(): \DateTimeInterface
    {
        return $this->dateOfIssue;
    }

    public function setDateOfIssue(\DateTimeInterface $dateOfIssue): self
    {
        $this->dateOfIssue = $dateOfIssue;

        return $this;
    }

    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(\DateTimeInterface $paymentDate): self
    {
        $this->paymentDate = $paymentDate;

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

    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(float $totalAmount): self
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getPayDue(): \DateTimeInterface
    {
        return $this->payDue;
    }

    public function setPayDue(\DateTimeInterface $payDue): self
    {
        $this->payDue = $payDue;

        return $this;
    }

    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->updateDate;
    }

    public function setUpdateDate(\DateTimeInterface $updateDate): self
    {
        $this->updateDate = $updateDate;

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

    public function getContract(): ?Contract
    {
        return $this->contract;
    }

    public function setContract(Contract $contract): self
    {
        $this->contract = $contract;

        return $this;
    }

    /**
     * @return Collection<int, BillPosition>
     */
    public function getBillPositions(): Collection
    {
        return $this->billPositions;
    }

    public function addBillPosition(BillPosition $billPosition): self
    {
        if (!$this->billPositions->contains($billPosition)) {
            $this->billPositions->add($billPosition);
            $billPosition->setBill($this);
        }

        return $this;
    }

    public function removeBillPosition(BillPosition $billPosition): self
    {
        if ($this->billPositions->removeElement($billPosition)) {
            // set the owning side to null (unless already changed)
            if ($billPosition->getBill() === $this) {
                $billPosition->setBill(null);
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
            $payment->setBill($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getBill() === $this) {
                $payment->setBill(null);
            }
        }

        return $this;
    }
}
