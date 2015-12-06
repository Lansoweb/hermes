<?php

namespace Demeter\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

final class GetAction extends AbstractAction
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $key = $request->getAttribute('key');

        if (!$this->storage->has($key)) {
            $response = new JsonResponse([
                'key' => $key,
                'message' => "Key not found.",
            ], 404);
        } else {
            $response = $this->getKeyResponse($key);
        }

        return $next($request, $response);
    }
}
