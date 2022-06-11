<?php

declare(strict_types=1);

namespace BreakdownHotline\Tests\Unit\API;

use BreakdownHotline\API\PostcodesAPI;
use PHPUnit\Framework\TestCase;

class PostcodesAPITest extends TestCase
{
    public function test_is_valid_postcode_valid()
    {
        $http_transport = $this->get_http_transport_mock();
        $http_transport->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('https://api.postcodes.io/postcodes/tw47pl/validate'))
            ->willReturn(array(
                'response' => array('code' => 200),
                'body' => json_encode(['result' => true, 'status' => 200])
            ));


        $client = new PostcodesAPI($http_transport);
        $this->assertTrue($client->is_valid_postcode('tw47pl'));
    }

    public function test_is_valid_postcode_invalid()
    {
        $http_transport = $this->get_http_transport_mock();
        $http_transport->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('https://api.postcodes.io/postcodes/tw47ps/validate'))
            ->willReturn(array(
                'response' => array('code' => 200),
                'body' => json_encode(array("status"=>200,"result"=>false))
            ));


        $client = new PostcodesAPI($http_transport);
        // $client = new PostcodesAPI(new \WP_Http());
        $this->assertFalse($client->is_valid_postcode('tw47ps'));
    }

    public function test_is_valid_postcode_with_invalid_response()
    {
        $http_transport = $this->get_http_transport_mock();

        $http_transport->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('https://api.postcodes.io/postcodes/tw47pl/validate'))
            ->willReturn(array("response" => array('code' => 501)));

        $client = new PostcodesAPI($http_transport);
        $error = $client->is_valid_postcode('tw47pl');
        $this->assertInstanceOf(\WP_Error::class, $error);
        $this->assertEquals('invalid', $error->get_error_code());
        $this->assertEquals(array("status_code" => 501), $error->get_error_data());
    }


    public function test_is_valid_postcode_with_empty_body()
    {
        $http_transport = $this->get_http_transport_mock();

        $http_transport->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('https://api.postcodes.io/postcodes/tw47pl/validate'))
            ->willReturn(array("response" => array('code' => 501)));

        $client = new PostcodesAPI($http_transport);
        $error = $client->is_valid_postcode('tw47pl');
        $this->assertInstanceOf(\WP_Error::class, $error);
        $this->assertEquals('invalid', $error->get_error_code());
        $this->assertEquals(array("status_code" => 501), $error->get_error_data());
    }



    public function get_http_transport_mock()
    {
        return  $this->getMockBuilder(\WP_Http::class)->getMock();
    }
}
