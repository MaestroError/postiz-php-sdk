<?php

declare(strict_types=1);

namespace Maestroerror\PostizClient\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Generate Video
 *
 * Creates AI-generated videos for posts
 */
class GenerateVideoRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected array $data)
    {
    }

    public function resolveEndpoint(): string
    {
        return '/generate-video';
    }

    protected function defaultBody(): array
    {
        return $this->data;
    }
}
