<?php

namespace BreakdownHotline\API;

class PostcodesAPI
{

    const ENDPOINT_BASE = "https://api.postcodes.io/postcodes";

    /**
     * The Wordpress HTTP transport.         
     *
     * @var \WP_Http
     */
    private $http_transport;

    /**
     * Constructor
     *
     * @param \WP_Http $http_transport
     */
    public function __construct(\WP_Http $http_transport)
    {
        $this->http_transport = $http_transport;
    }

    /**
     * Checks with Postcodes API if the given postcode is valid. 
     * 
     * This function makes a call to
     * https://api.postcodes.io/postcodes/" . $postcode . " /validate
     * 
     * API returns: {"status": code, "results": true | false}
     * 
     * @param string $postcode
     * @return bool|\WP_Error
     * @since 1.0.1
     */
    public function is_valid_postcode($postcode)
    {
        $response = $this->http_transport->get($this::ENDPOINT_BASE . "/$postcode/" . "validate");

        if ($response instanceof \WP_Error) {
            return $response;
        }


        $response_status_code = $this->get_response_status_code($response);

        if ($response_status_code === null) {
            return new \WP_Error('no_status_code');
        } elseif ($response_status_code != 200) {
            return new \WP_Error('invalid', 'invalid postcode', array('status_code' => $response_status_code));
        }

        //either true or false
        return json_decode($response['body'])->result;
    }



    /**
     * Get Borough name from API 
     * 
     * This function makes a call to: 
     * https://api.postcodes.io/postcodes/$postcode
     * 
     * We want to know the administrative borough of a particular postcode.
     * 
     * API Response schema contains 
     *  - status: 200 | 301 
     *  - results: {
     *     - admin_county: value, ---> is not important
     *    
     *     - admin_district: value, ---> is what we are looking for
     *    
     *     - country: value, ---> not important 
     * - }
     * @param string $postcode
     * @return string|\WP_Error  
     * @since 1.0.1
     */
    public function get_borough_name_by_postcode($postcode)
    {
        $response = $this->http_transport->get($this::ENDPOINT_BASE . "/$postcode");

        if ($response instanceof \WP_Error) {
            return $response;
        }

        $response_status_code = $this->get_response_status_code($response);

        if ($response_status_code === null) {
            return new \WP_Error('no_status_code');
        } elseif ($response_status_code != 200) {
            return new \WP_Error(
                'invalid',
                'invalid postcode',
                array("status_code" => $response_status_code)
            );
        }

        $response = json_decode($response['body'])->result->admin_district;
        return  $response;
    }

    /**
     * Extracts the status code from a given response.
     *
     * @param array $response
     * @return int|null
     */
    private function get_response_status_code(array $response)
    {
        if (
            !isset($response['response']) ||
            !is_array($response['response']) ||
            !isset($response['response']['code'])
        ) {
            return;
        }

        return (int) $response['response']['code'];
    }
}
