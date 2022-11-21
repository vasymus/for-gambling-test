<?php

namespace Support\Affiliate;

use DTOs\GeoPositionDTO;
use SplFileInfo;
use Support\Affiliate\Contract\AffiliateInterface;
use Support\Affiliate\Finder\FinderInterface;
use Support\Affiliate\Parser\TextParserInterface;

class AffiliateService implements AffiliateInterface
{
    /**
     * @param \Support\Affiliate\Parser\TextParserInterface $parser
     * @param \Support\Affiliate\Finder\FinderInterface $finder
     */
    public function __construct(
        private readonly TextParserInterface $parser,
        private readonly FinderInterface $finder
    ) {}

    /**
     * @param \SplFileInfo $file
     *
     * @return \DTOs\AffiliateDTO[]
     */
    public function parse(SplFileInfo $file): array
    {
        return $this->parser->parse($file);
    }

    /**
     * @param \DTOs\GeoPositionDTO $to
     * @param int $limit
     * @param \DTOs\AffiliateDTO[] $affiliates
     *
     * @return \DTOs\AffiliateDTO[]
     */
    public function getClosest(GeoPositionDTO $to, int $limit, array $affiliates): array
    {
        return $this->finder->getClosest($to, $limit, $affiliates);
    }
}
