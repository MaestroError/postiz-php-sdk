<?php

declare(strict_types=1);

namespace Maestroerror\PostizClient\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Data\MultipartValue;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasMultipartBody;

/**
 * Upload File
 *
 * Uploads a media file
 */
class UploadFileRequest extends Request implements HasBody
{
    use HasMultipartBody;

    protected Method $method = Method::POST;

    public function __construct(protected string $filePath)
    {
    }

    public function resolveEndpoint(): string
    {
        return '/upload';
    }

    protected function defaultBody(): array
    {
        return [
            new MultipartValue('file', file_get_contents($this->filePath), basename($this->filePath)),
        ];
    }
}
