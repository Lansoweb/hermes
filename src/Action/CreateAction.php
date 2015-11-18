<?php

namespace Hermes\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

final class CreateAction extends AbstractAction
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $key = $request->getAttribute('key');

        $value = $this->getValues($request);

        if (empty($value)) {
            return $next($request, new JsonResponse([
                'key' => $key,
                'message' => "No value provided.",
            ], 406));
        }

        if ($this->storage->has($key)) {
            return $next($request, new JsonResponse([
                'key' => $key,
                'message' => "Key already exists.",
            ], 409));
        }
        $sanitizedValue = $this->sanitizeValue($value);

        $data = $this->storage->set($key, $sanitizedValue);

        return $next($request, new JsonResponse($data));
    }
}
