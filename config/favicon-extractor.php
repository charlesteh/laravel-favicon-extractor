<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Favicon Provider
    |--------------------------------------------------------------------------
    |
    | This class is used for fetching favicons. You can swap it out easily if
    | you like as long as you implement the ProviderInterface.
    |
    | \StefanBauer\LaravelFaviconExtractor\Provider\ProviderInterface
    |
    */

    'provider_class' => \StefanBauer\LaravelFaviconExtractor\Provider\GoogleProvider::class,

    /*
    |--------------------------------------------------------------------------
    | Filename Generator
    |--------------------------------------------------------------------------
    |
    | If you don't specify a custom filename on saving the downloaded favicon
    | to your storage, this package generates a random string for it. You
    | can override this behaviour at any time by using a custom
    | implementation which implements the following interface.
    |
    | \StefanBauer\LaravelFaviconExtractor\Generator\FilenameGeneratorInterface
    |
    */
    'filename_generator_class' => \StefanBauer\LaravelFaviconExtractor\Generator\FilenameGenerator::class,

    /*
    |--------------------------------------------------------------------------
    | Image Processor
    |--------------------------------------------------------------------------
    |
    | This class is used for processing images (resizing, format conversion).
    | You can swap it out easily if you like as long as you implement the
    | ImageProcessorInterface.
    |
    | \StefanBauer\LaravelFaviconExtractor\Processor\ImageProcessorInterface
    |
    */
    'image_processor_class' => \StefanBauer\LaravelFaviconExtractor\Processor\ImageProcessor::class,

    /*
    |--------------------------------------------------------------------------
    | WebP Quality
    |--------------------------------------------------------------------------
    |
    | The quality setting for WebP conversion. This value should be between
    | 0 and 100, where 100 is the highest quality and largest file size.
    | The default value of 85 provides a good balance between quality and
    | file size.
    |
    */
    'webp_quality' => 85,
];
