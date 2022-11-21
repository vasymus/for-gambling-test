<?php

namespace Support\Affiliate\Finder;

use DTOs\GeoPositionDTO;

interface FinderInterface
{
    /**
     * @param \DTOs\GeoPositionDTO $to
     * @param int $limit
     * @param \DTOs\AffiliateDTO[] $affiliates
     *
     * @return \DTOs\AffiliateDTO[]
     */
    public function getClosest(GeoPositionDTO $to, int $limit, array $affiliates): array;
}
