<?php

declare(strict_types=1);

namespace StefanBauer\LaravelFaviconExtractor;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use StefanBauer\LaravelFaviconExtractor\Exception\FaviconCouldNotBeSavedException;
use StefanBauer\LaravelFaviconExtractor\Exception\InvalidUrlException;
use StefanBauer\LaravelFaviconExtractor\Favicon\FaviconFactoryInterface;
use StefanBauer\LaravelFaviconExtractor\Favicon\FaviconInterface;
use StefanBauer\LaravelFaviconExtractor\Generator\FilenameGeneratorInterface;
use StefanBauer\LaravelFaviconExtractor\Provider\ProviderInterface;
use StefanBauer\LaravelFaviconExtractor\Processor\ImageProcessorInterface;

class FaviconExtractor implements FaviconExtractorInterface
{
    private $faviconFactory;
    private $provider;
    private $filenameGenerator;
    private $imageProcessor;
    private $url;
    private $size;
    private $favicon;

    public function __construct(FaviconFactoryInterface $faviconFactory, ProviderInterface $provider, FilenameGeneratorInterface $filenameGenerator, ImageProcessorInterface $imageProcessor)
    {
        $this->provider = $provider;
        $this->faviconFactory = $faviconFactory;
        $this->filenameGenerator = $filenameGenerator;
        $this->imageProcessor = $imageProcessor;
    }

    public function fromUrl(string $url, int $size = 128): FaviconExtractorInterface
    {
        $this->url = $url;
        $this->size = $size;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function fetchOnly(): FaviconInterface
    {
        $rawContent = $this->provider->fetchFromUrl($this->getUrl(), $this->size);
        $processedContent = $this->imageProcessor->convertToWebP($rawContent, $this->size, config('favicon-extractor.webp_quality', 85));
        
        $this->favicon = $this->faviconFactory->create($processedContent);

        return $this->favicon;
    }

    public function fetchAndSaveTo(string $path, string $filename = null): string
    {
        if (null === $filename) {
            $filename = $this->filenameGenerator->generate(16);
        }

        $favicon = $this->fetchOnly();
        $targetPath = $this->getTargetPath($path, $filename);

        if (!Storage::put($targetPath, $favicon->getContent())) {
            throw new FaviconCouldNotBeSavedException(sprintf(
                'The favicon of %s could not be saved at path "%s" ',
                $this->getUrl(), $targetPath
            ));
        }

        return Str::replaceFirst('public/', '', $targetPath);
    }

    private function getTargetPath(string $path, string $filename): string
    {
        return $path.DIRECTORY_SEPARATOR.$filename.'.webp';
    }
}
