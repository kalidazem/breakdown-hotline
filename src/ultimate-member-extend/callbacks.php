<?php

// namespace BreakdownHotline\UltimateMember;
// The reason for using the functions below is that the Ultimate Member plugin uses function_exists
// to check fo custom call_backs


defined('ABSPATH') || exit;

/**
 * TODO write borough into their own json file as current file is quite large.
 * TODO: change return type
 * @return void
 */
function get_boroughs()
{
    $string = file_get_contents(PLUGIN_DIR . "/boroughs.json");
    $json = json_decode($string, true);

    $json_iterator = new \RecursiveIteratorIterator(
        new \RecursiveArrayIterator($json),
        \RecursiveIteratorIterator::SELF_FIRST
    );

    $boroughs = [];
    $borough = null;
    foreach ($json_iterator as $key => $value) {
        if (!is_array($value)) {
            //get borough name
            if (is_null($borough) && $key === "NAME_2") {
                //borough name
                $borough = $value;
            }

            //get borough code 
            if (!is_null($borough) && $key === "HASC_2") {
                $boroughs[$value] = $borough;
                $borough = null;
            }
        }
    }

    return $boroughs;
}
