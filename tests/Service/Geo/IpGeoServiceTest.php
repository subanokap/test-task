<?php

namespace App\Tests\Service\Geo;

use App\Service\Geo\IpGeoService;
use App\Tests\BaseTestCase;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class IpGeoServiceTest extends BaseTestCase
{
    private IpGeoService $ipGeoService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ipGeoService = new IpGeoService($this->httpClient);
    }

    public function testReturnsLocalhostForLocalIps(): void
    {
        $ips = ['127.0.0.1', '::1', '0.0.0.0'];

        $this->httpClient->expects($this->never())->method('request');

        foreach ($ips as $ip) {
            $this->assertSame('localhost', $this->ipGeoService->getCountryByIp($ip));
        }
    }

    public function testReturnsCountryOnSuccessfulResponse(): void
    {
        $testIp = '8.8.8.8';
        $expectedCountry = 'US';

        $mockResponse = $this->createMock(ResponseInterface::class);

        $mockResponse->method('toArray')->willReturn(['country' => $expectedCountry]);
        $mockResponse->method('getStatusCode')->willReturn(200);

        $this->httpClient->expects($this->once())->method('request')
            ->willReturn($mockResponse);

        $result = $this->ipGeoService->getCountryByIp($testIp);

        $this->assertSame($expectedCountry, $result);
    }

    public function testReturnsNullOnError(): void
    {
        $testIp = '1.1.1.1';

        $this->httpClient->expects($this->once())
            ->method('request')
            ->willThrowException($this->createMock(TransportExceptionInterface::class));

        $result = $this->ipGeoService->getCountryByIp($testIp);

        $this->assertNull($result);
    }
}