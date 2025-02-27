<?php

declare(strict_types=1);

namespace Setono\SyliusPickupPointPlugin\Model;

use Doctrine\ORM\Mapping as ORM;

trait PickupPointAwareTrait
{
    /** @ORM\Column(name="pickup_point_id", type="string", nullable=true) */
    protected ?string $pickupPointId = null;

    public function hasPickupPointId(): bool
    {
        return null !== $this->pickupPointId;
    }

    public function setPickupPointId(?string $pickupPointId): void
    {
        $this->pickupPointId = $pickupPointId;
    }

    public function getPickupPointId(): ?string
    {
        return $this->pickupPointId;
    }
}
