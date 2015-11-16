<?php
namespace Hermes\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

final class DeleteAction extends AbstractAction
{

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $key = $request->getAttribute('key');

        if (! $this->keyExists($key)) {
            return $this->lastResponse;
        }

        $this->storage->delete($key);

        return $next($request, new JsonResponse([], 204));
    }
}
