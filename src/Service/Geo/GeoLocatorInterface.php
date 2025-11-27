<?php

namespace App\Service\Geo;

interface GeoLocatorInterface
{
    public function getCountryByIp(string $ip): ?string;
}

