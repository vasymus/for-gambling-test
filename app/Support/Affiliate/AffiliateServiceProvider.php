<?php

namespace Support\Affiliate;

use Illuminate\Support\ServiceProvider;
use Support\Affiliate\Contract\AffiliateInterface;
use Support\Affiliate\Finder\Finder;
use Support\Affiliate\Parser\TextParser;

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
                new Finder()
            );
        });
    }
}
