<?php

namespace Hermes\Action;

use Zend\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Hermes\Storage\StorageInterface;
use Hermes\Exception\HermesException;

class PostAction
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

        $contentType = $request->getHeader('Content-type');

        if ($contentType && strpos($contentType[0], 'application/json') !== false) {
            $config = json_decode($request->getBody()->getContents(), true);
        } else {
            $config = $request->getParsedBody();
        }

        if (empty($config)) {
            return new JsonResponse([
                'service' => $service,
                'version' => $version,
                'message' => "Invalid data provided.",
            ], 400);
        }

        try {
            if (empty($version)) {
                $version = $this->storage->getLatestVersion($service);
                ++$version;
            }
            $config = $this->storage->set($service, $config, $version);
        } catch (HermesException $ex) {
            return new JsonResponse([
                'service' => $service,
                'version' => $version,
                'message' => $ex->getMessage(),
            ], 404);
        }

        return new JsonResponse([
            'service' => $service,
            'version' => $version,
            'config' => $config,
        ]);
    }
}
