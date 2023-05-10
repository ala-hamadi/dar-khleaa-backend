<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ApiResource]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface  $checkin_date = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface  $checkout_date = null;

    #[ORM\Column]
    private ?float $total_price = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?Apartment $apartment = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?User $user = null;

    #[ORM\Column]
    private ?bool $validate = null;







    public function getId(): ?int
    {
        return $this->id;
    }



    public function getCheckinDate(): ?\DateTimeInterface
    {
        return $this->checkin_date;
    }

    public function setCheckinDate(\DateTimeInterface $checkin_date): self
    {
        $this->checkin_date = $checkin_date;

        return $this;
    }

    public function getCheckoutDate(): ?\DateTimeInterface
    {
        return $this->checkout_date;
    }

    public function setCheckoutDate(\DateTimeInterface $checkout_date): self
    {
        $this->checkout_date = $checkout_date;

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->total_price;
    }

    public function setTotalPrice(float $total_price): self
    {
        $this->total_price = $total_price;

        return $this;
    }

    public function getApartment(): ?Apartment
    {
        return $this->apartment;
    }

    public function setApartment(?Apartment $apartment): self
    {
        $this->apartment = $apartment;

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

    public function isValidate(): ?bool
    {
        return $this->validate;
    }

    public function setValidate(bool $validate): self
    {
        $this->validate = $validate;

        return $this;
    }







}
