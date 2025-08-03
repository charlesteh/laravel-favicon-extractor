<?php

declare(strict_types=1);

namespace StefanBauer\LaravelFaviconExtractor\Processor;

use PHPUnit\Framework\TestCase;

class ImageProcessorTest extends TestCase
{
    private $processor;

    protected function setUp(): void
    {
        $this->processor = new ImageProcessor();
        parent::setUp();
    }


    public function test_it_can_convert_to_webp()
    {
        // Create a simple 1x1 pixel red PNG for testing
        $originalImage = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/5+hHgAHggJ/PchI7wAAAABJRU5ErkJggg==');
        
        $webpImage = $this->processor->convertToWebP($originalImage, 64, 85);
        
        $this->assertIsString($webpImage);
        $this->assertNotEmpty($webpImage);
        
        // Verify it's a valid WebP
        $imageInfo = getimagesizefromstring($webpImage);
        $this->assertGreaterThanOrEqual(64, $imageInfo[0]); // width (may be padded)
        $this->assertGreaterThanOrEqual(64, $imageInfo[1]); // height (may be padded)
        $this->assertEquals(IMAGETYPE_WEBP, $imageInfo[2]);
    }

    public function test_it_converts_webp_with_custom_quality()
    {
        // Create a simple 1x1 pixel red PNG for testing
        $originalImage = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/5+hHgAHggJ/PchI7wAAAABJRU5ErkJggg==');
        
        $webpImage90 = $this->processor->convertToWebP($originalImage, 128, 90);
        $webpImage50 = $this->processor->convertToWebP($originalImage, 128, 50);
        
        $this->assertIsString($webpImage90);
        $this->assertIsString($webpImage50);
        
        // Both should be valid WebP images
        $this->assertNotEmpty($webpImage90);
        $this->assertNotEmpty($webpImage50);
    }
}