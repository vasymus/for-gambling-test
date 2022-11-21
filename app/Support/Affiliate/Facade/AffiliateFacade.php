<?php

namespace App\Support\Affiliate\Facade;

use Illuminate\Support\Facades\Facade;
use App\Support\Affiliate\Contract\AffiliateInterface;

/**
 * @method static \App\DTOs\AffiliateDTO[] parse(\SplFileInfo $file)
 * @method static \App\DTOs\AffiliateDTO[] getClosest(\App\DTOs\GeoPositionDTO $to, int $limit, array $affiliates)
 */
class AffiliateFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return AffiliateInterface::class;
    }
}
