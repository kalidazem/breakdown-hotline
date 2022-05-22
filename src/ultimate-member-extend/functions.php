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
