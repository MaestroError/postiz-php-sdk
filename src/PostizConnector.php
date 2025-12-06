<?php

declare(strict_types=1);

namespace Maestroerror\PostizClient;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

/**
 * Postiz API Connector
 *
 * Main connector for the Postiz API
 */
class PostizConnector extends Connector
{
    use AcceptsJson;

    public function __construct(
        protected string $apiKey,
        protected string $baseUrl = 'https://postiz.com/api'
    ) {
    }

    /**
     * Define the base URL for the API
     */
    public function resolveBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Default headers for all requests
     */
    protected function defaultHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Default configuration for all requests
     */
    protected function defaultConfig(): array
    {
        return [
            'timeout' => 30,
        ];
    }
}
