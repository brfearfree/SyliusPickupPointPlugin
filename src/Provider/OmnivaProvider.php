<?php

declare(strict_types=1);

namespace Setono\SyliusPickupPointPlugin\Provider;

use Mijora\Omniva\Locations\PickupPoints;
use Mijora\Omniva\OmnivaException;
use Setono\SyliusPickupPointPlugin\Model\PickupPointCode;
use Setono\SyliusPickupPointPlugin\Model\PickupPointInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class OmnivaProvider extends Provider
{
    private FactoryInterface $pickupPointFactory;

    private array $countryCodes;

    public function __construct(
        FactoryInterface $pickupPointFactory,
        array $countryCodes = ['LV']
    ) {
        $this->pickupPointFactory = $pickupPointFactory;
        $this->countryCodes = $countryCodes;
    }

    public function findPickupPoints(OrderInterface $order): iterable
    {
        if($parcelShops = $this->retrieveParcelShops('lv')){
            foreach ($parcelShops as $item) {
                yield $this->transform($item);
            }
        }
        else{
            return [];
        }
    }

    public function findPickupPoint(PickupPointCode $code): ?PickupPointInterface
    {
        $one = [];
        if($parcelShops = $this->retrieveParcelShops('lv')){
            $zip = $code->getIdPart();
            foreach ($parcelShops as $item) {
                if($item['ZIP'] === $zip){
                    $one = $item;
                    break;
                }
            }
        }
        return $this->transform($one);
    }

    public function findAllPickupPoints(): iterable
    {
        if($parcelShops = $this->retrieveParcelShops('lv')){
            foreach ($parcelShops as $item) {
                yield $this->transform($item);
            }
        }
        else{
            return [];
        }
    }

    public function getCode(): string
    {
        return 'omniva';
    }

    public function getName(): string
    {
        return 'Omniva';
    }

    private function transform(array $parcelShop): PickupPointInterface
    {
        /** @var PickupPointInterface|object $pickupPoint */
        $pickupPoint = $this->pickupPointFactory->createNew();

        Assert::isInstanceOf($pickupPoint, PickupPointInterface::class);
        if(isSet($parcelShop['ZIP'])){
            $pickupPoint->setCode(new PickupPointCode($parcelShop['ZIP'], $this->getCode(), $parcelShop['A0_NAME']));
            $pickupPoint->setName($parcelShop['NAME']);
            $pickupPoint->setAddress($parcelShop['A5_NAME'] . ' ' .$parcelShop['A6_NAME'] .' ' .$parcelShop['A7_NAME']);
            $pickupPoint->setZipCode($parcelShop['ZIP']);
            $pickupPoint->setCity($parcelShop['A2_NAME']);
            $pickupPoint->setCountry($parcelShop['A1_NAME']);
            $pickupPoint->setLatitude((float) $parcelShop['X_COORDINATE']);
            $pickupPoint->setLongitude((float) $parcelShop['Y_COORDINATE']);
        }

        return $pickupPoint;
    }

    private function retrieveParcelShops(string $country_code = 'lv') :array{
        try {
            $omnivaPickupPointsObj = new PickupPoints();
            return $omnivaPickupPointsObj->getFilteredLocations($country_code);
        } catch (OmnivaException $e) {
            return [];
        }
    }

}
