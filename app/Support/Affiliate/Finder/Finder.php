<?php

namespace App\Support\Affiliate\Finder;

use App\DTOs\AffiliateDTO;
use App\DTOs\GeoPositionDTO;
use App\Support\Affiliate\DistanceCalculator\DistanceCalculatorInterface;

class Finder implements FinderInterface
{
    /**
     * @param \App\Support\Affiliate\DistanceCalculator\DistanceCalculatorInterface $distanceCalculator
     */
    public function __construct(
        private readonly DistanceCalculatorInterface $distanceCalculator
    ) {}

    /**
     * @param \App\DTOs\GeoPositionDTO $to
     * @param int $limitKm
     * @param \App\DTOs\AffiliateDTO[] $affiliates
     *
     * @return \App\DTOs\AffiliateDTO[]
     */
    public function getClosest(GeoPositionDTO $to, int $limitKm, array $affiliates): array
    {
        return collect($affiliates)
            ->filter(
                fn(AffiliateDTO $affiliateDTO) => $this->passLimit($to, $affiliateDTO, $limitKm)
            )
            ->values()
            ->all();
    }

    /**
     * @param \App\DTOs\GeoPositionDTO $to
     * @param \App\DTOs\AffiliateDTO $affiliateDTO
     * @param int $limitKm
     *
     * @return bool
     */
    private function passLimit(GeoPositionDTO $to, AffiliateDTO $affiliateDTO, int $limitKm): bool
    {
        $distanceInM = $this->distanceCalculator->vincentyGreatCircleDistance(
            $affiliateDTO->geoPositionDTO->latitude,
            $affiliateDTO->geoPositionDTO->longitude,
            $to->latitude,
            $to->longitude
        );

        $distanceInKm = $distanceInM / 1000;

        return $limitKm > $distanceInKm;
    }
}
