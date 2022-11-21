<?php

namespace App\Support\Affiliate\Parser;

use App\DTOs\AffiliateDTO;
use App\DTOs\GeoPositionDTO;
use Exception;
use SplFileInfo;

class TextParser implements TextParserInterface
{
    private const KEY_ID = 'affiliate_id';
    private const KEY_NAME = 'name';
    private const KEY_LATITUDE = 'latitude';
    private const KEY_LONGITUDE = 'longitude';

    /**
     * @param \SplFileInfo $file
     *
     * @return \App\DTOs\AffiliateDTO[]
     */
    public function parse(SplFileInfo $file): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            return [];
        }

        $result = [];

        while (!feof($handle)) {
            $line = fgets($handle);
            $affiliate = $this->parseLine((string)$line);
            if (!$affiliate) {
                continue;
            }

            $result[] = $affiliate;
        }
        fclose($handle);

        return $result;
    }

    /**
     * @param string $line
     *
     * @return \App\DTOs\AffiliateDTO|null
     */
    private function parseLine(string $line): ?AffiliateDTO
    {
        $affArr = json_decode($line, true);
        if (!$affArr) {
            return null;
        }

        try {
            $lat = $affArr[self::KEY_LATITUDE];
            $lon = $affArr[self::KEY_LONGITUDE];
            $geoPosition = new GeoPositionDTO((float)$lat, (float)$lon);

            return new AffiliateDTO(
                $affArr[self::KEY_ID],
                $affArr[self::KEY_NAME],
                $geoPosition
            );
        } catch (Exception) {
            return null;
        }
    }
}
