<?php

namespace App\Support\Affiliate\DistanceCalculator;

interface DistanceCalculatorInterface
{
    /**
     * @param float $latitudeFrom
     * @param float $longitudeFrom
     * @param float $latitudeTo
     * @param float $longitudeTo
     * @param float $earthRadiusInM
     *
     * @return float
     */
    public function vincentyGreatCircleDistance(
        float $latitudeFrom,
        float $longitudeFrom,
        float $latitudeTo,
        float $longitudeTo,
        float $earthRadiusInM = 6371000
    ): float;
}
