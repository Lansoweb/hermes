<?php

namespace AppTest\Action;

use Hermes\Action\DeleteAction;
use Hermes\Storage\StorageInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class DeleteActionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->storage = $this->prophesize(StorageInterface::class);
    }

    public function testKeyNotFound()
    {
        $this->storage->has('notfound')->willReturn(false);
        $action = new DeleteAction($this->storage->reveal());

        $request = new ServerRequest([], [], '/key/notfound', 'DELETE');
        $request = $request->withAttribute('key', 'notfound');
        $response = $action($request, new Response(), function ($request, $response) {
            return $response;
        });
        $json = json_decode((string) $response->getBody());

        $this->assertTrue($response instanceof Response\JsonResponse);
        $this->assertSame(404, $response->getStatusCode());
        $this->assertTrue(isset($json->message));
    }

    public function testDeleteKey()
    {
        $this->storage->has('found')->willReturn(true);
        $this->storage->delete('found')->willReturn(true);
        $action = new DeleteAction($this->storage->reveal());

        $request = new ServerRequest([], [], '/key/found', 'DELETE');
        $request = $request->withAttribute('key', 'found');
        $response = $action($request, new Response(), function ($request, $response) {
            return $response;
        });
        $json = json_decode((string) $response->getBody());

        $this->assertTrue($response instanceof Response\JsonResponse);
        $this->assertSame(204, $response->getStatusCode());
        $this->assertEmpty($json);
    }
}
