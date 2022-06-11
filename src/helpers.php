<?php

/**
 * Helper functions 
 */

namespace BreakdownHotline\Helpers;

// defined('ABSPATH') || exit;


/**
 * Queries user meta by key and value pairs
 *
 * @param string $key
 * @param string $value
 * @return array
 */
function queryUserByMetaKeyValue($key, $value)
{
    $query  = new \WP_User_Query(['meta_key' => $key, 'meta_value' => $value]);
    return $query->get_results();
}
