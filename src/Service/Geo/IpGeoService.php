<?php

namespace App\Service\Geo;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class IpGeoService implements GeoLocatorInterface
{
    private const API_LOCATE = 'https://www.iplocate.io/api/lookup/';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {}

    public function getCountryByIp(string $ip): ?string
    {
        if (in_array($ip, ['127.0.0.1', '::1', '0.0.0.0'])) {
            return 'localhost';
        }

        try {
            $response = $this->httpClient->request('GET', self::API_LOCATE . $ip, [
                'timeout' => 5
            ]);

            $data = $response->toArray();

            return $data['country'] ?? null;
        } catch (\Throwable $exception) {
            return null;
        }
    }
}