<?php

declare(strict_types=1);

namespace StefanBauer\LaravelFaviconExtractor\Provider;

use GrahamCampbell\GuzzleFactory\GuzzleFactory;

class GoogleProvider implements ProviderInterface
{
    public function fetchFromUrl(string $url, ?int $size = null): string
    {
        $client = GuzzleFactory::make();
        $response = $client->get($this->getUrl($url, $size));

        return $response->getBody()->getContents();
    }

    private function getUrl(string $url, ?int $size = null): string
    {
        $baseUrl = 'https://www.google.com/s2/favicons?domain='.urlencode($url);
        
        if ($size !== null) {
            $baseUrl .= '&sz='.$size;
        }
        
        return $baseUrl;
    }
}
