<?php

declare(strict_types=1);

namespace Maestroerror\PostizClient\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Upload from URL
 *
 * Uploads a file from an existing URL
 */
class UploadFromUrlRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected string $url)
    {
    }

    public function resolveEndpoint(): string
    {
        return '/upload-from-url';
    }

    protected function defaultBody(): array
    {
        return [
            'url' => $this->url,
        ];
    }
}
