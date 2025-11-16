<?php

use App\Helpers\QrCodeHelper;

if (!function_exists('qr_code')) {
    function qr_code(string $data, int $size = 300): string
    {
        return QrCodeHelper::generate($data, $size);
    }
}

if (!function_exists('qr_code_html')) {
    function qr_code_html(string $data, int $size = 300, string $class = ''): string
    {
        return QrCodeHelper::html($data, $size, $class);
    }
}

if (!function_exists('qr_code_svg')) {
    function qr_code_svg(string $data, int $size = 300): string
    {
        return QrCodeHelper::svg($data, $size);
    }
}