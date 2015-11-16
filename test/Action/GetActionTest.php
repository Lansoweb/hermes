<?php

namespace AppTest\Action;

use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;
use Hermes\Action\GetAction;
use Hermes\Storage\StorageInterface;

class GetActionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->storage = $this->prophesize(StorageInterface::class);
    }

    public function testKeyNotFound()
    {
        $action = new GetAction($this->storage->reveal());
        $response = $action(new ServerRequest(['/key/notfound']), new Response(), function ($request, $response) {
            return $response;
        });
        $json = json_decode((string) $response->getBody());

        $this->assertTrue($response instanceof Response\JsonResponse);
        $this->assertSame(404, $response->getStatusCode());
        $this->assertTrue(isset($json->message));
    }

    public function testKeyResponse()
    {
        $this->storage->has('found')->willReturn(true);
        $this->storage->get('found')->willReturn(["key"=>"found","value"=>123]);
        $action = new GetAction($this->storage->reveal());

        $request = new ServerRequest([],[],'/key/found');
        $request = $request->withAttribute('key', 'found');
        $response = $action($request, new Response(), function ($request, $response) {
            return $response;
        });
        $json = json_decode((string) $response->getBody());

        $this->assertTrue($response instanceof Response\JsonResponse);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue(isset($json->key));
        $this->assertTrue(isset($json->value));
        $this->assertSame('found', $json->key);
        $this->assertSame(123, $json->value);
    }

}
