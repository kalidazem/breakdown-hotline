<?php


// The reason for using the functions below is that the Ultimate Member plugin uses function_exists
// to check fo custom call_backs

/**
 * TODO write borough into their own json file as current file is quite large.
 * @return void
 */
function get_boroughs()
{
    $string = file_get_contents(PLUGIN_DIR . "/boroughs.json");
    $json = json_decode($string, true);

    $json_iterator = new RecursiveIteratorIterator(
        new RecursiveArrayIterator($json),
        RecursiveIteratorIterator::SELF_FIRST
    );

    $boroughs = [];
    $helper = null;
    foreach ($json_iterator as $key => $value) {
        if (!is_array($value)) {
            //get borough code 
            if ($key === "HASC_2") {
                $helper = $value;
            }

            if (!is_null($helper) && $key === "NAME_2") {
                $boroughs[$helper] = $value;
                $helper = null;
            }
            //get borough name 
        }
    }
    return $boroughs;
}
