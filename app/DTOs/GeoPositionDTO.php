<?php

namespace DTOs;

use Spatie\LaravelData\Data;

class GeoPositionDTO extends Data
{
    public function __construct(
        public float $latitude,
        public float $longitude
    ) {}
}
