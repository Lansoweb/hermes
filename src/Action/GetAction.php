<?php

namespace Hermes\Action;

use Zend\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Hermes\Storage\StorageInterface;
use Hermes\Exception\HermesException;

class GetAction
{
    protected $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $service = $request->getAttribute('service');
        $version = $request->getAttribute('version');

        try {
            if (empty($version)) {
                $version = $this->storage->getLatestVersion($service);
            }
            $config = $this->storage->get($service, $version);
        } catch (HermesException $ex) {
            return new JsonResponse([
                'service' => $service,
                'version' => (int)$version,
                'message' => $ex->getMessage(),
            ], 404);
        }

        return new JsonResponse([
            'service' => $service,
            'version' => (int)$version,
            'config' => json_decode($config, true),
        ]);
    }
}
