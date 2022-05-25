<?php

namespace BreakdownHotline\UltimateMember;

defined('ABSPATH') || exit;

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


//check validity of customer's collection $postcode
add_action('um_members_directory_search', 'check_validity_of_customer_postcode', 10, 1);
function check_validity_of_customer_postcode($args)
{
    return $args;
}
//Look up the $borough in which the customer's collection $postcode exists
//Look up the drivers that operate within $borough

// add_filter('um_prepare_user_query_args', 'BreakdownHotline\UltimateMember\um_my_custom_query_args', 99, 2);
function um_my_custom_query_args($query_args, $args)
{
    var_dump($query_args);
    if ($args["form_id"] == "1") {  // you can validate  the current member directory form ID

        $query_args['meta_query'][] = array(
            "relation" => "OR",
            array(
                'key' => 'job_title',
                'value' => serialize('WP Plugin developer'),
                'compare' => 'LIKE'
            ),
            array(
                'key' => 'job_title',
                'value' => 'WP Plugin developer',
                'compare' =>  '='
            )
        );
    } // endif

    return $query_args;
}








// <!-- // add_action('um_members_directory_footer', 'BreakdownHotline\UltimateMember\my_members_directory_footer', 10, 1);
// // function my_members_directory_footer($args)
// // {

// // var_dump($args);
// // } -->