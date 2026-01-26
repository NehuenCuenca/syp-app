<?php

function customFormatNumber(int $number): string {
    return number_format($number, 0, ',', '.');
}

if (!function_exists('custom_format_number')) {
    /**
     * Format a number to use a custom string format.
     *
     * @param int $number
     * @return string
     */
    function custom_format_number(int $number): string {
        return customFormatNumber($number);
    }
}

if (!function_exists('format_number_to_currency')) {
    /**
     * Format a number into a currency format.
     *
     * @param int $number
     * @return string
     */
    function format_number_to_currency(int $number): string {
        return "$" . customFormatNumber($number);
    }
}
