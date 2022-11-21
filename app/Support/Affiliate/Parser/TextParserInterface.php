<?php

namespace App\Support\Affiliate\Parser;

use SplFileInfo;

interface TextParserInterface
{
    /**
     * @param \SplFileInfo $file
     *
     * @return \App\DTOs\AffiliateDTO[]
     */
    public function parse(SplFileInfo $file): array;
}
