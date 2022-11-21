<?php

namespace Support\Affiliate\Contract;

use DTOs\GeoPositionDTO;
use SplFileInfo;

interface AffiliateInterface
{
    /**
     * @param \SplFileInfo $file
     *
     * @return \DTOs\AffiliateDTO[]
     */
    public function parse(SplFileInfo $file): array;

    /**
     * @param \DTOs\GeoPositionDTO $to
     * @param int $limit
     * @param \DTOs\AffiliateDTO[] $affiliates
     *
     * @return \DTOs\AffiliateDTO[]
     */
    public function getClosest(GeoPositionDTO $to, int $limit, array $affiliates): array;
}
