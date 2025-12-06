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
        if (! file_exists($this->filePath)) {
            throw new \InvalidArgumentException("File does not exist: {$this->filePath}");
        }

        if (! is_readable($this->filePath)) {
            throw new \InvalidArgumentException("File is not readable: {$this->filePath}");
        }

        // Check file size (limit to 100MB by default)
        $maxSize = 100 * 1024 * 1024; // 100MB in bytes
        $fileSize = filesize($this->filePath);

        if ($fileSize > $maxSize) {
            throw new \InvalidArgumentException("File size exceeds maximum allowed size of 100MB");
        }
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
