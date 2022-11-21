<?php

namespace Support\Affiliate\Facade;

use Illuminate\Support\Facades\Facade;
use Support\Affiliate\Contract\AffiliateInterface;

/**
 * @method static \DTOs\AffiliateDTO[] parse(\SplFileInfo $file)
 * @method static \DTOs\AffiliateDTO[] getClosest(\DTOs\GeoPositionDTO $to, int $limit, array $affiliates)
 */
class AffiliateFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return AffiliateInterface::class;
    }
}
