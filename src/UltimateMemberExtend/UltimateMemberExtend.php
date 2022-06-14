<?php

/**
 * Customizes the functionalities provided by the Ultimate Member Plugin. 
 * Adds custom form validations as per requirements (e.g. mobile number should be unique).
 * 
 * @package BreakdownHotline\UltimateMember
 * TODO: refactor and group related-components together.  
 */

namespace BreakdownHotline\UltimateMemberExtend;

use BreakdownHotline\API\PostcodesAPI;

class UltimateMemberExtend
{

    /**
     * Instance of this class   
     *
     * @var self
     */
    private static $instance = null;

    /**
     * Postcodes API
     *
     * @var PostcodesAPI
     */
    private $postcodes_api_client;

    public function __construct(PostcodesAPI $postcodes_api_client)
    {
        $this->postcodes_api_client = $postcodes_api_client;
        $this->setupFilters();
        $this->setupActions();
    }

    public function setupActions()
    {
        add_action(
            'um_custom_field_validation_mobile_number',
            [$this, 'umMobileNumberCustomValidation'],
            30,
            3
        );
    }


    public function setupFilters()
    {
        add_filter(
            'um_prepare_user_query_args',
            [$this, 'um_prepare_user_lookup_custom_query'],
            99,
            2
        );
    }

    // public static function getInstance()
    // {
    //     if (is_null(self::$instance)) {
    //         self::$instance = new self();
    //     }

    //     return self::$instance;
    // }


    /**
     * This function is a handler that delegates other methods to achieve below logic.
     * 
     * This custom query allows looking up drivers based on the postcode of the job.
     * As Ultimate Member does not provide this feature by default.
     * 
     * How it works? 
     * 
     * 1- This functions receives a postcode.
     * 
     * 2- Checks validity of postcode by making to a third-part api via GET request.
     * 
     * 3- Checks which borough this postcode belongs to - makes GET request to the same api.
     * 
     * 4- Looks up the borough code associated with the borough name received - boroughs.json.
     * 
     * 5- Passes the borough code to the $queryArgs, so Ultimate Member can look up 
     * the drivers that work in that borough.
     * 
     * @param array $queryArgs
     * @param array $args
     * @since 1.0.1
     * @return array
     */
    public function um_prepare_user_lookup_custom_query($queryArgs, $args): array
    {
        if (!$_POST['search']) {
            return $queryArgs;
        }

        $postcode = $_POST['search'];

        //Don't proceed
        //Probably is not a postcode-based search
        if (!$this->postcodes_api_client->is_valid_postcode($postcode)) {
            return $queryArgs;
        }

        $boroughName = $this->postcodes_api_client->get_borough_name_by_postcode($postcode);

        $listOfBoroughs = $this->getBorough();

        // ["GB.HU" => "Hounslow"] the index is the borough code.
        $boroughCode = $this->get_borough_code_by_name($listOfBoroughs, $boroughName);

        //TODO: form_id shouldn't be hard-coded 
        if ($args["form_id"] == "285") {
            $queryArgs = $this->add_borough_to_search_query($boroughCode, $queryArgs);
        }
        return $queryArgs;
    }


    /**
     * Adds the the borough to the search query so users with matching borough code can be retrieved.
     *
     * @param string $boroughCode
     * @param array $queryArgs
     * @since 1.0.1
     * @return array
     */
    function add_borough_to_search_query(string $boroughCode, array $queryArgs): array
    {
        $queryArgs['meta_query'] = array(
            "relation" => "OR",
            array(
                'key' => 'select_borough',
                'value' =>  $boroughCode,
                'compare' => '='
            ),
            array(
                'key' => 'select_borough',
                'value' =>  $boroughCode,
                'compare' => 'LIKE'
            )
        );
        return $queryArgs;
    }


    private function getBorough()
    {
        return get_boroughs();
    }

    /** 
     * Get a borough id by its name: ["GB.HU"=>"Hounslow"]
     * 
     * @param array $listOfBoroughs
     * @param string $boroughName
     * @since 1.0.1
     * @return string
     *       
     */
    function get_borough_code_by_name(array $listOfBoroughs, string $boroughName): string
    {
        $boroughDetails = array_filter(
            $listOfBoroughs,
            function ($value) use ($boroughName) {
                if (strcmp(strtolower($boroughName), strtolower($value)) === 0) {
                    return true;
                }
            }
        );

        //get the key only, hence the [0]
        return array_keys($boroughDetails)[0];
    }

    /**
     * Check if provided postcode valid 
     * 
     * This function makes a call to
     * https://api.postcodes.io/postcodes/" . $postcode . " /validate
     * 
     * API returns: {"status": code, "results": true | false}
     *
     * @param string $postcode
     * @return boolean
     * @deprecated 1.0.0
     */
    public function isPostcodeValid($postcode): bool
    {
        // check validity of customer's collection $postcode
        $remote_data = wp_remote_get(
            "https://api.postcodes.io/postcodes/" . $postcode . " /validate"
        );

        //TODO try catch throw error!!?
        if (is_wp_error($remote_data) || wp_remote_retrieve_response_code($remote_data) !== 200) {
            return false;
        }


        //result = true | false
        return  json_decode($remote_data['body'])->result;
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
     * @deprecated 1.0.0
     * @param string $postcode
     * @return string|null  
     * @since 1.0.0
     */
    function getBoroughNameByPostcode(string $postcode)
    {

        //Look up the $borough in which the customer's collection $postcode exists
        //TODO 
        $remote_data = wp_remote_get(
            BOROUGH_VIA_POST_LOOKUP_ENDPOINT . "$postcode"
        );

        //check for errors
        //TODO what value should be returned?
        if (is_wp_error($remote_data) || wp_remote_retrieve_response_code($remote_data) !== 200) {
            return null;
        }

        $remote_data = json_decode($remote_data['body']);

        return  $remote_data->result->admin_district;
    }


    /** 
     * Get a borough id by its name: ["GB.HU"=>"Hounslow"]:
     * It is not case-sensitive: all strings passed will be lower-cased
     * @param string $borough_name
     * @since 1.0.1
     * @return array
     *       
     */
    function get_borough_id_by_name(string $borough_name, array $list_of_boroughs): array
    {
        if (count($list_of_boroughs) === 0) {
            return [];
        }

        return array_filter(
            $list_of_boroughs,
            function ($value) use ($borough_name) {
                if (strcmp(strtolower($borough_name), strtolower($value)) === 0) {
                    return true;
                }
            }
        );
    }


    /**
     * Validate field Mobile Number
     * @param string $key
     * @param array  $array
     * @param array  $args
     * @since 1.0.0
     * @return void
     */
    function umMobileNumberCustomValidation($key, $array, $args): void
    {
        //TODO add reg expression for uk mobile number validation

        $is_unique =  $this->mobileNumberExists($key, $args[$key]);

        if (!$is_unique) {

            $this->umAddFormError($key, "Mobile number is already associated with another account");
        }
    }

    /**
     * Wrapper function for UM original function that adds errors to forms
     *
     * @param string $field_key
     * @param string $error_message
     * @since 1.0.0
     * @return void
     */
    function umAddFormError($field_key, $error_message): void
    {
        UM()->form()->add_error($field_key, __($error_message, 'ultimate-member'));
    }

    /**
     * ensure submitted phone numbers are unique  
     * Ultimate member stores users' values in user_meta table as key/value pairs
     *
     * @param [String] $key  -  key stored in user_meta table
     * @param [String] $value - value stored in user_meta table
     * @since 1.0.0
     * @return bool
     */
    function mobileNumberExists($key, $value): bool
    {
        $user_meta_query = \BreakdownHotline\Helpers\queryUserByMetaKeyValue($key, $value);

        if (!empty($user_meta_query)) {
            return false;
        }

        return true;
    }
}
