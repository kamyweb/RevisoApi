<?php

namespace Weble\RevisoApi\Endpoint;

use Illuminate\Support\Collection;
use Psr\Http\Client\ClientExceptionInterface;
use Weble\RevisoApi\Client;
use Weble\RevisoApi\Exceptions\ErrorResponseException;

class ProductsEndpoint extends Endpoint
{
    public function getResourceKey(): string|int|null
    {
        return 'productNumber';
    }
}
