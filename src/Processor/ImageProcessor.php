<?php

declare(strict_types=1);

namespace StefanBauer\LaravelFaviconExtractor\Processor;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageProcessor implements ImageProcessorInterface
{
    private $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    public function convertToWebP(string $imageContent, int $size, int $quality = 85): string
    {
        $image = $this->manager->read($imageContent);
        
        // Resize maintaining aspect ratio, then pad to exact square dimensions
        $image->contain($size, $size);
        
        return $image->toWebp($quality)->toString();
    }
}