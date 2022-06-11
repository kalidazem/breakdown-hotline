<?php

declare(strict_types=1);

namespace BreakdownHotline\Tests\Integration\API;

use BreakdownHotline\API\PostcodesAPI;
use PHPUnit\Framework\TestCase;

class PostcodesAPITest extends TestCase
{
    public function test_is_valid_postcode_valid()
    {
        $http_transport = new \WP_Http();

        $client = new PostcodesAPI($http_transport);
        $this->assertTrue($client->is_valid_postcode('tw47pl'));
    }

    public function test_is_valid_postcode_invalid()
    {
        $http_transport =  new \WP_Http();
        $client = new PostcodesAPI($http_transport);
        $this->assertFalse($client->is_valid_postcode('twdsx0'));
    }

  

    public function get_http_transport_mock()
    {
        return  $this->getMockBuilder(\WP_Http::class)->getMock();
    }
}
