<?php

namespace App\Helpers;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Color\Color;

class QrCodeHelper
{
    /**
     * Generate QR code as data URI for inline display (v6.0)
     */
    public static function generate(string $data, int $size = 300): string
    {
        try {
            // v6.0 uses constructor with named parameters, then build()
            $builder = new Builder(
                writer: new PngWriter(),
                data: $data,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::Medium,
                size: $size,
                margin: 10,
                roundBlockSizeMode: RoundBlockSizeMode::Margin,
                foregroundColor: new Color(0, 0, 0),
                backgroundColor: new Color(255, 255, 255)
            );
            
            $result = $builder->build();
            
            return $result->getDataUri();
        } catch (\Exception $e) {
            \Log::error('QR Helper generation failed: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Generate QR code as HTML img tag
     */
    public static function html(string $data, int $size = 300, string $class = ''): string
    {
        $dataUri = self::generate($data, $size);
        
        if (empty($dataUri)) {
            return '<div class="alert alert-danger">QR Code generation failed</div>';
        }
        
        return sprintf(
            '<img src="%s" alt="QR Code" class="%s" style="max-width: %dpx;">',
            $dataUri,
            $class,
            $size
        );
    }

    /**
     * Generate SVG QR code
     */
    public static function svg(string $data, int $size = 300): string
    {
        try {
            $builder = new Builder(
                writer: new SvgWriter(),
                data: $data,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::Medium,
                size: $size,
                margin: 10
            );
            
            $result = $builder->build();
            
            return $result->getString();
        } catch (\Exception $e) {
            \Log::error('SVG QR generation failed: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Save QR code to file
     */
    public static function saveToFile(string $data, string $filepath, int $size = 300): bool
    {
        try {
            $builder = new Builder(
                writer: new PngWriter(),
                data: $data,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::Medium,
                size: $size,
                margin: 10,
                roundBlockSizeMode: RoundBlockSizeMode::Margin,
                foregroundColor: new Color(0, 0, 0),
                backgroundColor: new Color(255, 255, 255)
            );
            
            $result = $builder->build();
            
            // Save to file
            file_put_contents($filepath, $result->getString());
            
            return file_exists($filepath);
        } catch (\Exception $e) {
            \Log::error('QR save to file failed: ' . $e->getMessage());
            return false;
        }
    }
}