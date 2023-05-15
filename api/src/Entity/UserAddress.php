<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserAddressRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserAddressRepository::class)]
class UserAddress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $country = null;

    #[ORM\Column(length: 50)]
    private ?string $city = null;

    #[ORM\Column(length: 50)]
    private ?string $street = null;

    #[ORM\Column(length: 50)]
    private ?string $houseNumber = null;

    #[ORM\Column(length: 7)]
    private ?string $zipCode = null;

    #[ORM\Column]
    private bool $isInvoiceAddress = false;

    public function getId(): ?int
    {
        return $this->id;
    }
}
