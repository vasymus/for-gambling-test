<?php

namespace App\Support\Affiliate\Contract;

use App\DTOs\GeoPositionDTO;
use SplFileInfo;

interface AffiliateInterface
{
    /**
     * @param \SplFileInfo $file
     *
     * @return \App\DTOs\AffiliateDTO[]
     */
    public function parse(SplFileInfo $file): array;

    /**
     * @param \App\DTOs\GeoPositionDTO $to
     * @param int $limitKm
     * @param \App\DTOs\AffiliateDTO[] $affiliates
     *
     * @return \App\DTOs\AffiliateDTO[]
     */
    public function getClosest(GeoPositionDTO $to, int $limitKm, array $affiliates): array;
}
