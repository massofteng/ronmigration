<?php

if (!function_exists('dd')) {
    /**
     * Dump the variable and end script execution.
     *
     * @param mixed ...$vars
     */
    function dd(...$vars)
    {
        foreach ($vars as $var) {
            echo '<pre>';
            var_dump($var);
            echo '</pre>';
        }
        die();
    }
}
