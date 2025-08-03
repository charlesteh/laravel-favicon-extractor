# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## About This Package

This is a Laravel package that extracts favicons from websites using various providers (Google, DuckDuckGo, GitHub). It's a fork of stefanbauer/laravel-favicon-extractor with support for modern Laravel & PHP versions.

## Requirements

- PHP 8.2+
- Laravel 10.0+
- GD extension (for image processing)

## Development Commands

### Testing
```bash
vendor/bin/phpunit
```

### Code Style (if available)
```bash
vendor/bin/php-cs-fixer fix
```

## Architecture Overview

### Core Components

**FaviconExtractor** - Main service class that orchestrates favicon extraction and saving
- Implements fluent interface pattern: `fromUrl()->fetchOnly()` or `fromUrl()->fetchAndSaveTo()`
- Handles both fetch-only and fetch-and-save operations
- Uses dependency injection for providers, factories, and filename generators

**Provider System** - Pluggable favicon fetching backends
- `GoogleProvider` - Uses Google's favicon service (default)
- `DuckDuckGoProvider` - Alternative provider using DuckDuckGo
- `GitHubProvider` - Specialized for GitHub repositories
- All implement `ProviderInterface` for easy swapping

**Favicon Factory & Models**
- `FaviconFactory` creates `Favicon` instances from raw content
- `Favicon` model wraps favicon content and provides access methods
- Implements `FaviconInterface` for consistent API

**Filename Generation**
- `FilenameGenerator` creates random 16-character filenames when none provided
- Implements `FilenameGeneratorInterface` for custom implementations

### Configuration

The package uses `config/favicon-extractor.php` with two configurable classes:
- `provider_class` - Which favicon provider to use (default: GoogleProvider)
- `filename_generator_class` - How to generate random filenames

### Service Provider Registration

`FaviconExtractorServiceProvider` handles:
- Config publishing and merging
- Interface-to-implementation bindings via Laravel's service container
- All classes are properly bound to their interfaces for dependency injection

### Laravel Integration

- Auto-discovery enabled for Laravel 5.5+
- Facade available: `FaviconExtractor`
- Uses Laravel's Storage facade for file operations
- Integrates with Laravel's filesystem configuration

## New Features (v2.0+)

### Size Parameter Support
- `fromUrl()` now accepts optional `size` parameter (default: 128)
- GoogleProvider uses `&sz={size}` for optimized favicon fetching
- All images automatically resized to exact dimensions while maintaining aspect ratio

### WebP Default Output
- All favicon methods now output WebP format by default
- Configurable WebP quality (default: 85)
- Better compression with modern format support
- Uses Intervention Image v3 for processing

### Image Processing
- **ImageProcessor** service handles resizing and format conversion
- Proportional scaling with proper aspect ratio maintenance
- All output is WebP format for optimal compression

### Usage Examples

```php
// Size parameter with WebP output
FaviconExtractor::fromUrl('https://laravel.com', 64)->fetchOnly();
FaviconExtractor::fromUrl('https://laravel.com', 256)->fetchAndSaveTo('favicons');
```

### Breaking Changes (v2.0+)

**WebP as Default**: 
- `fetchOnly()` now returns WebP format instead of PNG
- `fetchAndSaveTo()` now saves as `.webp` files instead of `.png`
- All existing code will get WebP output instead of PNG

## Key Patterns

1. **Interface Segregation** - Each component has a focused interface
2. **Dependency Injection** - All dependencies injected via constructor
3. **Strategy Pattern** - Swappable providers for different favicon sources
4. **Fluent Interface** - Chainable method calls for better developer experience
5. **Exception Handling** - Custom exceptions for domain-specific errors
6. **Additive API Design** - New features added without breaking existing functionality

## Testing Approach

Uses Orchestra Testbench for Laravel package testing with:
- Mockery for mocking dependencies
- Storage facade mocking for file operations
- Comprehensive test coverage for both existing and new functionality
- Proper setup/teardown in base test class
- Image processing tests with real image data