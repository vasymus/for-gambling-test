<?php

namespace App\Support\Affiliate;

use App\DTOs\GeoPositionDTO;
use SplFileInfo;
use App\Support\Affiliate\Contract\AffiliateInterface;
use App\Support\Affiliate\Finder\FinderInterface;
use App\Support\Affiliate\Parser\TextParserInterface;

class AffiliateService implements AffiliateInterface
{
    /**
     * @param \App\Support\Affiliate\Parser\TextParserInterface $parser
     * @param \App\Support\Affiliate\Finder\FinderInterface $finder
     */
    public function __construct(
        private readonly TextParserInterface $parser,
        private readonly FinderInterface $finder
    ) {}

    /**
     * @param \SplFileInfo $file
     *
     * @return \App\DTOs\AffiliateDTO[]
     */
    public function parse(SplFileInfo $file): array
    {
        return $this->parser->parse($file);
    }

    /**
     * @param \App\DTOs\GeoPositionDTO $to
     * @param int $limitKm
     * @param \App\DTOs\AffiliateDTO[] $affiliates
     *
     * @return \App\DTOs\AffiliateDTO[]
     */
    public function getClosest(GeoPositionDTO $to, int $limitKm, array $affiliates): array
    {
        return $this->finder->getClosest($to, $limitKm, $affiliates);
    }
}
