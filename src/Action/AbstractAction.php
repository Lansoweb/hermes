<?php

namespace Hermes\Action;

use Hermes\Storage\StorageInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Stratigility\MiddlewareInterface;

abstract class AbstractAction implements MiddlewareInterface
{
    protected $storage;

    protected $lastResponse;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    protected function getValues(ServerRequestInterface $request)
    {
        $contentType = $request->getHeader('Content-type');

        if (count($contentType) > 0 && strpos($contentType[0], 'application/json') !== false) {
            $value = json_decode($request->getBody()->getContents(), true);
        } else {
            $value = $request->getParsedBody();
        }

        return $value;
    }

    protected function sanitizeValue($value)
    {
        $sanitizedValue = [];

        if (array_key_exists('value', $value)) {
            $sanitizedValue['value'] = $value['value'];
        }

        if (array_key_exists('_embedded', $value)) {
            $sanitizedValue['_embedded'] = $value['_embedded'];
        }

        return $sanitizedValue;
    }

    protected function getLastResponse()
    {
        return $this->lastResponse;
    }

    protected function keyExists($key)
    {
        if ($this->storage->has($key)) {
            return true;
        }

        $this->lastResponse = new JsonResponse([
            'key' => $key,
            'message' => "Key not found.",
        ], 404);
        return false;
    }

    protected function getKeyResponse($key)
    {
        return new JsonResponse($this->storage->get($key));
    }
}
