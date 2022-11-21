<?php

namespace App\Support\Affiliate\Finder;

use App\DTOs\GeoPositionDTO;

interface FinderInterface
{
    /**
     * @param \App\DTOs\GeoPositionDTO $to
     * @param int $limit
     * @param \App\DTOs\AffiliateDTO[] $affiliates
     *
     * @return \App\DTOs\AffiliateDTO[]
     */
    public function getClosest(GeoPositionDTO $to, int $limit, array $affiliates): array;
}
