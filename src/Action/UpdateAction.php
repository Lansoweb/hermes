<?php

namespace Demeter\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

final class UpdateAction extends AbstractAction
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $key = $request->getAttribute('key');

        if (!$this->storage->has($key)) {
            return $next($request, new JsonResponse([
                'key' => $key,
                'message' => "Key not found.",
            ], 404));
        }

        $value = $this->getValues($request);

        if (empty($value)) {
            return $next($request, new JsonResponse([
                'key' => $key,
                'message' => "No value provided.",
            ], 406));
        }

        $sanitizedValue = $this->sanitizeValue($value);
        if ($request->getMethod() == 'PUT') {
            $data = $this->storage->set($key, $sanitizedValue);
        } elseif ($request->getMethod() == 'PATCH') {
            $data = $this->storage->get($key);
            $mergedData = array_merge($data, $sanitizedValue);
            $data = $this->storage->set($key, $mergedData);
        }

        return $next($request, new JsonResponse($data));
    }
}
