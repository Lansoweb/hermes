<?php

namespace Hermes\Action;

use Hermes\Storage\StorageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Stratigility\MiddlewareInterface;

final class SetHeaderMiddleware implements MiddlewareInterface
{
    private $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $index = $this->storage->getIndex();

        return $response->withHeader('X-Hermes-Index', $index);
    }
}
