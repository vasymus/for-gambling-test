<?php

namespace App\Support\Affiliate\Finder;

use App\DTOs\GeoPositionDTO;

interface FinderInterface
{
    /**
     * @param \App\DTOs\GeoPositionDTO $to
     * @param int $limitKm
     * @param \App\DTOs\AffiliateDTO[] $affiliates
     *
     * @return \App\DTOs\AffiliateDTO[]
     */
    public function getClosest(GeoPositionDTO $to, int $limitKm, array $affiliates): array;
}
