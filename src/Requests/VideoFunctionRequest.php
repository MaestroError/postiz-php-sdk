<?php

declare(strict_types=1);

namespace Maestroerror\PostizClient\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Video Function
 *
 * Execute video-related functions like loading available voices
 */
class VideoFunctionRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected array $data)
    {
    }

    public function resolveEndpoint(): string
    {
        return '/video/function';
    }

    protected function defaultBody(): array
    {
        return $this->data;
    }
}
