<?php

declare(strict_types=1);

namespace Maestroerror\PostizClient\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Delete Post
 *
 * Deletes an existing post
 */
class DeletePostRequest extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(protected string $postId)
    {
    }

    public function resolveEndpoint(): string
    {
        return '/posts/' . $this->postId;
    }
}
