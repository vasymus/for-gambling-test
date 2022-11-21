<?php

namespace Support\Affiliate\Parser;

use SplFileInfo;

class TextParser implements TextParserInterface
{
    /**
     * @param \SplFileInfo $file
     *
     * @return \DTOs\AffiliateDTO[]
     */
    public function parse(SplFileInfo $file): array
    {
        return [];
    }
}
