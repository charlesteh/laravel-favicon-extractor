<?php

declare(strict_types=1);

namespace StefanBauer\LaravelFaviconExtractor;

use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Orchestra\Testbench\TestCase;
use StefanBauer\LaravelFaviconExtractor\Exception\FaviconCouldNotBeSavedException;
use StefanBauer\LaravelFaviconExtractor\Exception\InvalidUrlException;
use StefanBauer\LaravelFaviconExtractor\Favicon\Favicon;
use StefanBauer\LaravelFaviconExtractor\Favicon\FaviconFactoryInterface;
use StefanBauer\LaravelFaviconExtractor\Generator\FilenameGeneratorInterface;
use StefanBauer\LaravelFaviconExtractor\Provider\ProviderInterface;
use StefanBauer\LaravelFaviconExtractor\Processor\ImageProcessorInterface;

class FaviconExtractorTest extends TestCase
{
    /**
     * @var FaviconFactoryInterface|MockInterface
     */
    private $faviconFactory;

    /**
     * @var ProviderInterface|MockInterface
     */
    private $provider;

    /**
     * @var FilenameGeneratorInterface|MockInterface
     */
    private $filenameGenerator;

    /**
     * @var ImageProcessorInterface|MockInterface
     */
    private $imageProcessor;

    /**
     * @var FaviconExtractor
     */
    private $extractor;

    protected function setUp(): void
    {
        $this->faviconFactory = \Mockery::mock(FaviconFactoryInterface::class);
        $this->provider = \Mockery::mock(ProviderInterface::class);
        $this->filenameGenerator = \Mockery::mock(FilenameGeneratorInterface::class);
        $this->imageProcessor = \Mockery::mock(ImageProcessorInterface::class);

        $this->extractor = new FaviconExtractor($this->faviconFactory, $this->provider, $this->filenameGenerator, $this->imageProcessor);

        parent::setUp();
    }

    public function test_it_fetches_the_favicon()
    {
        $expectedUrl = 'http://example.com';
        $rawContent = 'raw-content';
        $processedContent = 'processed-content';

        $this->provider
            ->shouldReceive('fetchFromUrl')
            ->once()
            ->with($expectedUrl, 128)
            ->andReturn($rawContent)
        ;

        $this->imageProcessor
            ->shouldReceive('convertToWebP')
            ->once()
            ->with($rawContent, 128, 85)
            ->andReturn($processedContent)
        ;

        $this->faviconFactory
            ->shouldReceive('create')
            ->once()
            ->with($processedContent)
        ;

        $this->extractor->fromUrl($expectedUrl)->fetchOnly();
    }

    public function test_it_generates_a_filename_if_none_given()
    {
        $this->provider->shouldIgnoreMissing();
        $this->imageProcessor->shouldIgnoreMissing();

        $expectedFavicon = new Favicon('content');

        $this->faviconFactory
            ->shouldReceive('create')
            ->withAnyArgs()
            ->andReturn($expectedFavicon)
        ;

        $this->filenameGenerator
            ->shouldReceive('generate')
            ->once()
            ->with(16)
            ->andReturn('random-filename')
        ;

        $this->extractor
            ->fromUrl('http://example.com')
            ->fetchAndSaveTo('some-path')
        ;
    }

    public function test_it_saves_it_properly()
    {
        $this->provider->shouldIgnoreMissing();
        $this->imageProcessor->shouldIgnoreMissing();

        $expectedFavicon = new Favicon('content');

        $this->faviconFactory
            ->shouldReceive('create')
            ->withAnyArgs()
            ->andReturn($expectedFavicon)
        ;

        Storage::fake();
        Storage::
            shouldReceive('put')
            ->once()
            ->with('some-path/a-filename.webp', 'content')
            ->andReturn(true)
        ;

        $this->extractor
            ->fromUrl('http://example.com')
            ->fetchAndSaveTo('some-path', 'a-filename')
        ;
    }

    public function test_it_throws_an_exception_when_saving_was_not_successful()
    {
        $this->provider->shouldIgnoreMissing();
        $this->imageProcessor->shouldIgnoreMissing();

        $expectedFavicon = new Favicon('content');

        $this->faviconFactory
            ->shouldReceive('create')
            ->withAnyArgs()
            ->andReturn($expectedFavicon)
        ;

        Storage::fake();
        Storage::
        shouldReceive('put')
            ->once()
            ->andReturn(false)
        ;

        $this->expectException(FaviconCouldNotBeSavedException::class);

        $this->extractor
            ->fromUrl('http://example.com')
            ->fetchAndSaveTo('some-path', 'a-filename')
        ;
    }

    public function test_it_can_fetch_with_custom_size()
    {
        $expectedUrl = 'http://example.com';
        $customSize = 64;
        $rawContent = 'raw-content';
        $processedContent = 'processed-content';

        $this->provider
            ->shouldReceive('fetchFromUrl')
            ->once()
            ->with($expectedUrl, $customSize)
            ->andReturn($rawContent)
        ;

        $this->imageProcessor
            ->shouldReceive('convertToWebP')
            ->once()
            ->with($rawContent, $customSize, 85)
            ->andReturn($processedContent)
        ;

        $this->faviconFactory
            ->shouldReceive('create')
            ->once()
            ->with($processedContent)
        ;

        $this->extractor->fromUrl($expectedUrl, $customSize)->fetchOnly();
    }

}
