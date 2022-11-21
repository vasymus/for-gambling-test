<?php

namespace Support\Affiliate\Parser;

use SplFileInfo;

interface TextParserInterface
{
    /**
     * @param \SplFileInfo $file
     *
     * @return \DTOs\AffiliateDTO[]
     */
    public function parse(SplFileInfo $file): array;
}
