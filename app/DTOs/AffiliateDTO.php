<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

class AffiliateDTO extends Data
{
    public function __construct(
        public int $id,
        public ?string $name,
        public GeoPositionDTO $geoPositionDTO
    ) {}
}
