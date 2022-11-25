<?php

namespace App\Support\Affiliate;

use App\Support\Affiliate\DistanceCalculator\DistanceCalculator;
use Illuminate\Support\ServiceProvider;
use App\Support\Affiliate\Contract\AffiliateInterface;
use App\Support\Affiliate\Finder\Finder;
use App\Support\Affiliate\Parser\TextParser;

class AffiliateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return mixed
     */
    public function register()
    {
        $this->app->bind(AffiliateInterface::class, function() {
            return new AffiliateService(
                new TextParser(),
                new Finder(
                    new DistanceCalculator()
                )
            );
        });
    }
}
