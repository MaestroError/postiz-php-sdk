<?php

declare(strict_types=1);

namespace Maestroerror\PostizClient\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Get Integrations
 *
 * Retrieves a list of all integrations for the authenticated user
 */
class GetIntegrationsRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/integrations';
    }
}
