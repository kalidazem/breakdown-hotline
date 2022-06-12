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

    public function test_get_borough_name_by_postcode_with_invalid_postcode()
    {
        $http_transport =  new \WP_Http();
        $client = new PostcodesAPI($http_transport);
        $error = $client->get_borough_name_by_postcode('tw4ss');
        $this->assertInstanceOf(\WP_Error::class,  $error);
        $this->assertEquals(404, $error->get_error_data()['status_code']);
        $this->assertEquals('invalid', $error->get_error_code());
    }


    public function test_get_borough_name_by_postcode_with_valid_postcode()
    {
        $http_transport = new \WP_Http();
        $client = new PostcodesAPI($http_transport);
        $data = $client->get_borough_name_by_postcode('m47dn');
        $this->assertEqualsIgnoringCase('manchester', $data);
    }



    public function get_http_transport_mock()
    {
        return  $this->getMockBuilder(\WP_Http::class)->getMock();
    }
}
