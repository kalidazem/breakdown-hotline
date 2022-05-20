<?php

namespace BreakdownHotline\Controllers;


class Ultimate_Member_Extend_Controller
{
    private static $instance = null;

    private function __construct()
    {
    }

    public function register()
    {
        $this->setup_um_actions();
    }


    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    public  function setup_um_actions()
    {
        add_action('um_custom_field_validation_mobile_number', [
            $this,
            'um_custom_validate_mobile_number'
        ], 30, 3);
    }

    /**
     * Validate field Mobile Number
     * @param string $key
     * @param array  $array
     * @param array  $args
     * @since 1.0.0
     */
    public function um_custom_validate_mobile_number($key, $array, $args)
    {
        //TODO add reg expression for uk mobile number validation

        $is_unique =  $this->is_mobile_number_unique($key, $args[$key]);

        if (!$is_unique) {
            $this->add_um_breakdown_form_error($key, "Mobile number is already associated with another account");
        }
    }

    /**
     *
     * @param [type] $field_key
     * @param [type] $error_message
     * @return void
     */
    function add_um_breakdown_form_error($field_key, $error_message)
    {
        UM()->form()->add_error($field_key, __($error_message, 'ultimate-member'));
    }



    /********************************DB RELATED FUNCTIONS START ************************************* */

    /**
     * Queries user meta by key and value pairs
     *
     * @param string $key
     * @param string $value
     * @return array
     */
    private function query_user_meta_by_key_value($key, $value): array
    {
        $query  = new \WP_User_Query(['meta_key' => $key, 'meta_value' => $value]);
        return $query->get_results();
    }
    /********************************DB RELATED FUNCTIONS END ************************************* */




    /********************************BUSINESS-LOGIC FUNCTIONS START ************************************* */
    /**
     * ensure submitted phone numbers are unique  
     * Ultimate member stores users' values in user_meta table as key/value pairs
     *
     * @param [String] $key  -  key stored in user_meta table
     * @param [String] $value - value stored in user_meta table
     * @return bool
     * @since 1.0.0
     */
    private function is_mobile_number_unique($key, $value): bool
    {
        $user_meta_query = $this->query_user_meta_by_key_value($key, $value);

        if (!empty($user_meta_query)) {
            return false;
        }

        return true;
    }



    /********************************BUSINESS-LOGIC FUNCTIONS END ************************************* */

    //Business-logic related functions - should not be dependent on any other piece of code 
    //Other pieces of code should be dependent of business login to perform desired actions correctly
}
