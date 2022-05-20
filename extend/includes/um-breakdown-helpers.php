<?php
namespace BreakdownHotline\Helpers;
/**
 * Functions to interact with Ultimate Member plugins should live here.
 */


if (!function_exists('add_um_breakdown_form_error')) {

    /**
     * Adds custom error message to Ultimate member forms 
     *
     * @param [string] $field_key - meta_key in user_meta table
     * @param [string] $error_message - message to be displayed to on form
     * @return void
     */
    function add_um_breakdown_form_error($field_key, $error_message)
    {
        UM()->form()->add_error($field_key, __($error_message, 'ultimate-member'));
    }
}
