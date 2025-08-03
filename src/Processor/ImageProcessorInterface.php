<?php

declare(strict_types=1);

namespace StefanBauer\LaravelFaviconExtractor\Processor;

interface ImageProcessorInterface
{
    public function convertToWebP(string $imageContent, int $size, int $quality = 85): string;
}