<?php

namespace BreakdownHotline\UltimateMember;


defined('ABSPATH') || exit;

//custom mobile number validation hook
add_action(
    'um_custom_field_validation_mobile_number',
    '\BreakdownHotline\UltimateMember\um_custom_validate_mobile_number',
    30,
    3
);
/**
 * Validate field Mobile Number
 * @param string $key
 * @param array  $array
 * @param array  $args
 * @since 1.0.0
 */
function um_custom_validate_mobile_number($key, $array, $args)
{
    //TODO add reg expression for uk mobile number validation

    $is_unique =  is_mobile_number_unique($key, $args[$key]);

    if (!$is_unique) {
        add_um_breakdown_form_error($key, "Mobile number is already associated with another account");
    }
}


/**
 * Queries user meta by key and value pairs
 *
 * @param string $key
 * @param string $value
 * @return array
 */
function query_user_meta_by_key_value($key, $value): array
{
    $query  = new \WP_User_Query(['meta_key' => $key, 'meta_value' => $value]);
    return $query->get_results();
}


/**
 * ensure submitted phone numbers are unique  
 * Ultimate member stores users' values in user_meta table as key/value pairs
 *
 * @param [String] $key  -  key stored in user_meta table
 * @param [String] $value - value stored in user_meta table
 * @return bool
 * @since 1.0.0
 */
function is_mobile_number_unique($key, $value): bool
{
    $user_meta_query = query_user_meta_by_key_value($key, $value);

    if (!empty($user_meta_query)) {
        return false;
    }

    return true;
}
