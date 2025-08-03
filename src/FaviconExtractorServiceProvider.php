<?php

declare(strict_types=1);

namespace StefanBauer\LaravelFaviconExtractor;

use Illuminate\Support\ServiceProvider;
use StefanBauer\LaravelFaviconExtractor\Favicon\FaviconFactory;
use StefanBauer\LaravelFaviconExtractor\Favicon\FaviconFactoryInterface;
use StefanBauer\LaravelFaviconExtractor\Generator\FilenameGeneratorInterface;
use StefanBauer\LaravelFaviconExtractor\Provider\ProviderInterface;
use StefanBauer\LaravelFaviconExtractor\Processor\ImageProcessorInterface;

class FaviconExtractorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/favicon-extractor.php' => config_path('favicon-extractor.php')
        ], 'config');

        $this->app->bind(
            FaviconFactoryInterface::class,
            FaviconFactory::class
        );

        $this->app->bind(
            ProviderInterface::class,
            config('favicon-extractor.provider_class')
        );

        $this->app->bind(
            FilenameGeneratorInterface::class,
            config('favicon-extractor.filename_generator_class')
        );

        $this->app->bind(
            ImageProcessorInterface::class,
            config('favicon-extractor.image_processor_class')
        );

        $this->app->bind(
            FaviconExtractorInterface::class,
            FaviconExtractor::class
        );

        $this->app->alias(FaviconExtractorInterface::class, 'favicon.extractor');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/favicon-extractor.php', 'favicon-extractor');
    }
}
